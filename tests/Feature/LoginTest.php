<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\User;
use Illuminate\Auth\SessionGuard;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use DatabaseMigrations;

    private const PASSWORD = 'i-cannot-wait-to-start-at-2solar';

    public function test_that_a_user_can_see_the_login_form(): void
    {
        $response = $this->get('/login');
        $response->assertViewIs('auth.login');
    }

    public function test_that_a_user_can_login(): void
    {
        $user = factory(User::class)->create([
            'password' => bcrypt(self::PASSWORD)
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => self::PASSWORD,
        ]);

        $this->assertAuthenticatedAs($user);
    }

    /**
     * The logoutOtherSessions functionality force a new hash from the user password.
     * So by checking the first hash not equals the hash from the db after login can test this flow.
     * Larevel with automatically log you out if the hashes doesn't match
     *
     * @see SessionGuard::logoutOtherDevices()
     */
    public function test_that_the_password_hash_change_when_call_logout_other_devices(): void
    {
        // I always setup the 'world' first
        $mainPasswordCrypt = bcrypt(self::PASSWORD);
        $user = factory(User::class)->create([
            'password' => $mainPasswordCrypt
        ]);

        // After setup the world, I do the calls which will affect the tests
        $this->post('/login', [
            'email' => $user->email,
            'password' => self::PASSWORD,
        ]);
        $userFromDb = User::first();

        // And at last, I do the assertions
        $this->assertAuthenticatedAs($user);
        $this->assertNotEquals($mainPasswordCrypt, $userFromDb->password);
    }
}
