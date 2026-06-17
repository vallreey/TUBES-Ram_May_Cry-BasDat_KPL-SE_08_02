<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_role_admin_constant()
    {
        $this->assertEquals(
            'admin',
            User::ROLE_ADMIN
        );
    }

    public function test_role_peternak_constant()
    {
        $this->assertEquals(
            'peternak',
            User::ROLE_PETERNAK
        );
    }

    public function test_role_pembeli_constant()
    {
        $this->assertEquals(
            'pembeli',
            User::ROLE_PEMBELI
        );
    }

    public function test_fillable_contains_email()
    {
        $user = new User();

        $this->assertContains(
            'email',
            $user->getFillable()
        );
    }

    public function test_fillable_contains_role()
    {
        $user = new User();

        $this->assertContains(
            'role',
            $user->getFillable()
        );
    }
}