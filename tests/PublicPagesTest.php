<?php

namespace App\Tests;

use App\Entity\Album;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PublicPagesTest extends WebTestCase
{
    private KernelBrowser $client;
    private Album $album;
    private User $userEnabled;
    private User $userDisabled;

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = static::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);

        $this->album = $entityManager->getRepository(Album::class)->findOneBy(['name' => 'Test album 2']);
        $this->userEnabled = $entityManager->getRepository(User::class)->findOneBy(['email' => 'user-enabled1@example.com']);
        $this->userDisabled = $entityManager->getRepository(User::class)->findOneBy(['email' => 'user-disabled1@example.com']);
    }

    public function testCountLinks(): void
    {
        $this->client->request('GET', '/');

        self::assertSelectorCount(6, 'a');

        self::assertAnySelectorTextContains('a', 'Invités');
        self::assertAnySelectorTextContains('a', 'Portfolio');
        self::assertAnySelectorTextContains('a', 'Qui suis-je ?');
        self::assertAnySelectorTextContains('a', 'Connexion');
        self::assertAnySelectorTextContains('a', 'découvrir');
    }

    public function testGuestAlbums(): void
    {
        $this->client->request('GET', '/guests');
        self::assertSelectorTextContains('h3.mb-5', 'Invités');
        self::assertSelectorExists('.guest.py-5.d-flex.justify-content-between.align-items-center');
    }

    public function testGuestAlbumAccess(): void
    {
        $this->client->request('GET', '/guest/' . $this->userEnabled->getId());
        self::assertEquals('/guest/' . $this->userEnabled->getId(), $this->client->getRequest()->getPathInfo());

        $this->client->request('GET', '/guest/' . $this->userDisabled->getId());
        self::assertResponseRedirects('/');
    }

    public function testGuestAlbum(): void
    {
        $this->client->request('GET', '/guest/' . $this->userEnabled->getId());
        self::assertSelectorTextContains('h3.mb-4', $this->userEnabled->getName());
        self::assertSelectorTextContains('p.mb-5.w-100', $this->userEnabled->getDescription());
    }

    public function testPortfolio(): void
    {
        $this->client->request('GET', '/portfolio');
        self::assertSelectorTextContains('h3.mb-4', 'Portfolio');
        self::assertSelectorTextContains('a.btn.w-100.py-3.active', 'Toutes');

        $this->client->request('GET', '/portfolio/' . $this->album->getId());
        self::assertSelectorTextContains('a.btn.w-100.py-3.active', 'Test album 2');
    }

    public function testAbout(): void
    {
        $this->client->request('GET', '/about');
        self::assertSelectorTextContains('h2.about-title', 'Qui suis-je ?');
    }
}
