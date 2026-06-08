<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MonCompteController extends AbstractController
{
    #[Route('/mon-compte', name: 'app_mon_compte')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        $user = $this->getUser();

        return $this->render('mon_compte/index.html.twig', [
            'dossiers' => $user->getDossiers(),
        ]);
    }
}