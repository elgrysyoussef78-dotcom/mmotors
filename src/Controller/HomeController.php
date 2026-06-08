<?php

namespace App\Controller;

use App\Entity\Vehicule;
use App\Repository\VehiculeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, VehiculeRepository $vehiculeRepository): Response
    {
        // On récupère le filtre choisi dans l'URL (?type=achat ou ?type=location)
        $type = $request->query->get('type');

        if ($type === 'achat' || $type === 'location') {
            $vehicules = $vehiculeRepository->findBy(['type' => $type]);
        } else {
            $vehicules = $vehiculeRepository->findAll();
        }

        return $this->render('home/index.html.twig', [
            'vehicules' => $vehicules,
            'typeActuel' => $type,
        ]);
    }

    #[Route('/voiture/{id}', name: 'app_voiture_show')]
    public function show(Vehicule $vehicule): Response
    {
        return $this->render('home/show.html.twig', [
            'vehicule' => $vehicule,
        ]);
    }
}