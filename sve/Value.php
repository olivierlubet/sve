<?php
namespace sve;

class Value
{
    private $date;
    private $value;
    protected $nextValue = null;
    protected $previousValue = null;

    public function getDate(){return $this->date;}

    public function getValue(){return $this->value;}

    public function getNext(){return $this->nextValue;}

    public function getPrevious(){return $this->previousValue;}

    public function setNext(Value $v)
    {
        $this->nextValue=$v;
        $v->previousValue=$this;
    }

    public function setPrevious(Value $v)
    {
        $this->previousValue = $v;
        $v->nextValue = $this;
    }

    public function __construct($date, string $value)
    {
        try {
            if(is_string($date)) {
                $this->date = \DateTime::createFromFormat("Y-m-d",$date);//,new \DateTimeZone("Europe/Paris")
            } else if (get_class($date) == "DateTime") {
                $this->date=$date;
            }
            $this->value = floatval ($value);
        } catch (Exception $e) {
            echo $e->getMessage();
            exit(1);
        }
    }

    public function isLast()
    {
        return is_null($this->getNext());
    }

    public function isFirst()
    {
        return is_null($this->getPrevious());
    }

    // May return null
    public function getValidValue(DateTime $date)
    {
        $current = $this;
        
        // Forward
        while(!is_null($current->getNext()) 
        && $current->getNext()->getDate()<=$date)
        {
           $current = $current->getNext();
        }

        //Rewind
        while(!is_null($current)
        && $current->getDate()>$date)
        {
            $current = $current->getPrevious();
        }
        return $current;
    }

    public function __toString()
    {
        return $this->getDate()->format('Y-m-d').":".$this->getValue();
    }
}