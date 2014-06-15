<?php
namespace sve\agent;

class CainBest extends \sve\Agent
{
/*
Observation de Sinus, dérivée en rapport (%) et dérivée en valeur
-> achat quand s'>s''
-> vente quand s>s'
Sinus est entre -1 et 1.

Transposé à une valeur financière
s' -> Performance
s'' -> Derivée de s'
s -> s'' * (-1)

Suite à expérimentations, usage des MM 2 et 4 en lieu et place d'une unique MM6
*/
    public function __construct($yahooId, $period)
    {
        $s=new \sve\serie\Closing($yahooId, $period);

        $s1=$s->build('MobileAverage',array(2))->build('Performance');
        $s2=$s1->build('Derived');

        $s3=$s2->build('Multiply',array(-1));
        $s4=$s->build('MobileAverage',array(4))->build('Performance');

        $sBuy=$s1->build('Substract',array($s2));
        $sSell=$s3->build('Substract',array($s4));

        parent::__construct('CainB',$yahooId, $period,array($sBuy), array($sSell));

    }
}