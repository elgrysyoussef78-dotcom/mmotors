<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testGettersEtSetters(): void
    {
        $user = new User();
        $user->setEmail('test@mmotors.fr');
        $user->setNom('Wayne');
        $user->setPrenom('Peter');
        $user->setPassword('hashed_password');

        $this->assertSame('test@mmotors.fr', $user->getEmail());
        $this->assertSame('Wayne', $user->getNom());
        $this->assertSame('Peter', $user->getPrenom());
        $this->assertSame('hashed_password', $user->getPassword());
    }

    public function testRolesParDefaut(): void
    {
        $user = new User();

        // Tout utilisateur a au minimum ROLE_USER
        $this->assertContains('ROLE_USER', $user->getRoles());
    }

    public function testRoleAdmin(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);

        $this->assertContains('ROLE_ADMIN', $user->getRoles());
        // ROLE_USER est toujours présent en plus
        $this->assertContains('ROLE_USER', $user->getRoles());
    }

    public function testUserIdentifier(): void
    {
        $user = new User();
        $user->setEmail('identifiant@test.fr');

        $this->assertSame('identifiant@test.fr', $user->getUserIdentifier());
    }

    public function testIdEstNullAuDepart(): void
    {
        $user = new User();
        $this->assertNull($user->getId());
    }

    public function testEraseCredentials(): void
    {
        $user = new User();
        // Cette méthode existe pour l'interface de sécurité, on vérifie qu'elle ne casse rien
        $user->eraseCredentials();
        $this->assertTrue(true);
    }

    public function testAjoutDossier(): void
    {
        $user = new User();
        $dossier = new \App\Entity\Dossier();

        $user->addDossier($dossier);

        $this->assertCount(1, $user->getDossiers());
        $this->assertSame($user, $dossier->getUser());
    }
}