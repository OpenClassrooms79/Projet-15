<?php

namespace App\Tests;

use App\Entity\User;
use App\Security\UserChecker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

class UserCheckerTest extends TestCase
{
    public function testCheckUserThrowsWhenDisabled(): void
    {
        $user = new User();
        $user->setEnabled(false);

        $checker = new UserChecker();

        $this->expectException(CustomUserMessageAccountStatusException::class);
        $checker->checkPreAuth($user);
    }

    public function testCheckUserPassesWhenEnabled(): void
    {
        $user = new User();
        $user->setEnabled(true);

        $checker = new UserChecker();

        $checker->checkPreAuth($user); // pas d'exception => test OK
        $this->assertTrue(true);
    }
}