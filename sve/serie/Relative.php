<?php
namespace sve\serie;

/*
Courbe dont l'aboutissement est '1'
-> Permet de comparer différentes courbes
*/
class Relative extends \sve\AbstractSerie
{
    public function getName(){return 'Relative';}

    public function __construct($parent)
    {
        parent::__construct($parent);
        $this->compute();
    }

    public function compute()
    {
        if ($this->getParent()->count()==0) return;

        $reference = $this->getParent()->getLast()->getValue();

        $current=$this->getParent()->getFirst();
        while(!is_null($current)) {
            $this->addLast(new \sve\Value(
                $current->getDate(),
                $current->getValue()/$reference
                ));

            $current = $current->getNext();
        }
    }


    
}