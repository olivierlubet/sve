<?php
namespace sve\serie;

class MobileAverage extends \sve\AbstractSerie
{
    const MIN_SIZE=2;
    const MAX_SIZE=30;

    private $size;
    public function getName(){return "MobileAverage[".$this->size."]";}

    public function __construct($parent,$size)
    {
        parent::__construct($parent);
        $this->size = $size;
        $this->compute();
    }

    public function compute()
    {
        if ($this->getParent()->count()==0) return;

        $current=$this->getParent()->getFirst();

        $buffer=0;
        $bufferSize=1;
        $bufferFirst = $current;

        while(!is_null($current)) {
            $buffer+=$current->getValue();

            if($bufferSize<$this->size)
            {
                $bufferSize++;
            } else {
                $this->addLast(new \sve\Value(
                    $current->getDate(),
                    $buffer/$bufferSize
                    ));
                $buffer-=$bufferFirst->getValue();
                $bufferFirst = $bufferFirst->getNext();
            }

            $current = $current->getNext();
        }
    }

    public function buildAllele()
    {
        $newSize = min(self::MAX_SIZE,max(self::MIN_SIZE,rand($this->size*0.5,$this->size*1.5)));
        return new MobileAverage($this->getParent()->buildAllele(),$newSize);
    }


    public function getXmlNode(\DOMDocument $doc)
    {
        $node = parent::getXmlNode($doc);
        $node->appendChild($doc->createAttribute('size'))
            ->value=$this->size;

        return $node;
    }
    
    
    public static function parseXml(\DOMElement $element)
    {
    	$size=$element->getAttribute('size');
    	// Going deeper
    	$element = $element->getElementsByTagName('serie')->item(0);
    	$classname='\\'.$element->getAttribute('name');
    
    	$reflectionMethod = new \ReflectionMethod($classname, 'parseXml');
    	$parent = $reflectionMethod->invoke(null,$element);
    	 
    	return new self($parent,$size);
    }
}