<?php

namespace App\Tests;

use App\Entity\Album;
use App\Entity\Media;
use App\Entity\User;
use App\Repository\AlbumRepository;
use App\Repository\MediaRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use function sprintf;
use function time;

class AdminAccessTest extends WebTestCase
{
    private KernelBrowser $client;
    private Album $album;
    private Media $media;
    private User $user;
    private User $admin;
    private EntityManagerInterface $entityManager;
    private AlbumRepository $albumRepository;
    private MediaRepository $mediaRepository;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = static::createClient();

        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get(EntityManagerInterface::class);
        $this->entityManager = $em;

        /** @var AlbumRepository $albumRepository */
        $albumRepository = self::getContainer()->get(AlbumRepository::class);
        $this->albumRepository = $albumRepository;

        /** @var MediaRepository $mediaRepository */
        $mediaRepository = self::getContainer()->get(MediaRepository::class);
        $this->mediaRepository = $mediaRepository;

        /** @var UserRepository $userRepository */
        $userRepository = self::getContainer()->get(UserRepository::class);
        $this->userRepository = $userRepository;

        $this->album = $this->albumRepository->findOneBy(['name' => 'Test album 1']);
        $this->media = $this->mediaRepository->findOneBy([]); // récupérer un enregistrement quelconque
        $this->user = $this->userRepository->findOneBy(['email' => 'user-enabled4@example.com']);
        $this->admin = $this->userRepository->findOneBy(['email' => 'admin-enabled2@example.com']);
    }

    public function testAccessDenied(): void
    {
        $urls = [
            '/admin/album',
            '/admin/album/add',
            sprintf('/admin/album/update/%d', $this->album->getId()),
            sprintf('/admin/album/delete/%d', $this->album->getId()),
            '/admin/media/',
            '/admin/media/add',
            sprintf('/admin/media/delete/%d', $this->media->getId()),
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

    public function testAlbumIndexByAdmin(): void
    {
        $this->client->loginUser($this->admin);
        $this->client->request('GET', '/admin/album');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('a.btn.btn-primary', 'Ajouter');
        self::assertSelectorTextContains('a.btn.btn-warning', 'Modifier');
        self::assertSelectorTextContains('a.btn.btn-danger', 'Supprimer');

        $this->client->request('GET', '/admin/album/update/' . $this->album->getId());
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('button.btn.btn-primary', 'Modifier');
        self::assertSelectorTextContains('a.btn.btn-primary', 'Retour');
    }

    public function testAlbumAddAndUpdateByAdmin(): void
    {
        $this->client->loginUser($this->admin);
        $crawler = $this->client->request('GET', '/admin/album/add');
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('FORM[name=album]');
        $button = $crawler->selectButton('Ajouter');
        if ($button->count() === 0) {
            throw new RuntimeException("Le bouton 'Ajouter' n'a pas été trouvé !");
        }

        // remplir et soumettre le formulaire d’ajout d'album
        $album_name = 'Album de test ' . time();
        $form = $crawler->selectButton('Ajouter')->form([
            'album[name]' => $album_name,
        ]);
        $this->client->submit($form);

        // vérifier que la redirection fonctionne
        self::assertResponseRedirects();
        $this->client->followRedirect();

        // Vérifier que l'album a bien été ajouté dans la base de données
        $newAlbum = $this->albumRepository->findOneBy([
            'name' => $album_name,
        ]);

        self::assertNotNull($newAlbum, "L'album n'a pas été trouvé dans la base de données.");
        self::assertEquals($album_name, $newAlbum->getName(), "Le nom de l'album ajouté est mal enregistré dans la base de données.");


        // mise à jour d'un album
        $crawler = $this->client->request('GET', '/admin/album/update/' . $newAlbum->getId());
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('FORM[name=album]');
        $button = $crawler->selectButton('Modifier');
        if ($button->count() === 0) {
            throw new RuntimeException("Le bouton 'Modifier' n'a pas été trouvé !");
        }

        // remplir et soumettre le formulaire de modification d'album
        $album_name = 'Album de test modifié ' . time();
        $form = $crawler->selectButton('Modifier')->form([
            'album[name]' => $album_name,
        ]);
        $this->client->submit($form);

        // vérifier que la redirection fonctionne
        self::assertResponseRedirects();
        $this->client->followRedirect();

        // Vérifier que l'album a bien été modifié dans la base de données
        $newAlbumModified = $this->albumRepository->find($newAlbum->getId());
        $this->entityManager->refresh($newAlbumModified); // force Doctrine à vérifier de nouveau l'enregistrement en base de données

        self::assertNotNull($newAlbumModified, "L'album modifié n'a pas été trouvé dans la base de données.");
        self::assertEquals($album_name, $newAlbumModified->getName(), "Le nom de l'album modifié est mal enregistré dans la base de données.");

        $this->album = $newAlbumModified;
    }

    public function testDeleteAlbumByAdmin(): void
    {
        // enregistrement d'un nouvel album
        $album = new Album();
        $album->setName('Album de test');
        $this->entityManager->persist($album);
        $this->entityManager->flush();

        // suppression de l'album
        $this->client->loginUser($this->admin);
        $this->client->request('GET', '/admin/album/delete/' . $album->getId());
        self::assertResponseRedirects();
        self::assertResponseRedirects(expectedLocation: '/admin/album', expectedCode: 302);
    }
}
