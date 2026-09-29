<?php

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    Admin::query()->delete();
    Admin::create([
        'name' => 'Super Admin',
        'email' => 'admin@tgo.com',
        'phone' => '01700000000',
        'password' => Hash::make('12345678'),
    ]);
});

test('admin can login with email', function () {
    $response = $this->postJson('/api/admin/login', [
        'email' => 'admin@tgo.com',
        'password' => '12345678',
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonStructure([
            'success',
            'message',
            'resources' => [
                'admin' => ['id', 'name', 'email', 'phone'],
                'token',
            ],
        ]);
});

test('admin can login with phone', function () {
    $response = $this->postJson('/api/admin/login', [
        'phone' => '01700000000',
        'password' => '12345678',
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonPath('resources.admin.phone', '01700000000');
});

test('admin can login with phone sent in email field', function () {
    $response = $this->postJson('/api/admin/login', [
        'email' => '01700000000',
        'password' => '12345678',
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonPath('resources.admin.email', 'admin@tgo.com');
});

test('admin login fails with wrong password', function () {
    $response = $this->postJson('/api/admin/login', [
        'email' => 'admin@tgo.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(401)
        ->assertJsonPath('success', false);
});

test('authenticated admin can access me and logout', function () {
    $admin = Admin::first();
    $token = $admin->createToken('admin-token')->plainTextToken;

    $meResponse = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/admin/me');

    $meResponse->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonPath('resources.admin.email', 'admin@tgo.com');

    $logoutResponse = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/admin/logout');

    $logoutResponse->assertStatus(200)
        ->assertJsonPath('success', true);
});
