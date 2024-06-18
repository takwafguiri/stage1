<?php

namespace App\Controller;

use SmurfsSoftware\SeohubEntitiesBundle\Entity\Component;
use App\Form\ComponentType;
use SmurfsSoftware\SeohubEntitiesBundle\Repository\ComponentRepository;
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

#[Route('/component', name: 'app_component_')]
#[IsGranted('selected_one', 'request', statusCode: 404, message: 'Resource Not Found :)')]
class ComponentController extends AbstractController
{
    private ComponentRepository $componentRepository;

    public function __construct(ComponentRepository $componentRepository)
    {
        $this->componentRepository = $componentRepository;
    }

    #[Route('/list', name: 'list', options: ['expose' => true])]
    public function index(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $get = $request->query->all();
            $tableResult = $this->componentRepository->ajaxTable($get);
            return new JsonResponse($tableResult);
        }

        return $this->render('component/liste.html.twig');
    }


    #[Route('/new', name: 'new')]
    #[Route(path: '/edit/{component}', name: 'edit', options: ['expose' => true])]
    public function createAction(SessionInterface $session, Request $request, WebsiteRepository $websiteRepository, Component $component = null): Response
    {
        $selectedWebsiteId = $session->get(SessionKeys::SELECTED_SITE);
        $website = $websiteRepository->find($selectedWebsiteId);
        $isNew = false;
        if (is_null($website)) {
            return throw new NotFoundHttpException('Website not found');
        }

        if ($component == null) {
            $successMessage = "Component created";
            $component = new Component();
            $component->setWebsite($website);
            $isNew = true;
        } else {
            $component = $this->componentRepository->findOneBy(['id' => $component]);
            $successMessage = "Component updated";
        }

        $form = $this->createForm(ComponentType::class, $component);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->componentRepository->save($component);
            $this->addFlash('success', $successMessage);
            return $this->redirectToRoute('app_component_list');
        }

        return $this->render('component/new.html.twig', [
            'form' => $form->createView(),
            'isNew' => $isNew
        ]);
    }
}

