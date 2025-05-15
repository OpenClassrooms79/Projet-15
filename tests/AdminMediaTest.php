<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\Media;
use App\Entity\User;
use App\Repository\AlbumRepository;
use App\Repository\MediaRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use function imagecreatetruecolor;
use function imagedestroy;
use function imagepng;

class AdminMediaTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;
    private AlbumRepository $albumRepository;
    private MediaRepository $mediaRepository;
    private UserRepository $userRepository;
    private User $admin;
    private string $project_dir;
    private string $upload_dir;
    private Filesystem $filesystem;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);

        $this->project_dir = self::getContainer()->getParameter('kernel.project_dir');
        $this->upload_dir = $this->project_dir . '/public/uploads';
        $this->filesystem = new Filesystem();

        $this->albumRepository = self::getContainer()->get(AlbumRepository::class);
        $this->mediaRepository = self::getContainer()->get(MediaRepository::class);
        $this->userRepository = self::getContainer()->get(UserRepository::class);
        $this->admin = $this->userRepository->findOneBy(['email' => 'admin-enabled2@example.com']);

        $this->client->loginUser($this->admin);
    }

    public function testAdminAddMedia(): void
    {
        // Request a specific page
        $crawler = $this->client->request('GET', '/admin/media/add');
        self::assertResponseIsSuccessful();

        // vérification de la présence des champs du formulaire
        self::assertSelectorExists('form[name=media]');
        self::assertSelectorExists('select[name="media[user]"]');
        self::assertSelectorExists('select[name="media[album]"]');
        self::assertSelectorExists('input[name="media[title]"]');
        self::assertSelectorExists('input[name="media[file]"]');
        self::assertSelectorTextContains('button.btn.btn-primary', 'Ajouter');
        self::assertSelectorTextContains('a.btn.btn-primary', 'Retour');

        // préparer un fichier temporaire pour upload (doit exister sur le disque)
        $filePath = __DIR__ . '/test-image.jpg';
        $this->createTestImage($filePath);

        // Créer l'objet UploadedFile
        $uploadedFile = new UploadedFile(
            $filePath,
            'test-image.jpg',
            'image/jpeg',
            null,
            true, // true pour simuler un fichier uploadé, évite les contrôles d'existence sur le disque
        );

        $album = $this->albumRepository->findOneBy([]);
        $user = $this->userRepository->findOneBy([]);

        // récupérer et remplir le formulaire
        $form = $crawler->selectButton('Ajouter')->form();
        $form['media[user]'] = (string) $user->getId();
        $form['media[album]'] = (string) $album->getId();
        $form['media[title]'] = 'Test ajout media';
        /** @phpstan-ignore-next-line */
        $form['media[file]'] = $uploadedFile;

        // Soumettre le formulaire
        $this->client->submit($form);

        self::assertResponseRedirects('/admin/media');
        $this->client->followRedirect();

        $media = $this->mediaRepository->findOneBy(['title' => 'Test ajout media']);
        self::assertNotNull($media);
        self::assertFileExists($this->project_dir . '/public/' . $media->getPath());

        // supprimer le fichier temporaire
        if ($this->filesystem->exists($filePath)) {
            $this->filesystem->remove($filePath);
        }

        // supprimer l'enregistrement dans la table media
        $this->entityManager->remove($media);
        $this->entityManager->flush();
    }

    public function testAdminDeleteMedia(): void
    {
        $user = $this->userRepository->findOneBy([]);
        $album = $this->albumRepository->findOneBy([]);

        // ajouter un media de test
        $filePath = $this->upload_dir . '/test-image.jpg';
        $dbFilePath = 'uploads/test-image.jpg';
        $this->createTestImage($filePath);
        $media = new Media();
        $media->setUser($user);
        $media->setAlbum($album);
        $media->setPath($dbFilePath);
        $media->setTitle('Test suppression media ' . time());
        $this->entityManager->persist($media);
        $this->entityManager->flush();

        $this->client->request('GET', '/admin/media/delete/' . $media->getId());
        self::assertResponseRedirects('/admin/media');
        $this->client->followRedirect();
    }

    /**
     * créer une image PNG 1x1 pixel valide
     *
     * @param string $filePath
     * @return void
     */
    protected function createTestImage(string $filePath): void
    {
        $image = imagecreatetruecolor(1, 1);
        imagepng($image, $filePath);
        imagedestroy($image);
    }
}
