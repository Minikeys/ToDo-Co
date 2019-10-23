<?php


namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class UserEntityTest  extends TestCase
{

    public function testRoles()
    {
        $user = new User();
        $roles = ['ROLE_USER'];
        $user->setRoles($roles);

        $this->assertEquals($roles, $user->getRoles());
    }

    public function testAddTask()
    {
        $task = new Task();
        $user = new User();
        $user->addTask($task);
        $collection = new ArrayCollection([$task]);

        $this->assertEquals($collection, $user->getTasks());
    }

    public function testRemoveTask()
    {
        $task = new Task();
        $user = new User();
        $collection = new ArrayCollection([]);
        $user->removeTask($task);

        $this->assertEquals($collection, $user->getTasks());
    }
}