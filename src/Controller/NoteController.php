<?php

namespace App\Controller;

use App\Enum\UserRole;
use App\Form\NoteType;
use SmurfsSoftware\SeohubEntitiesBundle\Entity\Note;
use SmurfsSoftware\SeohubEntitiesBundle\Entity\Prospect;
use SmurfsSoftware\SeohubEntitiesBundle\Repository\NoteRepository;
use SmurfsSoftware\SeohubEntitiesBundle\Repository\ProspectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use function PHPUnit\Framework\isNull;

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
#[Route('/note', name: 'app_notes_')]
class NoteController extends AbstractController
{
    private ProspectRepository $prospectRepository;
    private NoteRepository $noteRepository;

    public function __construct(NoteRepository $noteRepository, ProspectRepository $prospectRepository)
    {
        $this->prospectRepository = $prospectRepository;
        $this->noteRepository = $noteRepository;
    }

    #[Route(path: '/{prospect}/new', name: 'new', options: ['expose' => true])]
    #[Route(path: '/edit/{note}', name: 'edit', options: ['expose' => true])]
    public function newAction(Prospect $prospect = null, Note $note = null, Request $request): Response
    {
        if(is_null($note)) {
            $successMessage = "Note created";
            $note = new Note();
            $isNew = true;
        } else {
            $prospect = $note->getProspect();
            $successMessage = "Note updated";
            $isNew = false;
        }

        //Create note form.
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        //Handle request.
        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                //If it's new note set user and update prospect status.
                if($isNew) {
                    $note->setProspect($prospect);
                    $prospect->setStatus($note->getStatus()->getLabel());
                    $note->setUser($this->getUser());
                }

                $this->noteRepository->save($note);
                $this->addFlash('success', $successMessage);

                return $this->redirectToRoute('app_prospects_show', [
                    'prospect' => $prospect->getId()
                ]);
            } else {
                $this->addFlash('error', 'Please validate all fields');
            }
        }
        $content = [
            'prospect' => $prospect,
            'form' => $form->createView(),
            'isNew' => $isNew,
            'note' => $note
        ];
        return $this->render('prospect/new_note.html.twig', $content);
    }

}
