<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class TaskControllerTest extends WebTestCase
{
    private $client = null;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testIndexUnauthenticated()
    {
        $crawler = $this->client->request('GET', '/tasks');

        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
}

    public function testIndexAuthenticatedAdmin()
    {
        $crawler = $this->client->request('GET', '/tasks', [], [], [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'admin',
        ]);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Liste des tâches', $crawler->filter('h1')->text());
    }

    public function testIndexAuthenticatedUser()
    {
        $crawler = $this->client->request('GET', '/tasks', [], [], [
            'PHP_AUTH_USER' => 'user',
            'PHP_AUTH_PW'   => 'user',
        ]);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Liste des tâches', $crawler->filter('h1')->text());
    }

    public function testCreateAuthenticatedAdmin()
    {
        $crawler = $this->client->request('GET', '/tasks/create', [], [], [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'admin',
        ]);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Créer une tâche', $crawler->filter('h1')->text());
    }

    public function testCreateAuthenticateUser()
    {
        $crawler = $this->client->request('GET', '/tasks/create', [], [], [
            'PHP_AUTH_USER' => 'user',
            'PHP_AUTH_PW'   => 'user',
        ]);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Créer une tâche', $crawler->filter('h1')->text());
    }

    public function testCreateAuthAdmin()
    {
        $crawler = $this->client->request('GET', '/tasks/create', [], [], [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'admin',
        ]);

        $form = $crawler->selectButton('Ajouter')->form();

        $form['task[title]'] = 'Tâche Admin Test';
        $form['task[content]'] = 'Tâche Admin Test';

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();
        $this->assertStringContainsString(
            'La tâche a été bien été ajoutée.',
            $this->client->getResponse()->getContent()
        );
    }

    public function testCreateAuthUser()
    {
        $crawler = $this->client->request('GET', '/tasks/create', [], [], [
            'PHP_AUTH_USER' => 'user',
            'PHP_AUTH_PW'   => 'user',
        ]);

        $form = $crawler->selectButton('Ajouter')->form();

        $form['task[title]'] = 'Tâche User Test';
        $form['task[content]'] = 'Tâche User Test';

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();
        $this->assertStringContainsString(
            'La tâche a été bien été ajoutée.',
            $this->client->getResponse()->getContent()
        );
    }

    public function testCreateBlank()
    {
        $crawler = $this->client->request('GET', '/tasks/create', [], [], [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'admin',
        ]);

        $form = $crawler->selectButton('Ajouter')->form();

        $this->client->submit($form);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertRegexp(
            '/Vous devez saisir un titre./',
            $this->client->getResponse()->getContent()
        );
    }

    public function testEditBlank()
    {
        $crawler = $this->client->request('GET', '/tasks/2/edit', [], [], [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'admin',
        ]);

        $form = $crawler->selectButton('Modifier')->form();

        $form['task[title]'] = '';
        $form['task[content]'] = '';

        $this->client->submit($form);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString(
            'Vous devez saisir un titre.',
            $this->client->getResponse()->getContent()
        );
    }

    public function testEdit()
    {
        $crawler = $this->client->request('GET', '/tasks/3/edit', [], [], [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'admin',
        ]);

        $form = $crawler->selectButton('Modifier')->form();

        $form['task[title]'] = 'Taches Admin Test Edit success';
        $form['task[content]'] = 'Taches Admin Test Edit success';

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();
        $this->assertStringContainsString(
            'La tâche a bien été modifiée.',
            $this->client->getResponse()->getContent()
        );
    }

    public function testDeleteAdminTaskByUser()
    {
        $crawler = $this->client->request('GET', '/tasks/2/delete', [], [], [
            'PHP_AUTH_USER' => 'user',
            'PHP_AUTH_PW'   => 'user',
        ]);

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();
        $this->assertStringContainsString(
            'Vous ne pouvez pas supprimer cette tâche.',
            $this->client->getResponse()->getContent()
        );
    }

    public function testDeleteTaskUser()
    {
        $crawler = $this->client->request('GET', '/tasks/3/delete', [], [], [
            'PHP_AUTH_USER' => 'user',
            'PHP_AUTH_PW'   => 'user',
        ]);

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();
        $this->assertStringContainsString(
            'La tâche a bien été supprimée.',
            $this->client->getResponse()->getContent()
        );
    }

    public function testDeleteAnonymousTaskByUser()
    {
        $crawler = $this->client->request('GET', '/tasks/5/delete', [], [], [
            'PHP_AUTH_USER' => 'user',
            'PHP_AUTH_PW'   => 'user',
        ]);

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();
        $this->assertStringContainsString(
            'Vous ne pouvez pas supprimer cette tâche.',
            $this->client->getResponse()->getContent()
        );
    }

    public function testDeleteAnonymousTaskByAdmin()
    {
        $crawler = $this->client->request('GET', '/tasks/5/delete', [], [], [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'admin',
        ]);

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();
        $this->assertStringContainsString(
            'La tâche a bien été supprimée.',
            $this->client->getResponse()->getContent()
        );
    }

    public function testDeleteUserTaskByAdmin()
    {
        $crawler = $this->client->request('GET', '/tasks/4/delete', [], [], [
            'PHP_AUTH_USER' => 'user',
            'PHP_AUTH_PW'   => 'user',
        ]);

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();
        $this->assertStringContainsString(
            'La tâche a bien été supprimée.',
            $this->client->getResponse()->getContent()
        );
    }

    public function testToggleTask()
    {
        $crawler = $this->client->request('GET', '/tasks/2/toggle', [], [], [
            'PHP_AUTH_USER' => 'user',
            'PHP_AUTH_PW'   => 'user',
        ]);

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();
        $this->assertStringContainsString(
            'a bien été marquée comme faite.',
            $this->client->getResponse()->getContent()
        );
    }
}
