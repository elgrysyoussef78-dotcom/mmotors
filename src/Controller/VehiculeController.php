<?php

namespace App\Controller;

use App\Entity\Vehicule;
use App\Form\VehiculeType;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/vehicule')]
final class VehiculeController extends AbstractController
{
    #[Route(name: 'app_vehicule_index', methods: ['GET'])]
    public function index(VehiculeRepository $vehiculeRepository): Response
    {
        return $this->render('vehicule/index.html.twig', [
            'vehicules' => $vehiculeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_vehicule_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $vehicule = new Vehicule();
        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('photoFile')->getData();
            if ($photoFile) {
                $typesAutorises = ['image/jpeg', 'image/png', 'image/webp'];
                if (in_array($photoFile->getMimeType(), $typesAutorises) && $photoFile->getSize() <= 5 * 1024 * 1024) {
                    $nomSecurise = $slugger->slug(pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME));
                    $nouveauNom = $nomSecurise . '-' . uniqid() . '.' . $photoFile->guessExtension();
                    try {
                        $photoFile->move($this->getParameter('dossier_vehicules'), $nouveauNom);
                        $vehicule->setPhoto($nouveauNom);
                    } catch (FileException $e) {
                        $this->addFlash('danger', "Erreur lors de l'envoi de la photo.");
                    }
                } else {
                    $this->addFlash('danger', 'Photo invalide (JPG, PNG, WEBP — 5 Mo max).');
                }
            }

            $vehicule->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($vehicule);
            $entityManager->flush();

            return $this->redirectToRoute('app_vehicule_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vehicule/new.html.twig', [
            'vehicule' => $vehicule,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_vehicule_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Vehicule $vehicule, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('photoFile')->getData();
            if ($photoFile) {
                $typesAutorises = ['image/jpeg', 'image/png', 'image/webp'];
                if (in_array($photoFile->getMimeType(), $typesAutorises) && $photoFile->getSize() <= 5 * 1024 * 1024) {
                    $nomSecurise = $slugger->slug(pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME));
                    $nouveauNom = $nomSecurise . '-' . uniqid() . '.' . $photoFile->guessExtension();
                    try {
                        $photoFile->move($this->getParameter('dossier_vehicules'), $nouveauNom);
                        $vehicule->setPhoto($nouveauNom);
                    } catch (FileException $e) {
                        $this->addFlash('danger', "Erreur lors de l'envoi de la photo.");
                    }
                } else {
                    $this->addFlash('danger', 'Photo invalide (JPG, PNG, WEBP — 5 Mo max).');
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_vehicule_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vehicule/edit.html.twig', [
            'vehicule' => $vehicule,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_vehicule_show', methods: ['GET'])]
    public function show(Vehicule $vehicule): Response
    {
        return $this->render('vehicule/show.html.twig', [
            'vehicule' => $vehicule,
        ]);
    }


    #[Route('/{id}', name: 'app_vehicule_delete', methods: ['POST'])]
    public function delete(Request $request, Vehicule $vehicule, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $vehicule->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($vehicule);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_vehicule_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/basculer', name: 'app_vehicule_basculer', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function basculer(Vehicule $vehicule, EntityManagerInterface $entityManager): Response
    {
        // Si le véhicule est en achat, on le passe en location, et inversement
        if ($vehicule->getType() === 'achat') {
            $vehicule->setType('location');
        } else {
            $vehicule->setType('achat');
        }

        $entityManager->flush();

        $this->addFlash('success', 'Le type du véhicule a été basculé.');

        return $this->redirectToRoute('app_vehicule_index');
    }
}
