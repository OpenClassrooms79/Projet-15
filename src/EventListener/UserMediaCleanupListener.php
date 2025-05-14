<?php

namespace App\EventListener;

use App\Entity\User;
use App\Entity\Media;
use Doctrine\ORM\Event\PreRemoveEventArgs;
use Symfony\Component\Filesystem\Filesystem;

use function is_file;

class UserMediaCleanupListener
{
    public function preRemove(User $user, PreRemoveEventArgs $args): void
    {
        $em = $args->getObjectManager();
        $medias = $em->getRepository(Media::class)->findBy(['user' => $user]);

        $filesystem = new Filesystem();

        foreach ($medias as $media) {
            $path = $media->getPath();
            if ($filesystem->exists($path) && is_file($path)) {
                $filesystem->remove($path);
            }
        }
    }
}
