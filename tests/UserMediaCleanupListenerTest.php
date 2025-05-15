<?php

namespace App\Tests;

use App\Entity\Album;
use App\Entity\Media;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;

use function file_get_contents;
use function file_put_contents;

class UserMediaCleanupListenerTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private Filesystem $filesystem;
    private string $uploadDir;

    /** @var string[] $filePaths */
    private array $filePaths = [];

    protected function setUp(): void
    {
        self::bootKernel();

        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
        $this->filesystem = new Filesystem();
        $this->uploadDir = self::getContainer()->getParameter('kernel.project_dir') . '/public/uploads';
        $this->filesystem->mkdir($this->uploadDir); // si le répertoire n'existe pas déjà
    }


    public function testDeletingUserAlsoDeletesMediaFiles(): void
    {
        $user = new User();
        $user->setAdmin(false);
        $user->setName('test');
        $user->setDescription('test');
        $user->setEmail('test@example.com');
        $user->setPassword('password');
        $user->setEnabled(true);
        $this->entityManager->persist($user);

        $album = new Album();
        $album->setName('Test album');
        $this->entityManager->persist($album);

        // créer des fichiers de test sur disque
        for ($n = 1; $n <= 3; $n++) {
            $filePath = $this->uploadDir . "/testfile$n.jpg";
            file_put_contents($filePath, 'contenu de test');
            $this->assertFileExists($filePath); // vérifier que le fichier a bien été créé

            $content = file_get_contents($filePath);
            $this->assertEquals('contenu de test', $content); // vérifier que le contenu du fichier est correct
            $this->filePaths[] = $filePath;

            // créer un média lié à ce fichier
            $media = new Media();
            $media->setUser($user);
            $media->setAlbum($album);
            $media->setPath($filePath);
            $media->setTitle("Test media $n");
            $this->entityManager->persist($media);
        }
        $this->entityManager->flush(); // enregistrer les changements dans la base de données

        // supprimer l'utilisateur pour déclencher le listener
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        // Vérifier que les fichiers ont bien été supprimés automatiquement
        foreach ($this->filePaths as $filePath) {
            $this->assertFileDoesNotExist($filePath);
        }
    }

    protected function tearDown(): void
    {
        // nettoyage, si les tests ont echoué
        foreach ($this->filePaths as $filePath) {
            $this->filesystem->remove($filePath);
        }
        parent::tearDown();
    }
}
