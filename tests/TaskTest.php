<?php

namespace First\Test\Task\Tests\Task;

use PHPUnit\Framework\TestCase;
use function First\Test\Task\DatabaseConnect\connect;

class TaskTest extends TestCase
{

    public function testExample()
    {
        $this->assertTrue(is_object(connect()));
    }
}
