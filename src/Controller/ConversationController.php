<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Participant;
use App\Repository\ConversationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/conversations', name: 'conversations.')]
class ConversationController extends AbstractController
{

    private $userRepository;
    private $conversationRepository;
    private $em;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $em, ConversationRepository $conversationRepository) {
        $this->userRepository = $userRepository;
        $this->em = $em;
        $this->conversationRepository = $conversationRepository;
    }

    #[Route('/{id}', name: 'getConversations')]
    public function index(Request $request, int $id)
    {
        $otherUser = $request->get("otherUser", 0);
        $otherUser = $this->userRepository->find($id);

        if(is_null($otherUser)) {
            throw new \Exception("user not found", 404);
        }

        //cannot make conversation with yourself
        if ($otherUser->getId() === $user = $this->getUser()->getId()) {
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
}
