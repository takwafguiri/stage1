<?php

namespace App\Controller;

use App\Enum\UserRole;
use App\Form\NoteType;
use App\Form\ProspectType;
use App\Form\StatusType;
use SmurfsSoftware\SeohubEntitiesBundle\Entity\Customer;
use SmurfsSoftware\SeohubEntitiesBundle\Entity\Note;
use SmurfsSoftware\SeohubEntitiesBundle\Entity\Prospect;
use SmurfsSoftware\SeohubEntitiesBundle\Entity\Status;
use SmurfsSoftware\SeohubEntitiesBundle\Entity\User;
use SmurfsSoftware\SeohubEntitiesBundle\Repository\CustomerRepository;
use SmurfsSoftware\SeohubEntitiesBundle\Repository\NoteRepository;
use SmurfsSoftware\SeohubEntitiesBundle\Repository\ProspectRepository;
use SmurfsSoftware\SeohubEntitiesBundle\Repository\StatusRepository;
use SmurfsSoftware\SeohubEntitiesBundle\Repository\WebsiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(
    new Expression(
        'is_granted("' . UserRole::ROLE_COMMERCIAL . '") or ' .
        'is_granted("' . UserRole::ROLE_ADMIN . '") or ' .
        'is_granted("' . UserRole::ROLE_COMMERCIAL_MANAGER . '")'
    ),
    'request',
    statusCode: 404,
    message: 'Resource Not Found :)'
)]
#[Route('/status', name: 'app_status_')]
class StatusController extends AbstractController
{
    private CustomerRepository $customerRepository;
    private ProspectRepository $prospectRepository;
    private TokenStorageInterface $tokenStorage;
    private WebsiteRepository $websiteRepository;
    private NoteRepository $noteRepository;
    private StatusRepository $statusRepository;

    public function __construct(StatusRepository $statusRepository,NoteRepository $noteRepository, ProspectRepository $prospectRepository, TokenStorageInterface $tokenStorage, WebsiteRepository $websiteRepository, CustomerRepository $customerRepository)
    {
        $this->prospectRepository = $prospectRepository;
        $this->websiteRepository = $websiteRepository;
        $this->tokenStorage = $tokenStorage;
        $this->customerRepository = $customerRepository;
        $this->noteRepository = $noteRepository;
        $this->statusRepository = $statusRepository;
    }
    #[Route('/list', name: 'list', options: ['expose' => true])]
    public function index(Request $request): response
    {
        $session = $request->getSession();
        if ($request->isXmlHttpRequest()) {
            $get = $request->query->all();
            $tableResult = $this->statusRepository->ajaxTable($get);

            return new JsonResponse($tableResult);
        }
        return $this->render('status/list.html.twig', [
            'selected_website' => $session->get('selected_website')
        ]);
    }
    #[Route(path: '/new', name: 'new', options: ['expose' => true])]
    #[Route(path: '/edit/{status}', name: 'edit', options: ['expose' => true])]
    public function edit(Status $status = null, Request $request): Response
    {
        if ($status == null) {
            $successMessage = "Status created";
            $status = new Status();
            $isNew = true;
        } else {
            $status = $this->statusRepository->findOneBy(['id' => $status]);
            $successMessage = "Status updated";
            $isNew = false;
        }
        $form = $this->createForm(StatusType::class, $status);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->statusRepository->save($status);
                $this->addFlash('success', $successMessage);
                return $this->redirectToRoute('app_status_list');
            } else {
                $this->addFlash('error', 'Please validate all fields');
            }
        }
        return $this->render('status/new.html.twig', [
            'form' => $form->createView(),
            'isNew' => $isNew,
        ]);
    }

}
