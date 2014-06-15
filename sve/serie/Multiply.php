<?php
namespace sve\serie;

/*

*/
class Multiply extends \sve\AbstractSerie
{
    private $multiplicator;

    public function getName(){return 'Multiply';}


    public function __construct($parent, $multiplicator)
    {
        parent::__construct($parent);
        $this->multiplicator = $multiplicator;
        $this->compute();
    }

    public function compute()
    {
        if ($this->getParent()->count()<1) return;

        $v = $this->getParent()->getFirst();

        while(!is_null($v)) {


            $this->addLast(new \sve\Value(
                $v->getDate(),
                $v->getValue()*$this->multiplicator
                ));

            $v = $v->getNext();
        }
    }

    public function buildAllele()
    {
        return new Multiply($this->getParent()->buildAllele(),$this->multiplicator);
    }


    public function getXmlNode(\DOMDocument $doc)
    {
        $node = parent::getXmlNode($doc);
        $node->appendChild($doc->createAttribute('multiplicator'))
            ->value=$this->multiplicator;

        return $node;
    }
}