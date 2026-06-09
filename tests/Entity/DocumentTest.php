<?php

namespace App\Tests\Entity;

use App\Entity\Document;
use App\Entity\Dossier;
use PHPUnit\Framework\TestCase;

class DocumentTest extends TestCase
{
    public function testGettersEtSetters(): void
    {
        $document = new Document();
        $document->setNomFichier('carte_identite.pdf');
        $document->setChemin('carte-identite-abc123.pdf');
        $date = new \DateTimeImmutable();
        $document->setUploadedAt($date);

        $this->assertSame('carte_identite.pdf', $document->getNomFichier());
        $this->assertSame('carte-identite-abc123.pdf', $document->getChemin());
        $this->assertSame($date, $document->getUploadedAt());
    }

    public function testRelationDossier(): void
    {
        $dossier = new Dossier();
        $dossier->setType('location');

        $document = new Document();
        $document->setDossier($dossier);

        $this->assertSame($dossier, $document->getDossier());
        $this->assertSame('location', $document->getDossier()->getType());
    }
}