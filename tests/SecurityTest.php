<?php
include_once("include.php");

use sve\Security;

class SecurityTest extends PHPUnit_Framework_TestCase
{

    public function testSecurity()
    {
        $yahooId='ACA.PA';
        $filename='ressources/dl/ACA.PA.10';
        //exec('rm '.$filename);
        //$this->assertFileNotExists($filename);

        $s = new Security($yahooId, 10);

        $this->assertEquals($s->getYahooId(),$yahooId);
        //$this->assertEquals($s->getYahooUrl(),"http://ichart.yahoo.com/table.csv?s=ACA.PA&a=0&b=1&c=2000&g=d");


        $this->assertFileExists($filename);
        $this->assertGreaterThan(0,filesize($filename));
        $today=new DateTime();
        
        $this->assertEquals($today->format('Y-m-d'),$s->getDlDate());
        $this->assertGreaterThan(0,$s->count());
    }
}