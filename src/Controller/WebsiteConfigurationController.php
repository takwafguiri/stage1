<?php

namespace App\Controller;

use SmurfsSoftware\SeohubEntitiesBundle\Entity\WebsiteConfiguration;
use App\Form\WebsiteConfigurationType;
use SmurfsSoftware\SeohubEntitiesBundle\Repository\WebsiteConfigurationRepository;
use SmurfsSoftware\SeohubEntitiesBundle\Repository\WebsiteRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Enum\SessionKeys;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/configuration', name: 'app_website_configuration_')]
#[IsGranted('selected_one', 'request', statusCode: 404, message: 'Resource Not Found :)')]
class WebsiteConfigurationController extends AbstractController
{
    private WebsiteConfigurationRepository $websiteConfigurationRepository;

    public function __construct(WebsiteConfigurationRepository $websiteConfigurationRepository)
    {
        $this->websiteConfigurationRepository = $websiteConfigurationRepository;
    }

    #[Route('/list', name: 'list', options: ['expose' => true])]
    public function index(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $get = $request->query->all();
            $tableResult = $this->websiteConfigurationRepository->ajaxTable($get);
            return new JsonResponse($tableResult);
        }
        return $this->render('website_configuration/liste.html.twig');
    }


    #[Route('/new', name: 'new')]
    #[Route(path: '/edit/{websiteConfiguration}', name: 'edit', options: ['expose' => true])]
    public function createAction(SessionInterface $session, Request $request, WebsiteRepository $websiteRepository, WebsiteConfiguration $websiteConfiguration = null): Response
    {
        $isNew = false;

        if ($websiteConfiguration == null) {
            $successMessage = "Configuration created";
            $websiteConfiguration = new WebsiteConfiguration();
            $selectedWebsiteId = $session->get(SessionKeys::SELECTED_SITE);
            $website = $websiteRepository->find($selectedWebsiteId);
            $websiteConfiguration->setWebsite($website);
            $isNew = true;
        } else {
            $successMessage = "Configuration updated";
        }

        $form = $this->createForm(WebsiteConfigurationType::class, $websiteConfiguration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->websiteConfigurationRepository->save($websiteConfiguration);
            $this->addFlash('success', $successMessage);
            return $this->redirectToRoute('app_website_configuration_list');
        }

        return $this->render('website_configuration/new.html.twig', [
            'form' => $form->createView(),
            'isNew' => $isNew
        ]);
    }
}