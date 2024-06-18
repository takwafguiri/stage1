<?php

namespace App\Security;

use SmurfsSoftware\SeohubEntitiesBundle\Entity\User;
use App\Enum\SessionKeys;
use App\Enum\UserRole;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(private Security $security, private readonly UrlGeneratorInterface $urlGenerator)
    {
    }
    public function onAuthenticationSuccess(Request $request, TokenInterface $token): ?Response
    {
        /** @var User $connectedUser */
        $request->getSession()->set(SessionKeys::SELECTED_SITE, null);
        $connectedUser = $token->getUser();
        if($this->security->isGranted(UserRole::ROLE_ADMIN)) {
            $request->getSession()->set(SessionKeys::SELECTED_SITE, 'all');
        } else {
            foreach ($connectedUser->getWebsites() as $website) {
                if($website->getIsEnabled()) {
                    $request->getSession()->set(SessionKeys::SELECTED_SITE, $website->getId());
                }
            }
        }
        if($request->getSession()->get(SessionKeys::SELECTED_SITE) === null) {
            $request->getSession()->set(SessionKeys::SELECTED_SITE, 'none');
            return new RedirectResponse('app_profile_edit');
        }

        return new RedirectResponse($this->urlGenerator->generate('app_dashboard'));
    }
}