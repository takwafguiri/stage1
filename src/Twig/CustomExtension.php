<?php

namespace App\Twig;

use App\Enum\ProspectStatus;
use Symfony\Component\HttpFoundation\Request;
use SmurfsSoftware\SeohubEntitiesBundle\Entity\User;
use App\Enum\UserRole;
use SmurfsSoftware\SeohubEntitiesBundle\Repository\WebsiteRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class CustomExtension extends AbstractExtension
{
    private TokenStorageInterface $tokenStorage;
    private WebsiteRepository $websiteRepository;
    private Security $security;

    public function __construct(WebsiteRepository $websiteRepository, tokenStorageInterface $tokenStorage, Security $security)
    {
        $this->websiteRepository = $websiteRepository;
        $this->tokenStorage = $tokenStorage;
        $this->security = $security;
    }
    public function getFilters(): array
    {
        return [
            new TwigFilter('extractRole', [$this, 'extractRole'])
        ];
    }
    public function getFunctions(): array
    {
        return [
            new TwigFunction('getAllRoles', [$this, 'getAllRoles']),
            new TwigFunction('getAllStatus', [$this, 'getAllStatus']),
            new TwigFunction('accessible_websites', [$this, 'getAccessibleWebsites']),
            new TwigFunction('getUserRole', [$this, 'getUserRole']),
            new TwigFunction('getSelectedWebsite', [$this, 'getSelectedWebsite']),
            new TwigFunction('getUserId', [$this, 'getUserId'])
        ];
    }

    public function extractRole(array $roles): string
    {
        $result = '';
        $rolesLabels = UserRole::getAllRolesWithLabel();
        foreach ($roles as $role) {


            if (isset($rolesLabels[$role])) {
                $result .= $rolesLabels[$role] . ' | ';

            }
        }
        return substr($result, 0, strlen($result)-3);
    }

    public function getAllRoles(): array
    {
        return UserRole::getAllRolesWithLabel();
    }
    public function getAllStatus(): array
    {
        return ProspectStatus::getAllStatusWithLabel();
    }

    public function getAccessibleWebsites() {

        /** @var User $currentUser */
        $currentUser = $this->tokenStorage->getToken()->getUser();
        if($this->security->isGranted(UserRole::ROLE_ADMIN)) {
            $websites = $this->websiteRepository->findBy([
               'isEnabled' => 1,
               'isDeleted' => 0
            ]);
        } else {
            $websites = $currentUser->getWebsites();
        }
        return $websites;
    }
    public function getUserRole()
    {
        /** @var User $currentUser */
        $currentUser = $this->tokenStorage->getToken()->getUser();
        $role = $currentUser->getRoles();
        return $role[0];
    }
    public function getUserId()
    {
        /** @var User $currentUser */
        $currentUser = $this->tokenStorage->getToken()->getUser();
        return $currentUser->getId();
    }
    public function getSelectedWebsite(Request $request)
    {
        $session = $request->getSession();
        return $session->get('selected_website');
    }
}