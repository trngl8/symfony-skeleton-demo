<?php

namespace Controller\Security;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ResetPasswordControllerTest extends WebTestCase
{
    public function testResetPasswordSuccess(): void
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/reset-password');

        $client->submitForm('Send password reset email', [
            'reset_password_request_form[username]' => 'test@test.com',
        ]);

        $this->assertResponseIsSuccessful();
    }
}
