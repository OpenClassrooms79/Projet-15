<?php

namespace App\DataFixtures;

use App\Entity\Album;
use App\Entity\User;
use App\Factory\AlbumFactory;
use App\Factory\MediaFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::createMany(50);
        AlbumFactory::createMany(10);

        $users = $manager->getRepository(User::class)->findAll();
        $albums = $manager->getRepository(Album::class)->findAll();

        MediaFactory::createMany(1000, static function (int $i) use ($users, $albums) {
            return [
                'user' => $users[array_rand($users)],
                'album' => $albums[array_rand($albums)],
            ];
        });

        $manager->flush();
    }
}
