<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/messages', name: 'messages.')]
class MessageController extends AbstractController
{

    private $entityMnager;
    private $messageRepository;

    public function __construct(EntityManagerInterface $entityMnager, MessageRepository $messageRepository)
    {
        $this->entityMnager = $entityMnager;
        $this->messageRepository = $messageRepository;
    }

    #[Route('/{id}', name: 'getMessages')]

    public function index(Request $request, Conversation $conversation)
    {

        // can i view the conversation
        $this->denyAccessUnlessGranted('view', $conversation);

        $messages = $this->messageRepository->findMessagesByConversation($conversation->getId());

        /**
         * @var   Message  $message  
         */
        array_map(function ($message) {
            
        }, $messages);

        return $this->render('message/index.html.twig', [
            'controller_name' => 'MessageController',
        ]);
    }
}
