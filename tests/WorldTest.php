<?php
include_once("include.php");

use sve\World;

class WorldTest extends PHPUnit_Framework_TestCase
{
    public function testWorld()
    {
        $w = new World('ACA.PA',5,50);

        $this->assertEquals('ACA.PA',$w->getYahooId());
        $this->assertEquals(0,$w->getCycleLeft());
        $this->assertGreaterThan(0,count($w->getPopulation()));

        $w->compute(1);
        $this->assertEquals(5,count($w->getPopulation()));

        foreach($w->getPopulation() as $agent)
        {
            //echo $agent->getName().":".$agent->getLast()->getValue()."\n";
        }
        
        $pop=$w->getPopulation();
        $pop[0]->save();
    }
}