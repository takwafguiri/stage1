<?php

namespace App\Controller;

use App\Enum\UserRole;
use App\Form\NoteType;
use App\Form\ProspectType;
use SmurfsSoftware\SeohubEntitiesBundle\Entity\Customer;
use SmurfsSoftware\SeohubEntitiesBundle\Entity\Note;
use SmurfsSoftware\SeohubEntitiesBundle\Entity\Prospect;
use SmurfsSoftware\SeohubEntitiesBundle\Entity\User;
use SmurfsSoftware\SeohubEntitiesBundle\Repository\CustomerRepository;
use SmurfsSoftware\SeohubEntitiesBundle\Repository\NoteRepository;
use SmurfsSoftware\SeohubEntitiesBundle\Repository\ProspectRepository;
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
#[Route('/prospect', name: 'app_prospects_')]
class ProspectController extends AbstractController
{
    private CustomerRepository $customerRepository;
    private ProspectRepository $prospectRepository;
    private TokenStorageInterface $tokenStorage;
    private WebsiteRepository $websiteRepository;
    private NoteRepository $noteRepository;

    public function __construct(NoteRepository $noteRepository,ProspectRepository $prospectRepository, TokenStorageInterface $tokenStorage, WebsiteRepository $websiteRepository, CustomerRepository $customerRepository)
    {
        $this->prospectRepository = $prospectRepository;
        $this->websiteRepository = $websiteRepository;
        $this->tokenStorage = $tokenStorage;
        $this->customerRepository = $customerRepository;
        $this->noteRepository = $noteRepository;
    }

    #[Route('/list', name: 'list', options: ['expose' => true])]
    public function index(Request $request): response
    {
        $session = $request->getSession();
        if ($request->isXmlHttpRequest()) {
            $get = $request->query->all();
            $tableResult = $this->prospectRepository->ajaxTable($get);

            return new JsonResponse($tableResult);
        }
        return $this->render('prospect/list.html.twig', [
            'selected_website' => $session->get('selected_website')
        ]);
    }

    #[Route(path: '/add', name: 'add')]
    #[Route(path: '/edit/{prospect}', name: 'edit', options: ['expose' => true])]
    public function add(Prospect $prospect = null, Request $request): Response
    {
        if ($prospect == null) {
            $successMessage = "Prospect created";
            $prospect = new Prospect();
            $session = $request->getSession();
            $website = $this->websiteRepository->findOneBy(['id' => $session->get('selected-website')]);
            $prospect->setWebsite($website);
            $isNew = true;
        } else {
            $prospect = $this->prospectRepository->findOneBy(['id' => $prospect]);
            $successMessage = "Prospect updated";
            $isNew = false;
        }
        $form = $this->createForm(ProspectType::class, $prospect);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->prospectRepository->save($prospect);
                $this->addFlash('success', $successMessage);
                return $this->redirectToRoute('app_prospects_list');
            } else {
                $this->addFlash('error', 'Please validate all fields');
            }
        }
        if ($prospect->getId()) {
            $content = [
                'form' => $form->createView(),
                'prospect' => $prospect->getId(),
                'isNew' => $isNew,
                'hasCustomer' => (bool)$prospect->getCustomer()
            ];
        } else {
            $content = [
                'form' => $form->createView(),
                'isNew' => $isNew,
            ];
        }
        return $this->render('prospect/new.html.twig', $content);
    }

    #[Route(path: '/show/{prospect}', name: 'show', options: ['expose' => true])]
    public function showAction(Prospect $prospect = null, Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $get = $request->query->all();
            $tableResult = $this->noteRepository->ajaxTable($get);
            return new JsonResponse($tableResult);
        }
        $prospect = $this->prospectRepository->findOneBy(['id' => $prospect]);
        return $this->render('prospect/show.html.twig', [
            'prospect' => $prospect
        ]);
    }
    #[Route(path: '/transform_to_customer/{prospect}', name: 'transform_to_customer', options: ['expose' => true])]
    public function transformToCustomer(Prospect $prospect, Request $request): Response
    {
        $session = $request->getSession();
        $prospect = $this->prospectRepository->findOneBy(['id' => $prospect]);
        $customer = new Customer();
        $customer->setEmail($prospect->getEmail());
        $customer->setWebsite($prospect->getWebsite());
        $customer->setFirstName($prospect->getFirstName());
        $customer->setLastName($prospect->getLastName());
        $customer->setPhoneNumber($prospect->getPhoneNumber());
        $customer->setWebsite($prospect->getWebsite());
        $customer->setProspect($prospect);
        if ($prospect->getCommercial() !== null) {
            $customer->setCommercial($prospect->getCommercial());
        }
        $this->customerRepository->save($customer);
        return $this->render('customer/list.html.twig', [
            'selected_website' => $session->get('selected_website')
        ]);
    }
}
