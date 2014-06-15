<?php
include_once("include.php");

if ($argc < 3 ) {
	?>

C'est une ligne de commande à deux options. Utilisation :
<?php echo $argv[0]; ?> population period 

population Integer : 3 
period Integer : 90

<?php
} else {

	echo "population:".intval($argv[1])."\n";
	echo "period:".intval($argv[2])."\n";

	$w = new sve\World('ACA.PA',intval($argv[1]),intval($argv[2]));

	$century=0;
	$nbToSave=round($argv[1]*0.1)+1;
	while(true)
	{
		echo "computing\n";
		$w->compute(1);
		$century++;

		echo "century:$century\n";


		echo "saving $nbToSave bests\n";
		$pop=$w->getPopulation();
		for($i=0;$i<$nbToSave;$i++)
		{
			$agent= $pop[$i];
			$agent->save();
			echo $agent->getName().":\t".$agent->getLast()->getValue()."\n";
		}
	}
}
?>