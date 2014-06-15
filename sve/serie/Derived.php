<?php
namespace sve\serie;

/*
Derived in %
Positif comme négatif
*/
class Derived extends \sve\AbstractSerie
{
    public function getName(){return 'Derived';}

    public function __construct($parent)
    {
        parent::__construct($parent);
        $this->compute();
    }

    public function compute()
    {
        if ($this->getParent()->count()<2) return;

        $v0 = $this->getParent()->getFirst();

        while(!is_null($v0->getNext())) {

            $v1 = $v0->getNext();

            $this->addLast(new \sve\Value(
                $v1->getDate(),
                $v1->getValue()-$v0->getValue()
                ));

            $v0 = $v0->getNext();
        }
    }

    public function buildAllele()
    {
        return new Derived($this->getParent()->buildAllele());
    }
}