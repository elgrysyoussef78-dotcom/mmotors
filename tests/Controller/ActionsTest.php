<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use App\Repository\VehiculeRepository;
use App\Repository\DossierRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ActionsTest extends WebTestCase
{
    public function testAdminBasculeVehicule(): void
    {
        $client = static::createClient();
        $userRepo = static::getContainer()->get(UserRepository::class);
        $vehiculeRepo = static::getContainer()->get(VehiculeRepository::class);

        $admin = $userRepo->findOneBy(['email' => 'admin@mmotors.fr']);
        $client->loginUser($admin);

        $vehicule = $vehiculeRepo->findOneBy([]);
        $client->request('GET', '/vehicule/' . $vehicule->getId() . '/basculer');

        // Après bascule, redirection vers la liste
        $this->assertResponseRedirects();
    }

    public function testAdminVoitFicheVehicule(): void
    {
        $client = static::createClient();
        $userRepo = static::getContainer()->get(UserRepository::class);
        $vehiculeRepo = static::getContainer()->get(VehiculeRepository::class);

        $admin = $userRepo->findOneBy(['email' => 'admin@mmotors.fr']);
        $client->loginUser($admin);

        $vehicule = $vehiculeRepo->findOneBy([]);
        $client->request('GET', '/vehicule/' . $vehicule->getId());

        $this->assertResponseIsSuccessful();
    }

    public function testFichePubliqueVehicule(): void
    {
        $client = static::createClient();
        $vehiculeRepo = static::getContainer()->get(VehiculeRepository::class);

        $vehicule = $vehiculeRepo->findOneBy([]);
        $client->request('GET', '/voiture/' . $vehicule->getId());

        $this->assertResponseIsSuccessful();
    }

    public function testAccueilAvecFiltreAchat(): void
    {
        $client = static::createClient();
        $client->request('GET', '/?type=achat');
        $this->assertResponseIsSuccessful();
    }

    public function testAccueilAvecFiltreLocation(): void
    {
        $client = static::createClient();
        $client->request('GET', '/?type=location');
        $this->assertResponseIsSuccessful();
    }

    public function testClientAccedeFormulaireDepot(): void
    {
        $client = static::createClient();
        $userRepo = static::getContainer()->get(UserRepository::class);
        $vehiculeRepo = static::getContainer()->get(VehiculeRepository::class);

        $clientUser = $userRepo->findOneBy(['email' => 'elgrysyoussef78@gmail.com']);
        $client->loginUser($clientUser);

        $vehicule = $vehiculeRepo->findOneBy([]);
        $client->request('GET', '/dossier/nouveau/' . $vehicule->getId());

        $this->assertResponseIsSuccessful();
    }

    public function testAdminValideUnDossier(): void
    {
        $client = static::createClient();
        $userRepo = static::getContainer()->get(UserRepository::class);
        $dossierRepo = static::getContainer()->get(DossierRepository::class);

        $admin = $userRepo->findOneBy(['email' => 'admin@mmotors.fr']);
        $client->loginUser($admin);

        $dossier = $dossierRepo->findOneBy([]);
        if ($dossier) {
            $client->request('GET', '/admin/dossier/' . $dossier->getId() . '/valider');
            $this->assertResponseRedirects();
        } else {
            $this->markTestSkipped('Aucun dossier en base de test.');
        }
    }

    public function testAdminRefuseUnDossier(): void
    {
        $client = static::createClient();
        $userRepo = static::getContainer()->get(UserRepository::class);
        $dossierRepo = static::getContainer()->get(DossierRepository::class);

        $admin = $userRepo->findOneBy(['email' => 'admin@mmotors.fr']);
        $client->loginUser($admin);

        $dossier = $dossierRepo->findOneBy([]);
        if ($dossier) {
            $client->request('GET', '/admin/dossier/' . $dossier->getId() . '/refuser');
            $this->assertResponseRedirects();
        } else {
            $this->markTestSkipped('Aucun dossier en base de test.');
        }
    }

    public function testClientDeposeUnDossier(): void
    {
        $client = static::createClient();
        $userRepo = static::getContainer()->get(UserRepository::class);
        $vehiculeRepo = static::getContainer()->get(VehiculeRepository::class);

        $clientUser = $userRepo->findOneBy(['email' => 'elgrysyoussef78@gmail.com']);
        $client->loginUser($clientUser);

        $vehicule = $vehiculeRepo->findOneBy([]);
        $crawler = $client->request('GET', '/dossier/nouveau/' . $vehicule->getId());

        // On soumet le formulaire de dépôt
        $client->submitForm('Envoyer ma demande', [
            'dossier[type]' => 'achat',
        ]);

        $this->assertResponseRedirects();
    }

    public function testAdminCreeUnVehicule(): void
    {
        $client = static::createClient();
        $userRepo = static::getContainer()->get(UserRepository::class);

        $admin = $userRepo->findOneBy(['email' => 'admin@mmotors.fr']);
        $client->loginUser($admin);

        $client->request('GET', '/vehicule/new');

        $client->submitForm('Créer', [
            'vehicule[marque]' => 'Toyota',
            'vehicule[modele]' => 'Yaris',
            'vehicule[motorisation]' => 'Hybride',
            'vehicule[kilometrage]' => '20000',
            'vehicule[type]' => 'achat',
            'vehicule[statut]' => 'disponible',
        ]);

        $this->assertResponseRedirects();
    }

    public function testClientAccedePageDocuments(): void
    {
        $client = static::createClient();
        $userRepo = static::getContainer()->get(UserRepository::class);
        $dossierRepo = static::getContainer()->get(DossierRepository::class);

        $clientUser = $userRepo->findOneBy(['email' => 'elgrysyoussef78@gmail.com']);
        $client->loginUser($clientUser);

        // On prend un dossier appartenant à ce client
        $dossier = $dossierRepo->findOneBy(['user' => $clientUser]);
        if ($dossier) {
            $client->request('GET', '/dossier/' . $dossier->getId() . '/documents');
            $this->assertResponseIsSuccessful();
        } else {
            $this->markTestSkipped('Aucun dossier pour ce client en base de test.');
        }
    }

    public function testUploadDocumentValide(): void
    {
        $client = static::createClient();
        $userRepo = static::getContainer()->get(UserRepository::class);
        $dossierRepo = static::getContainer()->get(DossierRepository::class);

        $clientUser = $userRepo->findOneBy(['email' => 'elgrysyoussef78@gmail.com']);
        $client->loginUser($clientUser);

        $dossier = $dossierRepo->findOneBy(['user' => $clientUser]);
        if (!$dossier) {
            $this->markTestSkipped('Aucun dossier pour ce client.');
        }

        // On crée un faux fichier PDF temporaire
        $cheminTemp = sys_get_temp_dir() . '/test_doc.pdf';
        file_put_contents($cheminTemp, '%PDF-1.4 test');

        $uploadedFile = new \Symfony\Component\HttpFoundation\File\UploadedFile(
            $cheminTemp,
            'test_doc.pdf',
            'application/pdf',
            null,
            true
        );

        $client->request('POST', '/dossier/' . $dossier->getId() . '/documents', [], [
            'document' => $uploadedFile,
        ]);

        $this->assertResponseRedirects();
    }

    public function testAdminVoitDetailDossier(): void
    {
        $client = static::createClient();
        $userRepo = static::getContainer()->get(\App\Repository\UserRepository::class);
        $dossierRepo = static::getContainer()->get(\App\Repository\DossierRepository::class);

        $admin = $userRepo->findOneBy(['email' => 'admin@mmotors.fr']);
        $client->loginUser($admin);

        $dossier = $dossierRepo->findOneBy([]);
        if ($dossier) {
            $client->request('GET', '/admin/dossier/' . $dossier->getId());
            $this->assertResponseIsSuccessful();
        } else {
            $this->markTestSkipped('Aucun dossier en base de test.');
        }
    }

    public function testAdminModifieVehicule(): void
    {
        $client = static::createClient();
        $userRepo = static::getContainer()->get(\App\Repository\UserRepository::class);
        $vehiculeRepo = static::getContainer()->get(\App\Repository\VehiculeRepository::class);

        $admin = $userRepo->findOneBy(['email' => 'admin@mmotors.fr']);
        $client->loginUser($admin);

        $vehicule = $vehiculeRepo->findOneBy([]);
        $client->request('GET', '/vehicule/' . $vehicule->getId() . '/edit');
        $this->assertResponseIsSuccessful();

        $client->submitForm('Mettre à jour', [
            'vehicule[marque]' => 'Renault',
            'vehicule[modele]' => 'Mégane',
            'vehicule[motorisation]' => 'Diesel',
            'vehicule[kilometrage]' => '45000',
            'vehicule[type]' => 'achat',
            'vehicule[statut]' => 'disponible',
        ]);
        $this->assertResponseRedirects();
    }

    public function testClientDeposeDossierLocationAvecOptions(): void
    {
        $client = static::createClient();
        $userRepo = static::getContainer()->get(\App\Repository\UserRepository::class);
        $vehiculeRepo = static::getContainer()->get(\App\Repository\VehiculeRepository::class);

        $clientUser = $userRepo->findOneBy(['email' => 'elgrysyoussef78@gmail.com']);
        $client->loginUser($clientUser);

        $vehicule = $vehiculeRepo->findOneBy([]);
        $client->request('GET', '/dossier/nouveau/' . $vehicule->getId());

        $client->submitForm('Envoyer ma demande', [
            'dossier[type]' => 'location',
        ]);
        $this->assertResponseRedirects();
    }
}