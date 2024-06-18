<?php

namespace App\Controller;

use SmurfsSoftware\SeohubEntitiesBundle\Entity\File;
use SmurfsSoftware\SeohubEntitiesBundle\Entity\User;
use SmurfsSoftware\SeohubEntitiesBundle\Entity\Website;
use App\Enum\SessionKeys;
use App\Form\UploadFileType;
use App\Form\ServiceType;
use App\Form\UploadPictureType;
use SmurfsSoftware\SeohubEntitiesBundle\Repository\UserRepository;
use SmurfsSoftware\SeohubEntitiesBundle\Repository\WebsiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;


#[Route('/upload', name: 'app_upload_')]
class UploadController extends AbstractController
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    #[Route('/profile-image', name: 'profile_picture', methods: ['POST'])]
    public function profileImage(Request $request, SluggerInterface $slugger): Response
    {
        //Get the connected user.
        /** @var User $connectedUser */
        $connectedUser = $this->getUser();

        //Create profile image form.
        $uploadPictureForm = $this->createForm(UploadPictureType::class);

        //Handle profile image form
        $uploadPictureForm->handleRequest($request);

        //Check if form is submitted
        if ($uploadPictureForm->isSubmitted()) {
            //Check if form data are correct
            if ($uploadPictureForm->isValid()) {
                $pictureFile = $uploadPictureForm->get('picture')->getData();
                if ($pictureFile) {
                    $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $pictureFile->guessExtension();

                    // Move the file to the directory where brochures are stored
                    try {
                        $pictureFile->move(
                            $this->getParameter('profile_image_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        $this->addFlash('error', 'Error uploading image');
                    }
                }
                $connectedUser->setPicture($this->getParameter('external_profile_image_directory') . $newFilename);
                $this->userRepository->save($connectedUser);

                //Set success flash message
                $this->addFlash('success', 'Your profile image is updated');
            } else {
                //Set error flash message
                $this->addFlash('error', 'Error uploading image');
            }
        }

        return $this->redirectToRoute("app_profile_edit");
    }

    #[Route('/website-file', name: 'website_file', methods: ['POST'])]
    #[IsGranted('selected_one', 'request', statusCode: 404, message: 'Resource Not Found :)')]
    public function websiteFile(Request $request, SluggerInterface $slugger, WebsiteRepository $websiteRepository): Response
    {
        /** @var Website $website */
        $website = $websiteRepository->find($request->getSession()->get(SessionKeys::SELECTED_SITE));
        if (is_null($website)) {
            throw new NotFoundHttpException('Website not found');
        }

        // Create profile image form.
        $uploadFileForm = $this->createForm(UploadFileType::class);

        // Handle profile image form.
        $uploadFileForm->handleRequest($request);

        // Check if form is submitted.
        if ($uploadFileForm->isSubmitted()) {
            // Check if form data are correct.
            if ($uploadFileForm->isValid()) {
                $file = $uploadFileForm->get('file')->getData();

                // Check if a file is uploaded.
                if ($file) {
                    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $file->guessExtension();

                    // Generate a unique filename.
                    $newFilename = $originalFilename . '-' . uniqid() . '.' . $extension;

                    // Move the file to the directory where files are stored.
                    try {
                        $file->move(
                            $this->getParameter("website_files_directory") . $website->getToken() . "/files/",
                            $newFilename
                        );

                        // Create a new File entity.
                        $fileInstance = new File();
                        $fileInstance->setWebsite($website);
                        $fileInstance->setOriginName($originalFilename);
                        $fileInstance->setPath($this->getParameter('external_website_files_directory') . $website->getToken() . "/files/$newFilename");

                        // Set the extension if it was guessed.
                        if ($extension) {
                            $fileInstance->setExtension($extension);
                        }

                        // Persist the File entity.
                        $this->userRepository->save($fileInstance);

                        // Set success flash message.
                        $this->addFlash('success', 'File uploaded');
                    } catch (FileException $e) {
                        // Handle file upload error.
                        $this->addFlash('error', 'Error uploading file');
                    }
                } else {
                    // Handle case where no file is uploaded.
                    $this->addFlash('error', 'No file uploaded');
                }
            } else {
                // Handle invalid form data.
                $this->addFlash('error', 'Invalid form data');
            }
        }

        return $this->redirectToRoute("app_file_index");
    }
    #[Route('/website-logo', name: 'website_logo', methods: ['POST'])]
    public function websiteLogoUpload(Request $request, SluggerInterface $slugger): Response
    {
        //Get the connected user.
        /** @var User $connectedUser */
        $connectedUser = $this->getUser();

        //Create profile image form.
        $uploadPictureForm = $this->createForm(UploadPictureType::class);

        //Handle profile image form
        $uploadPictureForm->handleRequest($request);

        //Check if form is submitted
        if ($uploadPictureForm->isSubmitted()) {
            //Check if form data are correct
            if ($uploadPictureForm->isValid()) {
                $pictureFile = $uploadPictureForm->get('picture')->getData();
                if ($pictureFile) {
                    $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $pictureFile->guessExtension();

                    // Move the file to the directory where brochures are stored
                    try {
                        $pictureFile->move(
                            $this->getParameter('profile_image_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        $this->addFlash('error', 'Error uploading image');
                    }
                }
                $connectedUser->setPicture($this->getParameter('external_profile_image_directory') . $newFilename);
                $this->userRepository->save($connectedUser);

                //Set success flash message
                $this->addFlash('success', 'Your profile image is updated');
            } else {
                //Set error flash message
                $this->addFlash('error', 'Error uploading image');
            }
        }

        return $this->redirectToRoute("app_profile_edit");
    }

}