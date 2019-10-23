<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserControllerTest extends WebTestCase
{
    private $client = null;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testIndexUnauthenticated()
    {
        $crawler = $this->client->request('GET', '/users');

        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
}

    public function testIndexAuthenticatedAdmin()
    {
        $crawler = $this->client->request('GET', '/users', [], [], [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'admin',
        ]);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Liste des utilisateurs', $crawler->filter('h1')->text());
    }

    public function testIndexAuthenticatedUser()
    {
        $crawler = $this->client->request('GET', '/users', [], [], [
            'PHP_AUTH_USER' => 'user',
            'PHP_AUTH_PW'   => 'user',
        ]);

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateAuthenticatedAdmin()
    {
        $crawler = $this->client->request('GET', '/users/create', [], [], [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'admin',
        ]);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Créer un utilisateur', $crawler->filter('h1')->text());
    }

    public function testCreate()
    {
        $crawler = $this->client->request('GET', '/users/create', [], [], [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'admin',
        ]);

        $form = $crawler->selectButton('Ajouter')->form();

        $form['user[username]'] = 'usertest';
        $form['user[password][first]'] = 'usertest';
        $form['user[password][second]'] = 'usertest';
        $form['user[email]'] = 'usertest@todoco.com';

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();
        $this->assertStringContainsString(
            'utilisateur a bien été ajouté.',
            $this->client->getResponse()->getContent()
        );
    }

    public function testCreateBlank()
    {
        $crawler = $this->client->request('GET', '/users/create', [], [], [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'admin',
        ]);

        $form = $crawler->selectButton('Ajouter')->form();

        $this->client->submit($form);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertRegexp(
            '/Vous devez saisir un nom d&#039;utilisateur./',
            $this->client->getResponse()->getContent()
        );
    }

    public function testEditBlank()
    {
        $crawler = $this->client->request('GET', '/users/2/edit', [], [], [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'admin',
        ]);

        $form = $crawler->selectButton('Modifier')->form();

        $form['user[username]'] = '';
        $form['user[password][first]'] = '';
        $form['user[password][second]'] = '';
        $form['user[email]'] = 'usertest@todoco.com';

        $this->client->submit($form);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString(
            'Vous devez saisir un nom',
            $this->client->getResponse()->getContent()
        );
    }

    public function testEditDuplicate()
    {
        $crawler = $this->client->request('GET', '/users/2/edit', [], [], [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'admin',
        ]);

        $form = $crawler->selectButton('Modifier')->form();

        $form['user[username]'] = 'usertest';
        $form['user[password][first]'] = 'usertest';
        $form['user[password][second]'] = 'usertest';
        $form['user[email]'] = 'usertest@todoco.com';

        $this->client->submit($form);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString(
            'Email déjà utilisé.',
            $this->client->getResponse()->getContent()
        );
    }

    public function testEdit()
    {
        $crawler = $this->client->request('GET', '/users/2/edit', [], [], [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'admin',
        ]);

        $form = $crawler->selectButton('Modifier')->form();

        $form['user[username]'] = 'user';
        $form['user[password][first]'] = 'user';
        $form['user[password][second]'] = 'user';
        $form['user[email]'] = 'usertest44@todoco.com';

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();
        $this->assertStringContainsString(
            'utilisateur a bien été modifié',
            $this->client->getResponse()->getContent()
        );
    }
}
