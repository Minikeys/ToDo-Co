<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class DefaultControllerTest extends WebTestCase
{
    private $client = null;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testIndexUnauthenticated()
    {
        $crawler = $this->client->request('GET', '/');

        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
}

    public function testIndexAuthenticatedAdmin()
    {
        $crawler = $this->client->request('GET', '/', [], [], [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'admin',
        ]);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Bienvenue sur Todo List, l\'application vous permettant de gérer l\'ensemble de vos tâches sans effort !', $crawler->filter('h1')->text());
    }

    public function testIndexAuthenticatedUser()
    {
        $crawler = $this->client->request('GET', '/', [], [], [
            'PHP_AUTH_USER' => 'user',
            'PHP_AUTH_PW'   => 'user',
        ]);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Bienvenue sur Todo List, l\'application vous permettant de gérer l\'ensemble de vos tâches sans effort !', $crawler->filter('h1')->text());
    }

    public function testIndexBadAuthenticated()
    {
        $crawler = $this->client->request('GET', '/', [], [], [
            'PHP_AUTH_USER' => 'user',
            'PHP_AUTH_PW'   => '',
        ]);

        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
    }

}
