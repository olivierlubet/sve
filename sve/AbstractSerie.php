<?php
namespace sve;

abstract class AbstractSerie
{
    private $parent = null;
    private $firstValue = null;
    private $lastValue = null;
    private $count=0;

    abstract public function getName();

    public function __construct(\sve\AbstractSerie $parent=null)
    {
        $this->parent = $parent;
    }

    public function count() {return $this->count;}

    public function getFirst() {return $this->firstValue;}

    public function getLast() {return $this->lastValue;}

    public function getFullName() {
        $name=$this->getName();
        if (! is_null($this->parent))
        {
            $name=$this->parent->getFullName()." ".$name;
        }
        return $name;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function addFirst(Value $v)
    {
        if (!is_null($this->firstValue))
        {
            $this->firstValue->setPrevious($v);
        } else {
            $this->lastValue=$v;
        }
        $this->firstValue=$v;
        $this->count++;
    }

    public function addLast(Value $v)
    {
        if (!is_null($this->lastValue))
        {
            $this->lastValue->setNext($v);
        } else {
            $this->firstValue=$v;
        }
        $this->lastValue=$v;
        $this->count++;
    }

    /**
     * Chain building
     * 
     * @param AbstractSerie $serie
     * @param array $args
     * @return AbstractSerie
     */
    public function build(AbstractSerie $serie, Array $args=array())
    {
        // Passing $this as parent in first argument
        $args=array_reverse ($args);
        $args[]=$this;
        $args=array_reverse ($args);

        //Generate class by reflection
        $classname='\sve\serie\\'.$serie;
        $class = new \ReflectionClass($classname);
        return $class->newInstanceArgs($args);
    }
    

    public function __toString()
    {
        $ret="Count:".$this->count()."\n";
        $current = $this->getFirst();
        while(!is_null($current)) {
            $ret.=$current."\n";
            $current = $current->getNext();
        }
        return $ret;
    }
    
    public function buildAllele()
    {
    	$classname=get_class($this);
        return new $classname($this->getParent()->buildAllele());
    }
    
    public function performance($depth)
    {
        if ($depth<1) throw new Exception('Depth must be 1 or more');
        if ($this->count<=$depth) return 0;
        
        $current = $this->getLast();
        $reference=$this->getLast();
        while($depth>0) {
            $reference=$reference->getPrevious();
            $depth--;
        }
        return $current->getValue()/$reference->getValue();
    }

    public function getXmlNode(\DOMDocument $doc)
    {
        $node = $doc->createElement("serie");
        $classname=get_class($this);
        $node->appendChild($doc->createAttribute('name'))
            ->value=$classname ;

        if (!is_null($this->getParent()))
        {
            $node->appendChild(
                $this->getParent()->getXmlNode($doc)
                );
        }
        return $node;
    }
    
    public static function parseXml(\DOMElement $element)
    {
    	// Going deeper
    	$element = $element->getElementsByTagName('serie')->item(0);
    	$classname='\\'.$element->getAttribute('name');
    
    	$reflectionMethod = new \ReflectionMethod($classname, 'parseXml');
    	$parent = $reflectionMethod->invoke(null,$element);
    	 
    	$classname=get_called_class();
    	return new $classname($parent);
    }
    
    public static function buildFromXml(\DOMElement $element)
    {
    	$classname='\\'.$element->getAttribute('name');
    	$reflectionMethod = new \ReflectionMethod($classname, 'parseXml');
    	return $reflectionMethod->invoke(null,$element);
    }
    
}