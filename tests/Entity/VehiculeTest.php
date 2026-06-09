<?php

namespace App\Tests\Entity;

use App\Entity\Vehicule;
use PHPUnit\Framework\TestCase;

class VehiculeTest extends TestCase
{
    public function testGettersEtSetters(): void
    {
        $vehicule = new Vehicule();
        $vehicule->setMarque('Renault');
        $vehicule->setModele('Clio');
        $vehicule->setMotorisation('Essence');
        $vehicule->setKilometrage(50000);
        $vehicule->setType('achat');
        $vehicule->setStatut('disponible');

        $this->assertSame('Renault', $vehicule->getMarque());
        $this->assertSame('Clio', $vehicule->getModele());
        $this->assertSame('Essence', $vehicule->getMotorisation());
        $this->assertSame(50000, $vehicule->getKilometrage());
        $this->assertSame('achat', $vehicule->getType());
        $this->assertSame('disponible', $vehicule->getStatut());
    }

    public function testPrixAchat(): void
    {
        $vehicule = new Vehicule();
        $vehicule->setPrixAchat(12000);

        $this->assertSame(12000, $vehicule->getPrixAchat());
    }

    public function testTypeLocation(): void
    {
        $vehicule = new Vehicule();
        $vehicule->setType('location');
        $vehicule->setPrixLocationMois(300);

        $this->assertSame('location', $vehicule->getType());
        $this->assertSame(300, $vehicule->getPrixLocationMois());
    }

    public function testPhotoEtCreatedAt(): void
    {
        $vehicule = new Vehicule();
        $vehicule->setPhoto('clio.jpg');
        $date = new \DateTimeImmutable();
        $vehicule->setCreatedAt($date);

        $this->assertSame('clio.jpg', $vehicule->getPhoto());
        $this->assertSame($date, $vehicule->getCreatedAt());
    }

    public function testIdEstNullAuDepart(): void
    {
        $vehicule = new Vehicule();
        $this->assertNull($vehicule->getId());
    }

    public function testBasculeType(): void
    {
        $vehicule = new Vehicule();
        $vehicule->setType('achat');
        $this->assertSame('achat', $vehicule->getType());

        // Simulation de la bascule (US10)
        $vehicule->setType('location');
        $this->assertSame('location', $vehicule->getType());
    }
}