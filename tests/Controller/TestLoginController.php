<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestLoginController extends WebTestCase
{
    public function testUserLogin()
    {
        $client = static::createClient();
        $client->followRedirects();

        $client->request('GET', '/');
        $crawler = $client->clickLink('Login');
        self::assertResponseStatusCodeSame(200);
        $form = $crawler->filter('form')->first()->form();
        $form['email'] = '111111111111';
        $form['password'] = '111111111';
        $crawler = $client->submit($form);
        self::assertEquals(
            'Invalid credentials.',
            $crawler->filter('.alert-danger')->first()->text()
        );

        $form['email'] =  'user1@example.com';
        $form['password'] = 'user1password';
        $client->submit($form);
    }
}