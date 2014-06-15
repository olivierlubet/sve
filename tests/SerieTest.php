<?php
include_once("include.php");

use sve\Serie;
use sve\Value;

class SerieTest extends PHPUnit_Framework_TestCase
{
    private $security = null;
    public function testSerie()
    {
        $s = new Serie("Test");

        $this->assertTrue($s->getName()=="Test");
        $this->assertTrue($s->count()==0);
        $this->assertNull($s->getFirst());
        $this->assertNull($s->getLast());

        $v = new Value("2014-04-18","10");
        $s->addFirst($v);

        $this->assertTrue($v===$s->getFirst());
        $this->assertTrue($v===$s->getLast());

        $v2 = new Value("2014-04-19","12");
        $s->addLast($v2);

        $this->assertTrue($v===$s->getFirst());
        $this->assertTrue($v2===$s->getLast());

        $v3 = new Value("2014-04-17","11");
        $s->addFirst($v3);

        $this->assertTrue($v3===$s->getFirst());
        $this->assertTrue($v2===$s->getLast());

        $this->assertTrue($v3->getNext()===$v2->getPrevious());// Expect $v

        $this->assertEquals(3,$s->count());
        
        //echo $s;
        
        $this->assertEquals(1.2,$s->performance(1));
        $this->assertEquals(1.0909090909091,$s->performance(2));
        $this->assertEquals(0,$s->performance(3));

        $s2 = $s->buildAllele();
    }

    protected function setUp()
    {
        $yahooId='ACA.PA';
        $period=20;
        $this->security = new sve\serie\Closing($yahooId, $period);
    }

    public function testSecurity()
    {
        $this->assertGreaterThan(0,$this->security->count());
        $this->assertEquals('Closing[ACA.PA]',$this->security->getName());
    }

    public function testRelative()
    {
        $s = $this->security->build('Relative');

        $this->assertNotNull($s);
        $this->assertEquals('Relative',$s->getName());
        $this->assertEquals('Closing[ACA.PA] Relative',$s->getFullName());
        $this->assertGreaterThan(0,$s->count());
        $this->assertEquals(1,$s->getLast()->getValue());
        $this->assertEquals(
            $this->security->getFirst()->getValue()/$this->security->getLast()->getValue(),
            $s->getFirst()->getValue());
    }

    public function testMobileAverage()
    {
        $s = $this->security->build('MobileAverage',array(3));
        $this->assertNotNull($s);
        $this->assertEquals('MobileAverage[3]',$s->getName());
        $this->assertEquals($this->security->count()-2,$s->count());
        //echo $this->security;
        //echo $s;
        $v1 = $this->security->getLast();
        $v2 = $v1->getPrevious();
        $v3 = $v2->getPrevious();
        $average = ($v1->getValue() + $v2->getValue() + $v3->getValue())/3;
        $this->assertEquals($average,$s->getLast()->getValue());
    }

    public function testPerformance()
    {
        $s = $this->security->build('Performance');
        $this->assertNotNull($s);
        $this->assertEquals('Performance',$s->getName());
        $this->assertEquals($this->security->count()-1,$s->count());

        $v1 = $this->security->getLast();
        $v2 = $v1->getPrevious();
        $performance=$v1->getValue()/$v2->getValue()-1;

        $this->assertEquals($performance,$s->getLast()->getValue());

        //echo $this->security;
        //echo $s;
    }

    public function testDerived()
    {
        $s = $this->security->build('Derived');
        $this->assertNotNull($s);
        $this->assertEquals('Derived',$s->getName());
        $this->assertEquals($this->security->count()-1,$s->count());

        $v1 = $this->security->getLast();
        $v2 = $v1->getPrevious();
        $derived=$v1->getValue()-$v2->getValue();

        $this->assertEquals($derived,$s->getLast()->getValue());
    }

    public function testMultiply()
    {
        $s = $this->security->build('Multiply', array(-1));
        $this->assertNotNull($s);
        $this->assertEquals('Multiply',$s->getName());
        $this->assertEquals($this->security->count(),$s->count());

        $v = $this->security->getLast();
        $result=$v->getValue()*(-1);

        $this->assertEquals($result,$s->getLast()->getValue());
    }

    public function testSubstract()
    {
        $s = $this->security->build('Substract',array($this->security));
        $this->assertNotNull($s);
        $this->assertEquals('Substract[Closing[ACA.PA]]',$s->getName());
        $this->assertEquals('Closing[ACA.PA] Substract[Closing[ACA.PA]]',$s->getFullName());
        $this->assertEquals($this->security->count(),$s->count());
        $this->assertEquals(0,$s->getFirst()->getValue());
        $this->assertEquals(0,$s->getLast()->getValue());

        //echo $s;
    }

    public function testAllele()
    {
        $s = $this->security->build('MobileAverage',array(6));
        $s2 = $s->buildAllele();
        //echo $s->getFullName()."\n";
        //echo $s2->getFullName()."\n";

        // From Cain
        $s1=$this->security->build('MobileAverage',array(6))->build('Performance');
        $s2=$s1->build('Derived');
        $sBuy=$s1->build('Substract',array($s2));
        //echo $sBuy->getFullName()."\n";
        //echo $sBuy->buildAllele()->getFullName()."\n";
    }
}