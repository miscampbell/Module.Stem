<?php

namespace Rhubarb\Stem\Tests\LoginProviders;

use Rhubarb\Crown\Encryption\HashProvider;
use Rhubarb\Crown\Encryption\Sha512HashProvider;
use Rhubarb\Crown\LoginProviders\Exceptions\LoginDisabledException;
use Rhubarb\Crown\LoginProviders\Exceptions\LoginFailedException;
use Rhubarb\Crown\LoginProviders\Exceptions\NotLoggedInException;
use Rhubarb\Crown\Tests\RhubarbTestCase;
use Rhubarb\Stem\Tests\Fixtures\TestLoginProvider;
use Rhubarb\Stem\Tests\Fixtures\User;

class ModelLoginProviderTest extends RhubarbTestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        HashProvider::SetHashProviderClassName(Sha512HashProvider::class);

        $user = new User();
        $user->Username = "billy";
        $user->Password = '$6$rounds=10000$EQeQYSJmy6UAzGDb$7MoO7FLWXex8GDHkiY/JNk5ukXpUHDKfzs3S5Q04IdB8Xz.W2qp1zZ7/oVWrFZrCX7qKckJNeBDwRC.rmVR/Q1';
        $user->Active = false;
        $user->save();

        $user = new User();
        $user->Username = "mdoe";
        $user->Password = '$6$rounds=10000$EQeQYSJmy6UAzGDb$7MoO7FLWXex8GDHkiY/JNk5ukXpUHDKfzs3S5Q04IdB8Xz.W2qp1zZ7/oVWrFZrCX7qKckJNeBDwRC.rmVR/Q1';
        $user->Active = true;
        // This secret property is used to test the model object is returned correctly.
        $user->SecretProperty = "111222";
        $user->save();

        // This rogue entry is to make sure that we can't login with no username
        // even if there happens to be someone with no username.
        $user = new User();
        $user->Username = "";
        $user->Password = "";
        $user->save();
    }

    public function testLoginChecksUsernameIsNotBlank()
    {
        $this->setExpectedException(LoginFailedException::class);

        $testLoginProvider = new TestLoginProvider();
        $testLoginProvider->login("", "");
    }

    public function testLoginChecksUsername()
    {
        $this->setExpectedException(LoginFailedException::class);

        $testLoginProvider = new TestLoginProvider();
        $testLoginProvider->login("noname", "nopassword");
    }

    public function testLoginChecksDisabled()
    {
        $this->setExpectedException(LoginDisabledException::class);

        $testLoginProvider = new TestLoginProvider();
        $testLoginProvider->login("billy", "abc123");
    }

    public function testLoginChecksPasswordAndThrows()
    {
        $this->setExpectedException(LoginFailedException::class);

        $testLoginProvider = new TestLoginProvider();
        $testLoginProvider->login("mdoe", "badpassword");
    }

    public function testLoginChecksPasswordReturnsModelAndLogsOut()
    {
        $testLoginProvider = new TestLoginProvider();

        try {
            $testLoginProvider->login("mdoe", "badpassword");
        } catch (LoginFailedException $er) {
        }

        $this->assertFalse($testLoginProvider->IsLoggedIn());

        $result = $testLoginProvider->login("mdoe", "abc123");

        $this->assertTrue($result);
        $this->assertTrue($testLoginProvider->IsLoggedIn());

        $model = $testLoginProvider->getModel();

        $this->assertInstanceOf(User::class, $model);
        $this->assertEquals("111222", $model->SecretProperty);

        $this->assertNotNull($testLoginProvider->LoggedInUserIdentifier);

        $testLoginProvider->LogOut();

        $this->assertFalse($testLoginProvider->IsLoggedIn());
        $this->assertNull($testLoginProvider->LoggedInUserIdentifier);

        $this->setExpectedException(NotLoggedInException::class);

        $model = $testLoginProvider->getModel();
    }

    public function testForceLogin()
    {
        $user = new User();
        $user->Username = "flogin";
        $user->save();

        $testLoginProvider = new TestLoginProvider();
        $testLoginProvider->forceLogin($user);

        $this->assertTrue($testLoginProvider->IsLoggedIn());
        $this->assertEquals($user->UniqueIdentifier, $testLoginProvider->getModel()->UniqueIdentifier);
    }
}
