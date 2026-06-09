<?php

namespace App\Tests\Entity;

use App\Entity\Dossier;
use App\Entity\User;
use App\Entity\Vehicule;
use PHPUnit\Framework\TestCase;

class DossierTest extends TestCase
{
    public function testCreationDossier(): void
    {
        $dossier = new Dossier();
        $dossier->setType('achat');
        $dossier->setStatut('en_attente');

        $this->assertSame('achat', $dossier->getType());
        $this->assertSame('en_attente', $dossier->getStatut());
    }

    public function testChangementStatutValide(): void
    {
        $dossier = new Dossier();
        $dossier->setStatut('en_attente');

        // L'admin valide le dossier
        $dossier->setStatut('valide');

        $this->assertSame('valide', $dossier->getStatut());
    }

    public function testChangementStatutRefuse(): void
    {
        $dossier = new Dossier();
        $dossier->setStatut('en_attente');

        // L'admin refuse le dossier
        $dossier->setStatut('refuse');

        $this->assertSame('refuse', $dossier->getStatut());
    }

    public function testRelationUser(): void
    {
        $user = new User();
        $user->setEmail('client@test.fr');

        $dossier = new Dossier();
        $dossier->setUser($user);

        $this->assertSame($user, $dossier->getUser());
        $this->assertSame('client@test.fr', $dossier->getUser()->getEmail());
    }

    public function testRelationVehicule(): void
    {
        $vehicule = new Vehicule();
        $vehicule->setMarque('Peugeot');

        $dossier = new Dossier();
        $dossier->setVehicule($vehicule);

        $this->assertSame($vehicule, $dossier->getVehicule());
        $this->assertSame('Peugeot', $dossier->getVehicule()->getMarque());
    }

    public function testCreatedAt(): void
    {
        $dossier = new Dossier();
        $date = new \DateTimeImmutable();
        $dossier->setCreatedAt($date);

        $this->assertSame($date, $dossier->getCreatedAt());
    }

    public function testIdEstNullAuDepart(): void
    {
        $dossier = new Dossier();
        $this->assertNull($dossier->getId());
    }

    public function testAjoutDocument(): void
    {
        $dossier = new Dossier();
        $document = new \App\Entity\Document();
        $document->setNomFichier('test.pdf');

        $dossier->addDocument($document);

        $this->assertCount(1, $dossier->getDocuments());
        $this->assertTrue($dossier->getDocuments()->contains($document));
    }
}