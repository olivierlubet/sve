<?php
namespace sve;

// just for testing
class Serie extends AbstractSerie
{
    private $name;

    public function getName() {return $this->name;}

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function buildAllele()
    {
        return clone $this;
    }
}