<?php

namespace App\Controller;

use SmurfsSoftware\SeohubEntitiesBundle\Entity\User;
use App\Enum\UserRole;
use App\Form\UserFormType;
use SmurfsSoftware\SeohubEntitiesBundle\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/users', name: 'app_users_')]
#[IsGranted('selected_all', 'request', statusCode: 404, message: 'Resource Not Found :)')]
class UserController extends AbstractController
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    #[Route('/list', name: 'list', options: ['expose' => true])]
    public function index(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $get = $request->query->all();
            $tableResult = $this->userRepository->ajaxTable($get);

            return new JsonResponse($tableResult);
        }
        return $this->render('user/list.html.twig');
    }

    #[Route(path: '/add', name: 'add')]
    #[Route(path: '/edit/{user}', name: 'edit', options: ['expose' => true])]
    public function add(User $user = null, Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        if ($user == null) {
            $successMessage = "User created";
            $user = new User();
            $isNew = true;
        } else {
            $user = $this->userRepository->findOneBy(['id' => $user]);
            $successMessage = "website affected";
            $isNew = false;
        }
        $form = $this->createForm(UserFormType::class, $user);

        if ($isNew) {
            $form->remove('isEnabled');
        } else {
            $form->remove('Password');
        }

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                if ($isNew) {
                    $user->setPassword(
                        $userPasswordHasher->hashPassword(
                            $user,
                            $form->get('Password')->getData()
                        )
                    );
                }
                $user->setRoles([$form->get('role')->getData()]);
                if ($form->get('role')->getData() == UserRole::ROLE_ADMIN) {
                    foreach ($user->getWebsites() as $website) {
                        $user->removeWebsite($website);
                    }
                }
                $this->userRepository->save($user);
                $this->addFlash('success', $successMessage);
                return $this->redirectToRoute('app_users_list');
            } else {
                $this->addFlash('error', 'Please validate all fields');
            }
        }


        return $this->render('user/new.html.twig', [
            'form' => $form->createView(),
            'isNew' => $isNew
        ]);
    
    }

    #[Route(path: '/change-password/{id}', name: 'change_password', options: ['expose' => true], methods: ['POST'])]
    public function changePassword(Request $request, User $user, UserPasswordHasherInterface $userPasswordHasher): JsonResponse
    {
        $password = $request->request->get('password');
        if ($password) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $password
                )
            );
            $this->userRepository->save($user);
            return new JsonResponse(['status' => 'success', 'message' => 'Password updated successfully']);
        }

        return new JsonResponse(['status' => 'error', 'message' => 'Password is required'], Response::HTTP_BAD_REQUEST);
    }
}
