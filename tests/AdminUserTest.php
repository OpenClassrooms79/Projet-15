<?php

namespace App\Tests;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use function time;

use const PHP_INT_MAX;

class AdminUserTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;
    private User $admin;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);

        $this->userRepository = $this->entityManager->getRepository(User::class);
        $this->admin = $this->userRepository->findOneBy(['email' => 'admin-enabled2@example.com']);

        $this->client->loginUser($this->admin);
    }

    public function testAdminUserIndex(): void
    {
        $this->client->loginUser($this->admin);
        $this->client->request('GET', '/admin/user');
        self::assertResponseIsSuccessful();
        self::assertAnySelectorTextContains('h1', 'Invités');
        self::assertAnySelectorTextContains('a.btn.btn-primary', 'Ajouter');
        self::assertAnySelectorTextContains('a.btn.btn-primary', 'Ajouter un utilisateur');
    }

    public function testAdminAddUser(): void
    {
        $this->client->loginUser($this->admin);
        $crawler = $this->client->request('GET', '/admin/user/add');
        self::assertResponseIsSuccessful();

        // vérification de la présence des champs du formulaire
        self::assertSelectorExists('form[name=user]');
        self::assertSelectorExists('input[name="user[name]"]');
        self::assertSelectorExists('textarea[name="user[description]"]');
        self::assertSelectorExists('input[name="user[email]"]');
        self::assertSelectorExists('input[name="user[password][first]"]');
        self::assertSelectorExists('input[name="user[password][second]"]');
        self::assertSelectorExists('input[name="user[admin]"]');
        self::assertSelectorTextContains('button.btn.btn-primary', 'Ajouter');
        self::assertSelectorTextContains('a.btn.btn-primary', 'Retour');

        $user = new User();
        $user->setName('test ' . time());
        $user->setDescription('description ' . time());
        $user->setEmail(sprintf('test%d@example.org', random_int(1, PHP_INT_MAX)));
        $user->setPassword('ef@lkGrlgjU_foiioedhfezihguig5$åfè8#é'); // un mot de passe complexe et long
        $user->setAdmin(false);

        // remplir et soumettre le formulaire d’ajout d'utilisateur
        $formData = [
            'user[name]' => $user->getName(),
            'user[description]' => $user->getDescription(),
            'user[email]' => $user->getEmail(),
            'user[password][first]' => $user->getPassword(),
            'user[password][second]' => $user->getPassword(),
        ];
        if ($user->isAdmin()) {
            $formData['user[admin]'] = '1';
        }
        $form = $crawler->selectButton('Ajouter')->form($formData);
        $this->client->submit($form);

        // vérifier que la redirection fonctionne
        self::assertResponseRedirects('/admin/user');
        $this->client->followRedirect();

        // lire l'utilisateur nouvellement créé
        $newUser = $this->userRepository->findOneBy(['email' => $user->getEmail()]);

        // comparer avec les valeurs de l'utilisateur initial
        self::assertNotNull($newUser);
        self::assertEquals($user->getName(), $newUser->getName());
        self::assertEquals($user->getDescription(), $newUser->getDescription());
        self::assertEquals($user->getEmail(), $newUser->getEmail());
        self::assertEquals($user->isAdmin(), $newUser->isAdmin());

        // Vérification du mot de passe
        /** @var UserPasswordHasherInterface $passwordHasher */
        $passwordHasher = self::getContainer()->get(UserPasswordHasherInterface::class);
        self::assertTrue(
            $passwordHasher->isPasswordValid($newUser, $user->getPassword()),
            "Le mot de passe enregistré n'est pas valide.",
        );
    }

    public function testAdminDeleteUser(): void
    {
        // ajout d'un utilisateur dans la base de données
        $user = new User();
        $user->setName('test suppression utilisateur ' . time());
        $user->setDescription('description ' . time());
        $user->setEmail(sprintf('test%d@example.org', random_int(1, PHP_INT_MAX)));
        $user->setPassword('ef@lkGrlgjU_foiioedhfezihguig5$åfè8#é'); // un mot de passe complexe et long
        $user->setAdmin(false);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        echo "Nouvel utilisateur créé: " . $user->getId() . "\n";

        $this->client->loginUser($this->admin);
        echo "Utilisateur supprimé: " . $user->getId() . "\n";
        $this->client->request('GET', '/admin/user/delete/' . $user->getId());
        self::assertResponseRedirects('/admin/user');
    }

    public function testAdminUserToggleStatus(): void
    {
        $user = $this->userRepository->findOneBy(['email' => 'user-enabled5@example.com']);
        $oldStatus = $user->isEnabled();

        $this->client->loginUser($this->admin);
        $this->client->request('POST', sprintf('/admin/user/%d/toggle', $user->getId()));

        $newUser = $this->userRepository->findOneBy(['email' => 'user-enabled5@example.com']);

        self::assertResponseIsSuccessful();
        self::assertEquals($oldStatus, !$newUser->isEnabled());
    }

    public function testAdminDeleteUserNotFound(): void
    {
        $this->client->loginUser($this->admin);

        // appel d'une URL qui déclenche addFlash('error', 'Utilisateur introuvable.');
        $this->client->request('GET', '/admin/user/delete/-1');

        self::assertResponseRedirects();
        $this->client->followRedirect();

        // vérifie que le message flash est visible dans le HTML
        self::assertSelectorTextContains('.alert.alert-danger', 'Utilisateur introuvable.');
    }
}
