<?php
include_once("include.php");

use sve\Value;

class ValueTest extends PHPUnit_Framework_TestCase
{
    public function testValues()
    {
        $v = new Value("2014-04-18","10");
        $this->assertObjectHasAttribute('date', $v);
        $this->assertObjectHasAttribute('value', $v);

        $this->assertTrue($v->isFirst());
        $this->assertTrue($v->isLast());

        $v2 = new Value("2014-04-19","12");
        $v->setNext($v2);

        $this->assertTrue($v->isFirst());
        $this->assertFalse($v->isLast());
        $this->assertFalse($v2->isFirst());
        $this->assertTrue($v2->isLast());

        $v3 = new Value("2014-04-17","11");
        $v->setPrevious($v3);

        $this->assertTrue($v3->isFirst());
        $this->assertFalse($v3->isLast());
        $this->assertFalse($v->isFirst());
        $this->assertFalse($v->isLast());
        $this->assertFalse($v2->isFirst());
        $this->assertTrue($v2->isLast());


    }
}