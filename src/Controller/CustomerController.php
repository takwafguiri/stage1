<?php

namespace App\Controller;

use App\Enum\UserRole;
use App\Form\CustomerType;
use App\Form\ProspectType;
use SmurfsSoftware\SeohubEntitiesBundle\Entity\Customer;
use SmurfsSoftware\SeohubEntitiesBundle\Repository\CustomerRepository;
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
#[Route('/customer', name: 'app_customers_')]
class CustomerController extends AbstractController
{
    private CustomerRepository $customerRepository;
    private TokenStorageInterface $tokenStorage;
    private WebsiteRepository $websiteRepository;

    public function __construct(TokenStorageInterface $tokenStorage, WebsiteRepository $websiteRepository, CustomerRepository $customerRepository)
    {
        $this->websiteRepository = $websiteRepository;
        $this->tokenStorage = $tokenStorage;
        $this->customerRepository = $customerRepository;
    }
    #[Route('/list', name: 'list', options: ['expose' => true])]
    public function index(Request $request): response
    {
        $session = $request->getSession();
        if ($request->isXmlHttpRequest()) {
            $get = $request->query->all();
            $tableResult = $this->customerRepository->ajaxTable($get);

            return new JsonResponse($tableResult);
        }
        return $this->render('customer/list.html.twig', [
            'selected_website' => $session->get('selected_website')
        ]);
    }

    #[Route(path: '/add', name: 'add')]
    #[Route(path: '/edit/{customer}', name: 'edit', options: ['expose' => true])]
    public function add(Customer $customer = null, Request $request): Response
    {
        if ($customer == null) {
            $successMessage = "Customer created";
            $customer = new Customer();
            $session = $request->getSession();
            $website = $this->websiteRepository->findOneBy(['id' => $session->get('selected-website')]);
            $customer->setWebsite($website);
            $isNew = true;
        } else {
            $customer = $this->customerRepository->findOneBy(['id' => $customer]);
            $successMessage = "Customer updated";
            $isNew = false;
        }
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->customerRepository->save($customer);
                $this->addFlash('success', $successMessage);
                return $this->redirectToRoute('app_customers_list');
            } else {
                $this->addFlash('error', 'Please validate all fields');
            }
        }

        return $this->render('customer/new.html.twig', [
            'form' => $form->createView(),
            'isNew' => $isNew
        ]);
    }

}
