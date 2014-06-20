<?php
include_once("include.php");

use sve\Agent;

class AgentTest extends PHPUnit_Framework_TestCase
{
    const VERBOSE=false;
    private $from;

    protected function setUp()
    {
    }


    public function testAdam()
    {
        $s = new \sve\agent\Adam('ACA.PA',20);

        $this->assertNotNull($s);
        $this->assertEquals('Adam',$s->getName());
        $this->assertEquals(1,$s->getGeneration());
        $this->assertGreaterThan(0,$s->count());
        $this->assertGreaterThan(0,$s->getResult());

        if(self::VERBOSE)
        {
            echo "Adam ".$s->getResult()."\n";
        }
    }

    public function testEve()
    {
        $s = new \sve\agent\Eve('ACA.PA',20);

        $this->assertNotNull($s);
        $this->assertEquals('Eve',$s->getName());
        $this->assertGreaterThan(0,$s->count());
        $this->assertGreaterThan(0,$s->getResult());

        if(self::VERBOSE)
        {
            echo "Eve ".$s->getResult()."\n";
        }
    }

    public function testCain()
    {
        $s = new \sve\agent\Cain('ACA.PA',20);

        $this->assertNotNull($s);
        $this->assertEquals('Cain',$s->getName());
        $this->assertGreaterThan(0,$s->count());
        $this->assertGreaterThan(0,$s->getResult());


        if(self::VERBOSE)
        {
            echo "Cain ".$s->getResult()."\n";
            //echo "Cain\n".$s;
        }
    }

    public function testAllele()
    {
        $s = new \sve\agent\Adam('ACA.PA',50);
        $s1 = $s->buildAllele();
        $s = new \sve\agent\Eve('ACA.PA',50);
        $s2 = $s->buildAllele();
        $s = new \sve\agent\Cain('ACA.PA',50);
        $s3 = $s->buildAllele();


        if(self::VERBOSE)
        {
            echo "Adam' ".$s1->getResult()."\n";
            echo "Eve' ".$s2->getResult()."\n";
            echo "Cain' ".$s3->getResult()."\n";
        }
    }

    public function testChildren()
    {
        $s1 = new \sve\agent\Adam('ACA.PA',50);
        $s2 = new \sve\agent\Eve('ACA.PA',50);
        $s3 = new \sve\agent\Cain('ACA.PA',50);

        $s4 = $s1->giveMeChild($s2);
        $s5 = $s3->giveMeChild($s2);

        $this->assertEquals(1,Agent::MIN_STRATEGY_LENGTH);

        $this->assertEquals(1,$s1->getGeneration());
        $this->assertEquals(2,$s4->getGeneration());
        $this->assertEquals(2,$s5->getGeneration());

        if(self::VERBOSE)
        {
            echo $s4->getName().' '.$s4->getResult()."\n";
            echo $s5->getName().' '.$s5->getResult()."\n";
        }
    }

    public function testNameManager()
    {
        $name=\sve\NameManager::getName();
        $this->assertEquals("MCMXXXII",\sve\NameManager::rome(1932));
    }
}