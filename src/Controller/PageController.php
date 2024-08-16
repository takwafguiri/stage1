<?php



namespace App\Controller;

use SmurfsSoftware\SeohubEntitiesBundle\Entity\Page;
use App\Form\PageType;
use SmurfsSoftware\SeohubEntitiesBundle\Entity\Service;
use SmurfsSoftware\SeohubEntitiesBundle\Entity\WebsiteCategory;
use SmurfsSoftware\SeohubEntitiesBundle\Repository\PageRepository;
use SmurfsSoftware\SeohubEntitiesBundle\Repository\ServiceRepository;
use SmurfsSoftware\SeohubEntitiesBundle\Repository\WebsiteCategoyRepository;
use SmurfsSoftware\SeohubEntitiesBundle\Repository\WebsiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Enum\SessionKeys;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('/page', name: 'app_page_')]
#[IsGranted('selected_one', 'request', statusCode: 404, message: 'Resource Not Found :)')]
class PageController extends AbstractController
{
    private WebsiteRepository $websiteRepository;
    private PageRepository $pageRepository;
    private WebsiteCategoyRepository $websiteCategoyRepository;
    private ServiceRepository $ServiceRepository;

    public function __construct(WebsiteRepository $websiteRepository, PageRepository $pageRepository, WebsiteCategoyRepository $websiteCategoyRepository , ServiceRepository $ServiceRepository)
    {
        $this->websiteRepository = $websiteRepository;
        $this->pageRepository = $pageRepository;
        $this->websiteCategoyRepository = $websiteCategoyRepository;
        $this->ServiceRepository =$ServiceRepository;
    }

    #[Route('/list', name: 'list', options: ['expose' => true])]
    public function index(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $get = $request->query->all();
            $tableResult = $this->pageRepository->ajaxTable($get);
            return new JsonResponse($tableResult);
        }
        return $this->render('page/list.html.twig');
    }

    #[Route('/new', name: 'new')]
    #[Route(path: '/edit/{page}', name: 'edit', options: ['expose' => true])]
    public function create(Request $request, Page $page = null): Response
    {
        $selectedWebsiteId = $request->getSession()->get(SessionKeys::SELECTED_SITE);
        $website = $this->websiteRepository->find($selectedWebsiteId);
        if (is_null($website)) {
            throw new NotFoundHttpException('Website not found');
        }

        $isNew = false;
        $successMessage = "Page updated";
        $category = null;
        $Service = null;

        if ($page === null) {
            $successMessage = "Page created!";
            $page = new Page();
            $page->setWebsite($website);
            $isNew = true;
            $idcategory= $request->query->get('id');
            if ($idcategory) {
                $category = $this->websiteCategoyRepository->find($idcategory);
            }
            $idService= $request->query->get('idService');
            if ($idService) {
                $Service = $this->ServiceRepository->find($idService);
            }
        } else {
            $successMessage = "Page updated";
            $category = $page->getWebsiteCategory();
            $Service = $page->getService();
        }

        $form = $this->createForm(PageType::class, $page, [
            'selected_website' => $selectedWebsiteId
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                if ($isNew) {
                    $page->setTag(bin2hex(random_bytes(10)));
                }
        
                if ($category) {
                    $page->setWebsiteCategory($category);
                } else {
                    $page->setWebsiteCategory(null);
                }
                if ($Service) {
                    $page->setService($Service);
                } else {
                    $page->setService(null);

                }
        
                if ($page->getHasCoverImage() && $coverImage = $form['coverImage']->getData()) {
                    $fileName = uniqid() . '.' . $coverImage->guessExtension();
                    $coverImage->move(
                        $this->getParameter("website_files_directory") . $website->getToken() . "/page/",
                        $fileName
                    );
                    $page->setCoverImage($this->getParameter("external_website_files_directory") . $website->getToken() . "/page/$fileName");
                }
                $this->pageRepository->save($page);
                $this->addFlash('success', $successMessage);
                if($isNew) {
                    return $this->redirectToRoute('app_page_list');
                }
            } else {
                $this->addFlash('error', 'Please validate all fields');
            }
        }
        

        return $this->render('page/new.html.twig', [
            'form' => $form->createView(),
            'page' => $page,
            'filePath' => $isNew ? '' : $page->getCoverImage(),
            'isNew' => $isNew
        ]);
    }
}
