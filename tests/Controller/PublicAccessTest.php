<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PublicAccessTest extends WebTestCase
{
    public function testPageAccueilRepond(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        // La page d'accueil doit être accessible à tous (code 200)
        $this->assertResponseIsSuccessful();
    }

    public function testPageLoginRepond(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
    }

    public function testPageInscriptionRepond(): void
    {
        $client = static::createClient();
        $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
    }

    public function testAdminDossiersInterditAuVisiteur(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/dossiers');

        // Un visiteur non connecté doit être redirigé (302) vers le login
        $this->assertResponseRedirects();
    }

    public function testBackOfficeVehiculeInterditAuVisiteur(): void
    {
        $client = static::createClient();
        $client->request('GET', '/vehicule');

        // Réservé admin : un visiteur est redirigé vers le login
        $this->assertResponseRedirects();
    }
}