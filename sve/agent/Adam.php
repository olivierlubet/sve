<?php
namespace sve\agent;

class Adam extends \sve\Agent
{
/*

A > B > C         B > A > C (sell)      B > C > A
A > C > B(buy)    C > A > B             C > B > A

*/
    public function __construct($yahooId, $period)
    {
        $s=new \sve\serie\Closing($yahooId, $period);
        $sA=$s->build('MobileAverage',array(3));
        $sB=$s->build('MobileAverage',array(6));
        $sC=$s->build('MobileAverage',array(12));

        $sBuy=$sA->build('Substract',array($sC));
        $sSell=$sB->build('Substract',array($sA));

        parent::__construct('Adam',$yahooId, $period,array($sBuy), array($sSell));
    }
}