<?php

namespace App\Tests;

use App\Entity\Album;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use function sprintf;

class AdminAccessTest extends WebTestCase
{
    private KernelBrowser $client;
    private Album $album;
    private User $user;

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = static::createClient();
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);

        $this->album = $entityManager->getRepository(Album::class)->findOneBy(['name' => 'Test album']);
        $this->user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'user-disabled4@example.com']);
    }

    public function testAccessDenied(): void
    {
        $urls = [
            '/admin/album',
            '/admin/album/add',
            sprintf('/admin/album/update/%d', $this->album->getId()),
            sprintf('/admin/album/delete/%d', $this->album->getId()),
            '/admin/user',
            '/admin/user/add',
            sprintf('/admin/user/delete/%d', $this->user->getId()),
        ];

        foreach ($urls as $url) {
            $this->client->request('GET', $url);
            self::assertResponseRedirects('/login');
        }

        $this->client->request('POST', sprintf('/admin/user/%d/toggle', $this->user->getId()));
        self::assertResponseRedirects('/login');
    }
}
