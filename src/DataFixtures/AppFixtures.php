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
    public const NB_USERS_PER_GROUP = 10;

    public function load(ObjectManager $manager): void
    {
        // 10 utilisateurs activés non admins
        UserFactory::createMany(self::NB_USERS_PER_GROUP, static function (int $i) {
            return [
                'enabled' => true,
                'admin' => false,
                'email' => "user-enabled$i@example.com",
            ];
        });

        // 10 utilisateurs non activés non admins
        UserFactory::createMany(self::NB_USERS_PER_GROUP, static function (int $i) {
            return [
                'enabled' => false,
                'admin' => false,
                'email' => "user-disabled$i@example.com",
            ];
        });

        // 10 utilisateurs activés admins
        UserFactory::createMany(self::NB_USERS_PER_GROUP, static function (int $i) {
            return [
                'enabled' => true,
                'admin' => true,
                'email' => "admin-enabled$i@example.com",
            ];
        });

        // 10 utilisateurs non activés admins
        UserFactory::createMany(10, static function (int $i) {
            return [
                'enabled' => false,
                'admin' => true,
                'email' => "admin-disabled$i@example.com",
            ];
        });


        AlbumFactory::createMany(10, static function (int $i) {
            return [
                'name' => 'Test album',
            ];
        });

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
