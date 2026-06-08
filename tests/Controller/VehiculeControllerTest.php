<?php

namespace App\Tests\Controller;

use App\Entity\Vehicule;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class VehiculeControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;

    /** @var EntityRepository<Vehicule> */
    private EntityRepository $vehiculeRepository;
    private string $path = '/vehicule/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->vehiculeRepository = $this->manager->getRepository(Vehicule::class);

        foreach ($this->vehiculeRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Vehicule index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first()->text());
    }

    public function testNew(): void
    {
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'vehicule[marque]' => 'Testing',
            'vehicule[modele]' => 'Testing',
            'vehicule[motorisation]' => 'Testing',
            'vehicule[kilometrage]' => 'Testing',
            'vehicule[prixAchat]' => 'Testing',
            'vehicule[prixLocationMois]' => 'Testing',
            'vehicule[type]' => 'Testing',
            'vehicule[statut]' => 'Testing',
            'vehicule[photo]' => 'Testing',
            'vehicule[createdAt]' => 'Testing',
        ]);

        self::assertResponseRedirects('/vehicule');

        self::assertSame(1, $this->vehiculeRepository->count([]));

        $this->markTestIncomplete('This test was generated');
    }

    public function testShow(): void
    {
        $fixture = new Vehicule();
        $fixture->setMarque('My Title');
        $fixture->setModele('My Title');
        $fixture->setMotorisation('My Title');
        $fixture->setKilometrage('My Title');
        $fixture->setPrixAchat('My Title');
        $fixture->setPrixLocationMois('My Title');
        $fixture->setType('My Title');
        $fixture->setStatut('My Title');
        $fixture->setPhoto('My Title');
        $fixture->setCreatedAt('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Vehicule');

        // Use assertions to check that the properties are properly displayed.
        $this->markTestIncomplete('This test was generated');
    }

    public function testEdit(): void
    {
        $fixture = new Vehicule();
        $fixture->setMarque('Value');
        $fixture->setModele('Value');
        $fixture->setMotorisation('Value');
        $fixture->setKilometrage('Value');
        $fixture->setPrixAchat('Value');
        $fixture->setPrixLocationMois('Value');
        $fixture->setType('Value');
        $fixture->setStatut('Value');
        $fixture->setPhoto('Value');
        $fixture->setCreatedAt('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'vehicule[marque]' => 'Something New',
            'vehicule[modele]' => 'Something New',
            'vehicule[motorisation]' => 'Something New',
            'vehicule[kilometrage]' => 'Something New',
            'vehicule[prixAchat]' => 'Something New',
            'vehicule[prixLocationMois]' => 'Something New',
            'vehicule[type]' => 'Something New',
            'vehicule[statut]' => 'Something New',
            'vehicule[photo]' => 'Something New',
            'vehicule[createdAt]' => 'Something New',
        ]);

        self::assertResponseRedirects('/vehicule');

        $fixture = $this->vehiculeRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getMarque());
        self::assertSame('Something New', $fixture[0]->getModele());
        self::assertSame('Something New', $fixture[0]->getMotorisation());
        self::assertSame('Something New', $fixture[0]->getKilometrage());
        self::assertSame('Something New', $fixture[0]->getPrixAchat());
        self::assertSame('Something New', $fixture[0]->getPrixLocationMois());
        self::assertSame('Something New', $fixture[0]->getType());
        self::assertSame('Something New', $fixture[0]->getStatut());
        self::assertSame('Something New', $fixture[0]->getPhoto());
        self::assertSame('Something New', $fixture[0]->getCreatedAt());

        $this->markTestIncomplete('This test was generated');
    }

    public function testRemove(): void
    {
        $fixture = new Vehicule();
        $fixture->setMarque('Value');
        $fixture->setModele('Value');
        $fixture->setMotorisation('Value');
        $fixture->setKilometrage('Value');
        $fixture->setPrixAchat('Value');
        $fixture->setPrixLocationMois('Value');
        $fixture->setType('Value');
        $fixture->setStatut('Value');
        $fixture->setPhoto('Value');
        $fixture->setCreatedAt('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/vehicule');
        self::assertSame(0, $this->vehiculeRepository->count([]));

        $this->markTestIncomplete('This test was generated');
    }
}
