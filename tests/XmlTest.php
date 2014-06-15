<?php
include_once("include.php");


class XmlTest extends PHPUnit_Framework_TestCase
{
    private $security;
    private $yahooId;

    protected function setUp()
    {
        $this->yahooId='ACA.PA';
        $this->security = new sve\serie\Closing($this->yahooId, 20);
    }

    public function testSimpleXml()
    {
        //echo "testSimpleXml\n";
        $s = new SimpleXMLElement('<data/>');
        $this->assertEquals('data',$s->getName());

        $s->addAttribute('attribute','value');

        $this->assertEquals(1,$s->attributes()->count());
        $this->assertEquals('value',(string)$s->attributes()->attribute);

        $c = $s->addChild('child','child value');
        $this->assertEquals('child',$c->getName());

        $this->assertEquals(1,$s->count());
        $this->assertEquals(1,$s->children()->count());
        $this->assertEquals('child value',(string)$s->child);

        $c->addAttribute('attribute','value');
        $this->assertEquals('value',(string)$s->child->attributes()->attribute);


        //echo "<pre>".htmlentities($s->asXML())."</pre>";
    }

    public function testDom()
    {
        $doc = new \DOMDocument;
        $node = $doc->createElement("para");
        $newnode = $doc->appendChild($node);

        //echo $doc->saveXML();
    }

    public function testSeries()
    {
        $doc = new \DOMDocument;

        $doc->appendChild($this->security->getXmlNode($doc));
        $this->assertTag(array(
            'tag'=>'serie',
            'attributes' => array('name'=>'sve\serie\Closing')
            ),$doc->saveXML());

        $doc->appendChild($this->security->build('Relative')->getXmlNode($doc));
        $this->assertTag(array(
            'tag'=>'serie',
            'attributes' => array('name'=>'sve\serie\Relative')
            ),$doc->saveXML());


        $doc->appendChild($this->security->build('MobileAverage',array(3))->getXmlNode($doc));
        $this->assertTag(array(
            'tag'=>'serie',
            'attributes' => array(
                'name'=>'sve\serie\MobileAverage',
                'size'=>'3'
            )
            ),$doc->saveXML());

        $doc->appendChild($this->security->build('Performance')->getXmlNode($doc));
        $this->assertTag(array(
            'tag'=>'serie',
            'attributes' => array('name'=>'sve\serie\Performance')
            ),$doc->saveXML());

        $doc->appendChild($this->security->build('Derived')->getXmlNode($doc));
        $this->assertTag(array(
            'tag'=>'serie',
            'attributes' => array('name'=>'sve\serie\Derived')
            ),$doc->saveXML());

        $doc->appendChild($this->security->build('Multiply', array(-1))->getXmlNode($doc));
        $this->assertTag(array(
            'tag'=>'serie',
            'attributes' => array(
                'name'=>'sve\serie\Multiply',
                'multiplicator'=>'-1'
            )
            ),$doc->saveXML());

        $doc->appendChild($this->security->build('Substract',array($this->security))->getXmlNode($doc));
        $this->assertTag(array(
            'tag'=>'serie',
            'attributes' => array(
                'name'=>'sve\serie\Substract'
            )
            ),$doc->saveXML());

$string = <<<XML
<?xml version="1.0"?>
<serie name="sve\serie\Security" yahooId="ACA.PA"/>
<serie name="sve\serie\Relative"><serie name="sve\serie\Security" yahooId="ACA.PA"/></serie>
<serie name="sve\serie\MobileAverage" size="3"><serie name="sve\serie\Security" yahooId="ACA.PA"/></serie>
<serie name="sve\serie\Performance"><serie name="sve\serie\Security" yahooId="ACA.PA"/></serie>
<serie name="sve\serie\Derived"><serie name="sve\serie\Security" yahooId="ACA.PA"/></serie>
<serie name="sve\serie\Multiply" multiplicator="-1"><serie name="sve\serie\Security" yahooId="ACA.PA"/></serie>
<serie name="sve\serie\Substract"><serie name="sve\serie\Security" yahooId="ACA.PA"/><subserie><serie name="sve\serie\Security" yahooId="ACA.PA"/></subserie></serie>
XML;


    }

    public function testAgent()
    {
        $a = new sve\agent\Adam('ACA.PA',20);
        $doc = new DOMDocument;
        $doc->formatOutput=true;
        $doc->appendChild($a->getXmlNode($doc));
        //echo $doc->saveXML();
        $filename='ressources/agent/root/Adam.xml';
        $doc->save($filename);
        $this->assertFileExists($filename);
        
        
        $a = new sve\agent\Eve('ACA.PA',20);
        $filename='ressources/agent/root/Eve.xml';
        $doc->save($filename);
        $a = new sve\agent\Cain('ACA.PA',20);
        $filename='ressources/agent/root/Cain.xml';
        $doc->save($filename);
    }
}