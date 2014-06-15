<?php
namespace sve\serie;

include_once("include.php");

class Closing extends \sve\AbstractSerie
{
    private $data;
    private $yahooId;

    public function getName(){return "Closing[".$this->getYahooId()."]";}
    public function getFullName() {return $this->getName();}
    public function getYahooId() {return $this->yahooId;}
    public function count() {return count($this->data);}

    public function __construct($yahooId, $period)
    {
        parent::__construct();

        $this->yahooId=$yahooId;

        $s = new \sve\Security($yahooId, $period);
        $this->data=$s->getData();

        $this->load();
    }

    private function load()
    {
        foreach ($this->data as $row)
        {
            $this->addFirst(new \sve\Value(
                    $row[\sve\YahooCol::Date],
                    $row[\sve\YahooCol::Close]
                ));
        }
    }

    /**
     *
     */
    public function buildAllele()
    {
        return $this;
    }

    public function getXmlNode(\DOMDocument $doc)
    {
        $node = parent::getXmlNode($doc);
        $node->appendChild($doc->createAttribute('yahooId'))
            ->value=$this->yahooId;

        return $node;
    }
}