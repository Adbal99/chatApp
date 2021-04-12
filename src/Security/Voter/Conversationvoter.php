<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ConversationVoter extends Voter
{

    const VIEW = 'view';

    protected function supports(string $attribute, $subject)
    {
        dd($attribute, $subject);
    }
    
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {

    }
}
