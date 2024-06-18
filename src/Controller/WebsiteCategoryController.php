<?php

namespace App\Controller;

use SmurfsSoftware\SeohubEntitiesBundle\Entity\WebsiteCategory;
use App\Form\WebsiteCategoryType;
use SmurfsSoftware\SeohubEntitiesBundle\Repository\WebsiteCategoyRepository;
use SmurfsSoftware\SeohubEntitiesBundle\Repository\WebsiteRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Enum\SessionKeys;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/category', name: 'app_category_')]
#[IsGranted('selected_one', 'request', statusCode: 404, message: 'Resource Not Found :)')]
class WebsiteCategoryController extends AbstractController
{
    public function __construct(
        private readonly WebsiteCategoyRepository $websiteCategoryRepository,
        private readonly WebsiteRepository        $websiteRepository)
    {
    }

    #[Route('/list', name: 'list', options: ['expose' => true])]
    public function index(Request $request): response
    {

        if ($request->isXmlHttpRequest()) {
            $get = $request->query->all();
            $tableResult = $this->websiteCategoryRepository->ajaxTable($get);
            return new JsonResponse($tableResult);
        }

        return $this->render('category/list.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }

    /**
     * @param WebsiteCategory|null $websiteCategory
     * @param Request $request
     * @param string $pictureDir
     * @return Response
     */
    #[Route('/new', name: 'new')]
    #[Route(path: '/edit/{websiteCategory}', name: 'edit', options: ['expose' => true])]
    public function createAction(Request $request, WebsiteCategory $websiteCategory = null): Response
    {
        $selectedWebsiteId = $request->getSession()->get(SessionKeys::SELECTED_SITE);
        $website = $this->websiteRepository->find($selectedWebsiteId);
    
        $page = $websiteCategory ? $websiteCategory->getPage() : null;
    
        if ($websiteCategory == null) {
            $successMessage = "Category created";
            $websiteCategory = new WebsiteCategory();
            $websiteCategory->setWebsite($website);
            $isNew = true;
        } else {
            $successMessage = "Category updated";
            $isNew = false;
        }
    
        $form = $this->createForm(WebsiteCategoryType::class, $websiteCategory, [
            'selected_website' => $selectedWebsiteId
        ]);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            if ($picture = $form['picture']->getData()) {
                $fileName = uniqid() . '.' . $picture->guessExtension();
                $picture->move(
                    $this->getParameter("website_files_directory") . $website->getToken() . "/category/",
                    $fileName
                );
                $websiteCategory->setPicture($this->getParameter("external_website_files_directory") . $website->getToken() . "/category/$fileName");
            }
            $this->websiteCategoryRepository->save($websiteCategory);
    
            $this->addFlash('success', $successMessage);
    
            $submitAction = $request->request->get('submit_action');
            if ($submitAction === 'save_and_create') {
                $categoryId = $websiteCategory->getId();
                $redirectRoute = $page ? 'app_page_edit' : 'app_page_new';
                $routeParams = $page ? ['page' => $page->getId()] : ['id' => $categoryId];
                return $this->redirectToRoute($redirectRoute, $routeParams);
            }
    
            return $this->redirectToRoute('app_category_list');
        }
    
        return $this->render('category/new.html.twig', [
            'form' => $form->createView(),
            'isNew' => $isNew,
            'filePath' => $isNew ? '' : $websiteCategory->getPicture()
        ]);
    }
}    