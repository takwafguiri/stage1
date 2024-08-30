<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestController extends AbstractController
{
    #[Route('/evacuationsanitaire', name: 'app_evacuationsaitaire')]
    public function index(): Response
    {
        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }

    #[Route('/evacuationsanitaire/inscri', name: 'inscri')]
    public function inscri(): Response
    {
        return $this->render('test/insci.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
    #[Route('/evacuationsanitaire/chirurgie', name: 'chirurgie')]
    public function chirurgie(): Response
    {
        return $this->render('test/chirurgie.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
    #[Route('/evacuationsanitaire/convention', name: 'convention')]
    public function convention(): Response
    {
        return $this->render('test/convention.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
    #[Route('/evacuationsanitaire/blog', name: 'blog')]
    public function blog(): Response
    {
        return $this->render('test/blog.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
    #[Route('/evacuationsanitaire/contactez-nous', name: 'contactez-nous')]
    public function contactez(): Response
    {
        return $this->render('test/contactez-nous.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
    #[Route('/evacuationsanitaire/specialités', name: 'specialités')]
    public function specialités(): Response
    {
        return $this->render('test/specialités.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
    #[Route('/evacuationsanitaire/PMA', name: 'PMA')]
    public function PMA(): Response
    {
        return $this->render('test/PMA.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
    #[Route('/evacuationsanitaire/ORL', name: 'ORL')]
    public function ORL(): Response
    {
        return $this->render('test/ORL.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
    #[Route('/evacuationsanitaire/Gastro', name: 'Gastro')]
    public function Gastro(): Response
    {
        return $this->render('test/Gastro.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
    #[Route('/evacuationsanitaire/Entérologie', name: 'Entérologie')]
    public function Entérologie(): Response
    {
        return $this->render('test/Entérologie.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
    #[Route('/evacuationsanitaire/Oncologie', name: 'Oncologie')]
    public function Oncologie(): Response
    {
        return $this->render('test/Oncologie.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
    #[Route('/evacuationsanitaire/Chirurgie-générale', name: 'Chirurgie-générale')]
    public function Chirurgiegénérale(): Response
    {
        return $this->render('test/Chirurgie-générale.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
    #[Route('/evacuationsanitaire/Pédiatrique', name: 'Pédiatrique')]
    public function Pédiatrique(): Response
    {
        return $this->render('test/Pédiatrique.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
    #[Route('/evacuationsanitaire/Neurochirurgie', name: 'Neurochirurgie')]
    public function Neurochirurgie(): Response
    {
        return $this->render('test/Neurochirurgie.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
    #[Route('/evacuationsanitaire/Orthopédie', name: 'Orthopédie')]
    public function Orthopédie(): Response
    {
        return $this->render('test/Orthopédie.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }

    #[Route('/evacuationsanitaire/Gynécologie', name: 'Gynécologie')]
    public function Gynécologie(): Response
    {
        return $this->render('test/Gynécologie.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
    #[Route('/evacuationsanitaire/Chirurgie-dentaire', name: 'Chirurgie-dentaire')]
    public function Chirurgiedentaire(): Response
    {
        return $this->render('test/Chirurgie-dentaire.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
    #[Route('/evacuationsanitaire/Ophtalmologie', name: 'Ophtalmologie')]
    public function Ophtalmologie(): Response
    {
        return $this->render('test/Ophtalmologie.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
    #[Route('/evacuationsanitaire/Cardiologie-Pneumologie', name: 'Cardiologie-Pneumologie')]
    public function CardiologiePneumologie(): Response
    {
        return $this->render('test/Cardiologie-Pneumologie.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
    #[Route('/evacuationsanitaire/article1', name: 'article1')]
    public function article1(): Response
    {
        return $this->render('test/article1.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
    #[Route('/evacuationsanitaire/article2', name: 'article2')]
    public function article2(): Response
    {
        return $this->render('test/article2.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
    #[Route('/evacuationsanitaire/article3', name: 'article3')]
    public function article3(): Response
    {
        return $this->render('test/article3.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }


}
