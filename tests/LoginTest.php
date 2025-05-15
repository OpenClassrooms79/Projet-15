<?php
/**
 * @author Jocelyn Flament
 * @since 14/05/2025
 */

namespace App\Tests;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->userRepository = $this->entityManager->getRepository(User::class);
    }

    public function testLoginSuccess(): void
    {
        // aller sur la page d'authentification, remplir et soumettre le formulaire
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Connexion')->form([
            '_username' => 'user-enabled1@example.com',
            '_password' => 'password',
        ]);
        $this->client->submit($form);
        self::assertResponseRedirects('/admin/media');
        $this->client->followRedirect();
    }

    public function testThatLoginShouldFail(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Connexion')->form([
            '_username' => 'user-enabled1@example.com',
            '_password' => 'wrong-password',
        ]);
        $this->client->submit($form);

        // pas de redirection
        self::assertResponseRedirects('/login');
    }

    public function testLogout(): void
    {
        $user = $this->userRepository->findOneBy(['email' => 'user-enabled8@example.com']);

        $this->client->loginUser($user);

        $this->client->request('GET', '/logout');
        self::assertResponseRedirects('/');
        $this->client->followRedirect();
        self::assertSelectorExists('a[href="/login"]');
    }
}