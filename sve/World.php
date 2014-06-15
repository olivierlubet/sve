<?php
namespace sve;

class World
{
    private $period;
    public function getPeriod() {return $this->period;}

    private $yahooId;
    public function getYahooId(){return $this->yahooId;}

    private $maxPopulation;
    public function getMaxPopulation(){return $this->maxPopulation;}

    private $cycleLeft;
    public function getCycleLeft(){return $this->cycleLeft;}

    private $population=array();
    public function getPopulation(){return $this->population;}

    public function __construct($yahooId='ACA.PA',$maxPopulation=10, $period=200)
    {
        if ($maxPopulation<3)throw new \Exception("Max population can't be < 3");

        $this->period=$period;
        $this->yahooId = $yahooId;
        $this->maxPopulation=$maxPopulation;

        $this->initPopulation();
    }

    private function initPopulation()
    {

        foreach (array(
                new \sve\agent\Adam($this->getYahooId(),$this->period),
                new \sve\agent\Eve($this->getYahooId(),$this->period),
                new \sve\agent\CainBest($this->getYahooId(),$this->period)
            ) as $agent)
            {
                $this->population [$agent->getResult()]= $agent;
                $a2=$agent->buildAllele();
                $this->population [$a2->getResult()]=$a2; 
            }
    }

    public function compute($cycle)
    {
        if ($cycle<1)throw new \Exception("Cycle can't be < 1");
        $this->cycleLeft=$cycle;

        while ($this->cycleLeft>0)
        {
            $this->populationRegulation();
            $this->populate();
            $this->sortByPerformance();
            $this->cycleLeft--;
        }
    }

    /**
     * Sorting DESC
     *
     */
    private function sortByPerformance()
    {
        usort($this->population,function($a,$b)
        {
            return ($a->getResult() <= $b->getResult()) ? +1 : -1;
        });
    }

    private function populationRegulation()
    {
        while(count($this->population)>($this->maxPopulation/3+2))
        {
            array_pop($this->population);
        }
    }

    private function populate()
    {
        $children=array();
        while(count($this->population)+count($children)<$this->maxPopulation)
        {
            $couple=array_rand($this->population, 2);
            $father = $this->population[$couple[0]];
            $mother = $this->population[$couple[1]];
            //echo $father->getName()."-".$mother->getName()."\n";
            $child=$father->giveMeChild($mother);
            
            // Dedoublonnage
            // TODO : le dedoublonnage entrainer souvent une boucle infinie
            //$key=$child->getResult();
            //$children [$key]=$child;
            
            // Sans dédoublonnage
            $children []=$child;
        }

        $this->population = array_merge($this->population, $children);
    }
}