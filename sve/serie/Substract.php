<?php
namespace sve\serie;

class Substract extends \sve\AbstractSerie
{
    private $subSerie;

    public function getName(){return 'Substract['.$this->subSerie->getFullName().']';}

    public function __construct(\sve\AbstractSerie $parent,\sve\AbstractSerie $subSerie)
    {
        parent::__construct($parent);
        $this->subSerie = $subSerie;
        $this->compute();
    }

    public function compute()
    {
        if ($this->getParent()->count()==0) return;
        if ($this->subSerie->count()==0) throw new \Exception("Sub serie is empty : ".$this->subSerie->getFullName());

        $current = $this->getParent()->getFirst();
        $toSubstract = $this->subSerie->getFirst();


        while(!is_null($current)) {
            $toSubstract = $toSubstract->getValidValue($current->getDate());
            $valToSubstract=0;

            if (is_null($toSubstract)) {
                $toSubstract = $this->subSerie->getFirst();
            } else {
                $valToSubstract=$toSubstract->getValue();
            }

            //echo "+".$current."\n";
            //echo "-".$toSubstract."\n";

            $this->addLast(new \sve\Value(
                $current->getDate(),
                $current->getValue()-$valToSubstract
                ));

            $current = $current->getNext();
        }
    }

    public function buildAllele()
    {
        return new Substract($this->getParent()->buildAllele(),$this->subSerie->buildAllele());
    }


    public function getXmlNode(\DOMDocument $doc)
    {
        $node = parent::getXmlNode($doc);
        $subNode=$node->appendChild($doc->createElement('subserie'));
        $subNode->appendChild($this->subSerie->getXmlNode($doc));

        return $node;
    }
}