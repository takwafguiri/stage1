<?php

namespace App\Controller;

use SmurfsSoftware\SeohubEntitiesBundle\Entity\Website;
use App\Form\WebsiteType;
use SmurfsSoftware\SeohubEntitiesBundle\Repository\WebsiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/website', name: 'app_website_')]
class WebsiteController extends AbstractController

{
    private WebsiteRepository $websiteRepository;

    public function __construct(
        WebsiteRepository              $websiteRepository,
    )
    {
        $this->websiteRepository = $websiteRepository;
    }

    #[Route('/list', name: 'list', options: ['expose' => true])]
    public function index(Request $request): response
    {
        if ($request->isXmlHttpRequest()) {
            $get = $request->query->all();
            $tableResult = $this->websiteRepository->ajaxTable($get);

            return new JsonResponse($tableResult);
        }
        return $this->render('website/list.html.twig');
    }

    #[Route('/new', name: 'add')]
    #[Route(path: '/edit/{website}', name: 'edit', options: ['expose' => true])]
    public function createAction(Website $website = null, Request $request): Response
    {
        if ($website == null) {
            $successMessage = "Website created";
            $website = new Website();
            $isNew = true;
        } else {
            $website = $this->websiteRepository->findOneBy(['id' => $website]);
            $successMessage = "Website updated";
            $isNew = false;
        }
        $form = $this->createForm(WebsiteType::class, $website);
        if($isNew)
            $form->remove('isEnabled');
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                if ($isNew) {
                    $website->initWebsite();
                    $website->setToken(bin2hex(random_bytes(10)));
                }
                $this->websiteRepository->save($website);
                $this->addFlash('success', $successMessage);
                if ($isNew) {
                    return $this->redirectToRoute('app_website_list');
                }
            } else {
                $this->addFlash('error', 'Please validate all fields');
            }
        }

        return $this->render('website/new.html.twig', [
            'form' => $form->createView(),
            'isNew' => $isNew
        ]);
    }
}
