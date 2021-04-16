<?php

namespace App\Security\Voter;

use App\Entity\Conversation;
use App\Entity\User;
use App\Repository\ConversationRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ConversationVoter extends Voter
{
    const VIEW = 'view';
    const ADD = 'add';
    private $conversationRepository;

    public function __construct(ConversationRepository $conversationRepository)
    {
        $this->conversationRepository = $conversationRepository;
    }


    protected function supports(string $attribute, $subject)
    {
        // dd($attribute, $subject);
        // return $attribute == self::VIEW && $subject instanceof Conversation;

        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::VIEW, self::ADD])) {
            return false;
        }

        // only vote on `Conversation` objects
        if (!$subject instanceof Conversation) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        // if user is not logged, deny access
        if (!$user instanceof User) {
            return false;
        }

        // dd($attribute, $subject, $token);

        $result = $this->conversationRepository->checkIfUserIsParticipant($user->getId(), $subject->getId());
        // dd($result);
        // return !!$result;

        switch ($attribute) {
            case self::VIEW || self::ADD:
                return !!$result;
                break;
        }
    }
}
