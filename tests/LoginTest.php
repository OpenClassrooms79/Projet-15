<?php
/**
 * @author Jocelyn Flament
 * @since 14/05/2025
 */

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginTest extends WebTestCase
{
    public function testLoginSuccess(): void
    {
        $client = static::createClient();

        // aller sur la page d'authentification, remplir et soumettre le formulaire
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Connexion')->form([
            '_username' => 'user-enabled1@example.com',
            '_password' => 'password',
        ]);
        $client->submit($form);
        self::assertResponseRedirects('/admin/media');
        $client->followRedirect();
    }

    public function testThatLoginShouldFail(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Connexion')->form([
            '_username' => 'user-enabled1@example.com',
            '_password' => 'wrong-password',
        ]);
        $client->submit($form);

        // pas de redirection
        self::assertResponseRedirects('/login');
    }
}