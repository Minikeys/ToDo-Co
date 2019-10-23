<?php


namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class TaskEntityTest  extends TestCase
{

    public function testAt()
    {
        $task = new Task();
        $date = new \Datetime();
        $task->setCreatedAt($date);

        $this->assertEquals($date, $task->getCreatedAt());
    }

    public function testisDone()
    {
        $task = new Task();

        $task->setIsDone(1);
        $this->assertEquals(1, $task->getIsDone());
    }

}