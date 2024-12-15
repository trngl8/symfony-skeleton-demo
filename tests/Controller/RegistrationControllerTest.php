<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    public function testRegisterSuccess(): void
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/register');

        $client->submitForm('Register', [
            'registration_form[username]' => 'test@test.com',
            'registration_form[plainPassword]' => 'secretpass',
            'registration_form[agreeTerms]' => true,
        ]);

        $this->assertResponseIsSuccessful();
    }
}
