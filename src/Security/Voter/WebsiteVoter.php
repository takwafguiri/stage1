<?php

// src/Security/PostVoter.php
namespace App\Security\Voter;

use SmurfsSoftware\SeohubEntitiesBundle\Entity\User;
use App\Enum\SessionKeys;
use App\Enum\UserRole;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class WebsiteVoter extends Voter
{
    const ALL_WEBSITE = 'selected_all';
    const ONE_WEBSITE = 'selected_one';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::ALL_WEBSITE,  self::ONE_WEBSITE])) {
            return false;
        }

        if (!$subject instanceof Request) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var Request $request */
        $request = $subject;
        return match ($attribute) {
            self::ALL_WEBSITE => $this->hasAccessToAllWebsites($request, $token),
            self::ONE_WEBSITE => $this->hasAccessToOneWebsite($request, $token)
        };
    }

    private function hasAccessToAllWebsites(Request $request, TokenInterface $token): bool
    {
        $currentSession  = $request->getSession()->get(SessionKeys::SELECTED_SITE);
        if($currentSession == 'all' && in_array(UserRole::ROLE_ADMIN, $token->getUser()->getRoles())) {
            return true;
        }
        return  false;
    }

    private function hasAccessToOneWebsite(Request $request, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();
        $currentSession  = $request->getSession()->get(SessionKeys::SELECTED_SITE);

        // Create a criteria to filter files by ID
        $criteria = Criteria::create()->andWhere(Criteria::expr()->eq('id', $currentSession));
        // Apply the criteria to the files collection
        $filteredFiles = $user->getWebsites()->matching($criteria);

        if($currentSession != 'all' && in_array(UserRole::ROLE_ADMIN, $user->getRoles()) || $filteredFiles->count() > 0) {
            return true;
        }
        return  false;
    }

}