<?php
include_once("include.php");

class SandBoxTest extends PHPUnit_Framework_TestCase
{

    public function testRand()
    {
        for($t=0;$i<10;$i++)
        {
            //echo rand(1,3)."\n";
        }
        /*
            1332212233
        */
    }

    public function testArray ()
    {
        $ar=array("a","b","c","d");
        foreach($ar  as $key => $value)
        {
            //echo "$key : $value \n";
        }
        /*
            0 : a
            1 : b
            2 : c
            3 : d
        */
    }
    
    public function testUSort()
    {
        
        $ar = array(
            new A(1),
            new A(3),
            new A(5),
            new A(2),
            new A(3)
            );
            
        usort($ar,function($a,$b){
            return ($a->getV() > $b->getV()) ? +1 : -1;
        });
        
        //foreach($ar as $v) echo $v->getV()."-";
        
        /*
            1-2-3-3-5
        */
    }
}

class A {
    private $v;
    public function getV(){return $this->v;}
    public function __construct($v) {$this->v = $v;}
}