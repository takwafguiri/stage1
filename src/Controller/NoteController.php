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

    #[Route(path: '/edit/{note}', name: 'edit', options: ['expose' => true])]
    public function edit(Note $note = null, Request $request): Response
    {
        $note = $this->noteRepository->findOneBy(['id' => $note]);
        $successMessage = "Note updated";
        $isNew = false;
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->noteRepository->save($note);
                $this->addFlash('success', $successMessage);
                return $this->redirectToRoute('app_prospects_show',[
                    'prospect' => $note->getProspect()->getId()
                ]);
            } else {
                $this->addFlash('error', 'Please validate all fields');
            }
        }
        $content = [
            'form' => $form->createView(),
            'note' => $note->getId(),
            'isNew' => $isNew
        ];
        return $this->render('prospect/edit_note.html.twig', $content);
    }

    #[Route(path: '/{prospect}/new', name: 'new', options: ['expose' => true])]
    public function newAction($prospect, Request $request): Response
    {
        $prospect = $this->prospectRepository->findOneBy(['id' => $prospect]);
        $successMessage = "Note created";
            $note = new Note();
            $isNew = true;
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $note->setProspect($prospect);
                $prospect->setStatus($note->getStatus()->getLabel());
                $note->setUser($this->getUser());
                $this->noteRepository->save($note);
                $this->addFlash('success', $successMessage);
                return $this->redirectToRoute('app_prospects_show',[
                    'prospect' => $prospect->getId()
                ]);
            } else {
                $this->addFlash('error', 'Please validate all fields');
            }
        }
            $content = [
                'prospect' => $prospect,
                'form' => $form->createView(),
                'isNew' => $isNew
            ];
        return $this->render('prospect/new_note.html.twig', $content);
    }

}
