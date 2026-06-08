<?php

namespace App\Controller;

use App\Entity\Dossier;
use App\Entity\Vehicule;
use App\Form\DossierType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\DossierRepository;

class DossierController extends AbstractController
{
    #[Route('/dossier/nouveau/{id}', name: 'app_dossier_new')]
    #[IsGranted('ROLE_USER')]
    public function new(Vehicule $vehicule, Request $request, EntityManagerInterface $em): Response
    {
        $dossier = new Dossier();
        $form = $this->createForm(DossierType::class, $dossier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dossier->setUser($this->getUser());
            $dossier->setVehicule($vehicule);
            $dossier->setStatut('en_attente');
            $dossier->setCreatedAt(new \DateTimeImmutable());

            $em->persist($dossier);
            $em->flush();

            $this->addFlash('success', 'Votre dossier a bien été déposé.');

            return $this->redirectToRoute('app_mon_compte');
        }

        return $this->render('dossier/new.html.twig', [
            'form' => $form,
            'vehicule' => $vehicule,
        ]);
    }

    #[Route('/admin/dossiers', name: 'app_admin_dossiers')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminList(DossierRepository $dossierRepository): Response
    {
        return $this->render('dossier/admin_list.html.twig', [
            'dossiers' => $dossierRepository->findAll(),
        ]);
    }

    #[Route('/admin/dossier/{id}/valider', name: 'app_admin_dossier_valider')]
    #[IsGranted('ROLE_ADMIN')]
    public function valider(Dossier $dossier, EntityManagerInterface $em): Response
    {
        $dossier->setStatut('valide');
        $em->flush();

        $this->addFlash('success', 'Le dossier a été validé.');

        return $this->redirectToRoute('app_admin_dossiers');
    }

    #[Route('/admin/dossier/{id}/refuser', name: 'app_admin_dossier_refuser')]
    #[IsGranted('ROLE_ADMIN')]
    public function refuser(Dossier $dossier, EntityManagerInterface $em): Response
    {
        $dossier->setStatut('refuse');
        $em->flush();

        $this->addFlash('success', 'Le dossier a été refusé.');

        return $this->redirectToRoute('app_admin_dossiers');
    }
}