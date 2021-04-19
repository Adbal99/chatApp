<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Participant;
use App\Repository\ConversationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\WebLink\Link;

#[Route('/conversations', name: 'conversations.')]
class ConversationController extends AbstractController
{

    private UserRepository $userRepository;
    private ConversationRepository $conversationRepository;
    private EntityManagerInterface $em;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $em, ConversationRepository $conversationRepository)
    {
        $this->userRepository = $userRepository;
        $this->em = $em;
        $this->conversationRepository = $conversationRepository;
    }

    #[Route('/', name: 'newConversations', methods: ['POST'])]
    public function index(Request $request)
    {
        $otherUser = $request->get("otherUser", 0);
        $otherUser = $this->userRepository->find($otherUser);

        if (is_null($otherUser)) {
            throw new \Exception("user not found", 404);
        }

        //cannot make conversation with yourself
        if ($otherUser->getId() === $this->getUser()->getId()) {
            throw new \Exception("cannot make conversation with yourself...");
        }

        //check if conversation already exists
        $conversation = $this->conversationRepository->findConversationByParticipants($otherUser->getId(), $user = $this->getUser()->getId());

        // dd($conversation);
        if (count($conversation)) {
            throw new \Exception("Conversation already exists");
        }
        $conversation = new Conversation();

        $participant = new Participant();
        $participant->setUser($this->getUser());
        $participant->setConversation($conversation);

        $otherParticipant = new Participant();
        $otherParticipant->setUser($otherUser);
        $otherParticipant->setConversation($conversation);

        $this->em->getConnection()->beginTransaction();

        try {
            $this->em->persist($conversation);
            $this->em->persist($participant);
            $this->em->persist($otherParticipant);

            $this->em->flush();
            $this->em->commit();
        } catch (\Exception $e) {
            $this->em->rollback();
            throw $e;
        }

        return $this->json([
            'id' => $conversation->getId()
        ], Response::HTTP_CREATED, [], []);
    }


    #[Route('/', name: 'getConversations', methods: ['GET'])]
    public function getConversations(Request $request): JsonResponse
    {
        $conversations = $this->conversationRepository->findConversationsByUser($this->getUser()->getId());

        $hubUrl = $this->getParameter('mercure.default_hub');

        // dd($hubUrl);
        $this->addLink($request, new Link('mercure', $hubUrl));
        return $this->json([$conversations]);
    }
}
