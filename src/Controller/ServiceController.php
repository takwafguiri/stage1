<?php

namespace App\Controller;

use SmurfsSoftware\SeohubEntitiesBundle\Entity\Service;
use App\Form\ServiceType;
use SmurfsSoftware\SeohubEntitiesBundle\Repository\WebsiteRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use SmurfsSoftware\SeohubEntitiesBundle\Repository\ServiceRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Enum\SessionKeys;

#[Route('/service', name: 'app_service_')]
class ServiceController extends AbstractController
{

    public function __construct(
        readonly private ServiceRepository $serviceRepository,
        readonly private WebsiteRepository $websiteRepository)
    {
    }

    #[Route('/list', name: 'list', options: ['expose' => true])]
    public function index(Request $request): response
    {
        if ($request->isXmlHttpRequest()) {
            $get = $request->query->all();
            $tableResult = $this->serviceRepository->ajaxTable($get);
            return new JsonResponse($tableResult);
        }
        return $this->render('service/list.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }

    #[Route('/new', name: 'new')]
    #[Route(path: '/edit/{service}', name: 'edit', options: ['expose' => true])]
    public function createAction(Request $request, Service $service = null): Response
    {
        $selectedWebsiteId = $request->getSession()->get(SessionKeys::SELECTED_SITE);
        $website = $this->websiteRepository->find($selectedWebsiteId);
    
        $page = $service ? $service->getPage() : null;
    
        $isNew = false;
        if ($service == null) {
            $successMessage = "Service created";
            $service = new Service();
            $isNew = true;
        } else {
            $successMessage = "Service updated";
        }
    
        $form = $this->createForm(ServiceType::class, $service, [
            'selected_website' => $selectedWebsiteId
        ]);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            if ($photo = $form['photo']->getData()) {
                $fileName = uniqid() . '.' . $photo->guessExtension();
                $photo->move(
                    $this->getParameter("website_files_directory") . $website->getToken() . "/service/",
                    $fileName
                );
                $service->setPhoto($this->getParameter("external_website_files_directory") . $website->getToken() . "/service/$fileName");
            }
            $this->serviceRepository->save($service);
    
            $this->addFlash('success', $successMessage);
            $submitAction = $request->request->get('submit_action');
            if ($submitAction === 'save_and_create') {
                $redirectRoute = $page ? 'app_page_edit' : 'app_page_new';
                $routeParams = $page ? ['page' => $page->getId()] : ['idService' => $service->getId()];
                return $this->redirectToRoute($redirectRoute, $routeParams);
            }
            return $this->redirectToRoute('app_service_list');

        }
    
        return $this->render('service/new.html.twig', [
            'form' => $form->createView(),
            'isNew' => $isNew,
            'filePath' => $isNew ? '' : $service->getPhoto()
        ]);
    }
}    