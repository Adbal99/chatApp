<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/messages', name: 'messages.')]
class MessageController extends AbstractController
{
    const ATTRIBUTES_TO_SERIALIZE = ['id', 'content', 'createdAt', 'mine'];
    private $entityManager;
    private $messageRepository;
    private $userRep;

    public function __construct(EntityManagerInterface $entityManager, MessageRepository $messageRepository, UserRepository $userRep)
    {
        $this->entityManager = $entityManager;
        $this->messageRepository = $messageRepository;
        $this->userRep = $userRep;
    }

    #[Route('/{id}', name: 'getMessages', methods: ['GET'])]

    public function index(Request $request, Conversation $conversation)
    {

        // can i view the conversation
        $this->denyAccessUnlessGranted('view', $conversation);

        $messages = $this->messageRepository->findMessagesByConversationId($conversation->getId());

        /**
         * @var   Message  $message  
         */
        array_map(function ($message) {
            $message->setMine(
                $message->getUser()->getId() === $this->getUser()->getId() ? true : false
            );
        }, $messages);

        return $this->json($messages, Response::HTTP_OK, [], [
            'attributes' => self::ATTRIBUTES_TO_SERIALIZE
        ]);
    }



    #[Route('/{id}', name: 'newMessage', methods: ['POST'])]
    public function newMessage(Request $request, Conversation $conversation)
    {
        $this->denyAccessUnlessGranted('add', $conversation);

        // bring back security, delete user repository
        $user = $this->getUser();
        $content = $request->get('content');


        $message = new Message();
        $message->setContent($content);
        $message->setUser($this->userRep->findOneBy(['id' => 2]));
        $message->setMine(true);

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

        return $this->json($message, Response::HTTP_CREATED, [], [
            'attributes' => self::ATTRIBUTES_TO_SERIALIZE
        ]);
    }
}
