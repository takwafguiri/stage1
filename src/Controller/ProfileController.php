<?php

namespace App\Controller;

use SmurfsSoftware\SeohubEntitiesBundle\Entity\User;
use App\Enum\SessionKeys;
use App\Form\ChangePasswordType;
use App\Form\ProfileType;
use App\Form\UploadPictureType;
use SmurfsSoftware\SeohubEntitiesBundle\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/profile', name: 'app_profile_')]
class ProfileController extends AbstractController
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    #[Route('/', name: 'edit')]
    public function index(Request $request): Response
    {
        //Get the connected user.
        $connectedUser = $this->getUser();

        //Create profile update form.
        $profileForm = $this->createForm(ProfileType::class, $connectedUser);

        //Handle profile update form.
        $profileForm->handleRequest($request);

        //Check if form is submitted
        if ($profileForm->isSubmitted()) {
            //Check if form data are correct
            if ($profileForm->isValid()) {
                //Update profile.
                $this->userRepository->save($connectedUser);

                //Set success flash message
                $this->addFlash('success', 'Your profile has been updated');
            } else {
                //Set error flash message
                $this->addFlash('error', 'Please validate all fields');
            }
        }

        //Render page
        return $this->render('profile/index.html.twig', [
            'connectedUser' => $connectedUser,
            'profileForm' => $profileForm->createView(),
            'uploadPictureForm' => $this->createForm(UploadPictureType::class)->createView(),
            'pageHeader' => "My profile"
        ]);
    }

    #[Route('/change-password', name: 'password')]
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        //Get the connected user.
        /** @var User $connectedUser */
        $connectedUser = $this->getUser();

        //Create profile update form.
        $passwordForm = $this->createForm(ChangePasswordType::class);

        //Handle profile update form.
        $passwordForm->handleRequest($request);

        //Check if form is submitted & form data are correct
        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {

            //Update profile.

            //hash password.
            $hashedPassword = $passwordHasher->hashPassword(
                $connectedUser,
                $passwordForm->get('newPassword')->getData()
            );
            $connectedUser->setPassword($hashedPassword);
            $this->userRepository->save($connectedUser);


            //Set success flash message
            $this->addFlash('success', 'Password updated');
        }

        //Render page
        return $this->render('profile/change-password.html.twig', [
            'passwordForm' => $passwordForm->createView(),
            'pageHeader' => "Change password"
        ]);
    }

    #[Route('/change-website', name: 'change_website', options: ['expose' => true])]
    public function changeWebsite(Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $request->getSession()->set(SessionKeys::SELECTED_SITE, $request->request->get('website'));
        return new JsonResponse(['status' => 'success']);
    }
}
