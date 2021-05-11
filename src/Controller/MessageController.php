<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Repository\MessageRepository;
use App\Repository\ParticipantRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/messages', name: 'messages.')]
class MessageController extends AbstractController
{
    const ATTRIBUTES_TO_SERIALIZE = ['id', 'content', 'createdAt', 'mine'];
    private $entityManager;
    private $messageRepository;
    private $participantRepository;
    private $publisher;
    private $userRep;

    public function __construct(
        EntityManagerInterface $entityManager,
        MessageRepository $messageRepository,
        ParticipantRepository $participantRepository,
        PublisherInterface $publisher,
        UserRepository $userRep
    ) {
        $this->entityManager = $entityManager;
        $this->messageRepository = $messageRepository;
        $this->participantRepository = $participantRepository;
        $this->publisher = $publisher;
        $this->userRep = $userRep;
    }


    /**
     * messages for given conversation {id}
     */
    #[Route('/{id}', name: 'getMessages', methods: ['GET'])]

    public function index(Request $request, Conversation $conversation)
    {

        // can i view the conversation
        $this->denyAccessUnlessGranted('view', $conversation);

        $messages = $this->messageRepository->findMessagesByConversationId($conversation->getId());

        /**
         * @var   Message  $message  
         */
        foreach ($messages as $key => $message) {
            $message->setMine(
                $message->getUser()->getId() === $this->getUser()->getId() ? true : false
            );
        }

        
        // array_map(function ($message) {
        //     $message->setMine(
        //         $message->getUser()->getId() === $this->getUser()->getId() ? true : false
        //     );
        // }, $messages);

        return $this->json($messages, Response::HTTP_OK, [], [
            'attributes' => self::ATTRIBUTES_TO_SERIALIZE
        ]);
    }



    #[Route('/{id}', name: 'newMessage', methods: ['POST'])]
    public function newMessage(Request $request, Conversation $conversation, SerializerInterface $serializerInterface)
    {
        // $user = $this->userRep->findOneBy(['id' => 2,]);
        $user = $this->getUser();

        $recipient = $this->participantRepository->findParticipantByConversationIdAndUserid(
            $conversation->getId(),
            $user->getId()
        );

        //can i add message
        $this->denyAccessUnlessGranted('add', $conversation);

        $content = $request->get('content');


        $message = new Message();
        $message->setContent($content);
        $message->setUser($user);

        $conversation->addMessage($message);
        $conversation->setLastMessage($message);

        $this->entityManager->getConnection()->beginTransaction();

        try {
            $this->entityManager->persist($message);
            $this->entityManager->persist($conversation);
            $this->entityManager->flush();

            $this->entityManager->commit();
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            throw $e;
        }

        $message->setMine(false);
        $messageSerialized = $serializerInterface->serialize($message, 'json', [
            'attributes' => [...self::ATTRIBUTES_TO_SERIALIZE, 'conversation' => ['id']]
        ]);


        $update = new Update(
            [
                sprintf("/conversations/%s", $conversation->getId()),
                sprintf("/conversations/%s", $recipient->getUser()->getUsername()),
            ],
            $messageSerialized,
            true
        );

        $this->publisher->__invoke($update);

        $message->setMine(true);


        return $this->json($message, Response::HTTP_CREATED, [], [
            'attributes' => self::ATTRIBUTES_TO_SERIALIZE
        ]);
    }
}
