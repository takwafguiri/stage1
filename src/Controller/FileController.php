<?php

namespace App\Controller;

use SmurfsSoftware\SeohubEntitiesBundle\Entity\File;
use SmurfsSoftware\SeohubEntitiesBundle\Entity\Website;
use App\Enum\SessionKeys;
use App\Form\UploadFileType;
use SmurfsSoftware\SeohubEntitiesBundle\Repository\FileRepository;
use SmurfsSoftware\SeohubEntitiesBundle\Repository\WebsiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/file', name: 'app_file_')]
#[IsGranted('selected_one', 'request', statusCode: 404, message: 'Resource Not Found :)')]
class FileController extends AbstractController
{
    public function __construct(readonly FileRepository $fileRepository, readonly WebsiteRepository $websiteRepository)
    {
    }

    #[Route('/', name: 'index', options: ['expose' => true])]
    public function index(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $get = $request->query->all();
            $tableResult = $this->fileRepository->ajaxTable($get);

            return new JsonResponse($tableResult);
        }
        return $this->render('file/index.html.twig', [
            'uploadFileForm' => $this->createForm(UploadFileType::class)->createView(),
        ]);
    }
}
