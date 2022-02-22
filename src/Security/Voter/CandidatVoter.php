<?php

namespace AcMarche\MeriteSportif\Security\Voter;

use AcMarche\MeriteSportif\Entity\Candidat;
use AcMarche\MeriteSportif\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CandidatVoter extends Voter
{
    protected function supports($attribute, $subject):bool
    {
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['CANDIDAT_EDIT', 'CANDIDAT_VIEW'])
            && $subject instanceof Candidat;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token):bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($user->hasRole('ROLE_MERITE_ADMIN')) {
            return true;
        }

        switch ($attribute) {
            case 'CANDIDAT_EDIT':
                return $this->canEdit($subject, $user);
                break;
            case 'POST_VIEW':
                // logic to determine if the user can VIEW
                // return true or false
                break;
        }

        return false;
    }

    private function canEdit(Candidat $subject, User $user): bool
    {
        $club = $user->getClub();
        if ($club === null) {
            return false;
        }

        return $subject->getAddBy() === $club->getEmail();
    }
}
