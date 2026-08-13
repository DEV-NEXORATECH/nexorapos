<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertRedirect(route('login'));
});

test('new users can not self register', function () {
    $response = $this->post('/register', [
        'company_name' => 'Test Company',
        'company_code' => 'TESTCO',
        'name' => 'Test User',
        'phone' => '0881023806530',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertGuest();
    $response->assertRedirect(route('login'));
});
