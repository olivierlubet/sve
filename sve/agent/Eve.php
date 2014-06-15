<?php
namespace sve\agent;

class Eve extends \sve\Agent
{
/*
Basé sur des performances de moyennes mobiles, vends plus vite qu'il n'achète
*/
    public function __construct($yahooId, $period)
    {
        $s=new \sve\serie\Closing($yahooId, $period);
        
        $sBuy=$s->build('MobileAverage',array(9))->build('Performance');
        $sSell=$s->build('MobileAverage',array(3))->build('Performance');


        parent::__construct('Eve',$yahooId, $period,array($sBuy), array($sSell));

    }
}
//Cain Abel Seth