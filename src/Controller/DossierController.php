<?php

namespace App\Controller;

use App\Entity\Dossier;
use App\Entity\Vehicule;
use App\Form\DossierType;
use App\Entity\Document;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\DossierRepository;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

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

            // On enregistre les options uniquement si c'est une location
            $optionsChoisies = $form->get('options')->getData();
            if ($dossier->getType() === 'location' && !empty($optionsChoisies)) {
                $dossier->setOptions(implode(', ', $optionsChoisies));
            }

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

    #[Route('/admin/dossier/{id}', name: 'app_admin_dossier_show')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminShow(Dossier $dossier): Response
    {
        return $this->render('dossier/admin_show.html.twig', [
            'dossier' => $dossier,
        ]);
    }

    #[Route('/dossier/{id}/documents', name: 'app_dossier_documents')]
    #[IsGranted('ROLE_USER')]
    public function documents(Dossier $dossier, Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        // Sécurité : un client ne peut gérer que SES propres dossiers
        if ($dossier->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Ce dossier ne vous appartient pas.');
        }

        if ($request->isMethod('POST')) {
            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $fichier */
            $fichier = $request->files->get('document');

            if ($fichier) {
                // Validation du type (PDF et images uniquement)
                $typesAutorises = ['application/pdf', 'image/jpeg', 'image/png'];
                if (!in_array($fichier->getMimeType(), $typesAutorises)) {
                    $this->addFlash('danger', 'Type de fichier non autorisé (PDF, JPG, PNG seulement).');
                    return $this->redirectToRoute('app_dossier_documents', ['id' => $dossier->getId()]);
                }

                // Validation de la taille (max 5 Mo)
                if ($fichier->getSize() > 5 * 1024 * 1024) {
                    $this->addFlash('danger', 'Fichier trop volumineux (5 Mo maximum).');
                    return $this->redirectToRoute('app_dossier_documents', ['id' => $dossier->getId()]);
                }

                // On crée un nom de fichier propre et unique
                $nomOriginal = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
                $nomSecurise = $slugger->slug($nomOriginal);
                $nouveauNom = $nomSecurise . '-' . uniqid() . '.' . $fichier->guessExtension();

                // On déplace le fichier dans public/uploads/documents
                try {
                    $fichier->move($this->getParameter('dossier_documents'), $nouveauNom);
                } catch (FileException $e) {
                    $this->addFlash('danger', "Erreur lors de l'envoi du fichier.");
                    return $this->redirectToRoute('app_dossier_documents', ['id' => $dossier->getId()]);
                }

                // On enregistre la trace en base
                $document = new Document();
                $document->setNomFichier($fichier->getClientOriginalName());
                $document->setChemin($nouveauNom);
                $document->setUploadedAt(new \DateTimeImmutable());
                $document->setDossier($dossier);

                $em->persist($document);
                $em->flush();

                $this->addFlash('success', 'Document ajouté avec succès.');
            }

            return $this->redirectToRoute('app_dossier_documents', ['id' => $dossier->getId()]);
        }

        return $this->render('dossier/documents.html.twig', [
            'dossier' => $dossier,
        ]);
    }


}