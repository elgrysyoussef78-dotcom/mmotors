<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthenticatedAccessTest extends WebTestCase
{
    public function testClientAccedeAMonCompte(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $clientUser = $userRepository->findOneBy(['email' => 'elgrysyoussef78@gmail.com']);

        $client->loginUser($clientUser);
        $client->request('GET', '/mon-compte');

        $this->assertResponseIsSuccessful();
    }

    public function testClientNePeutPasAccederAdmin(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $clientUser = $userRepository->findOneBy(['email' => 'elgrysyoussef78@gmail.com']);

        $client->loginUser($clientUser);
        $client->request('GET', '/admin/dossiers');

        // Un client (ROLE_USER) ne doit pas accéder à l'admin -> 403
        $this->assertResponseStatusCodeSame(403);
    }

    public function testAdminAccedeListeDossiers(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $adminUser = $userRepository->findOneBy(['email' => 'admin@mmotors.fr']);

        $client->loginUser($adminUser);
        $client->request('GET', '/admin/dossiers');

        $this->assertResponseIsSuccessful();
    }

    public function testAdminAccedeGestionVehicules(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $adminUser = $userRepository->findOneBy(['email' => 'admin@mmotors.fr']);

        $client->loginUser($adminUser);
        $client->request('GET', '/vehicule');

        $this->assertResponseIsSuccessful();
    }

    public function testAdminPeutCreerVehicule(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $adminUser = $userRepository->findOneBy(['email' => 'admin@mmotors.fr']);

        $client->loginUser($adminUser);
        $client->request('GET', '/vehicule/new');

        $this->assertResponseIsSuccessful();
    }
}