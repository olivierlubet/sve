<?php
namespace sve;

class Agent // extends AbstractSerie
{
	private $name;
	private $generation;
	private $period;
	private $buyStrategy=array();
	private $sellStrategy=array();
	private $security;
	private $countMovements=0;

	/**
	 * @var \sve\Serie
	 */
	private $values = null;
	/**
	 * @var \sve\Serie
	 */
	private $positions = null;

	public function getName() {
		return $this->name;
	}
	public function getGeneration() {
		return $this->generation;
	}
	public function getSecurity() {
		return $this->security;
	}
	public function count() {
		return $this->security->count();
	}
	public function countMovements() {
		return $this->countMovements;
	}
	public function getPeriod() {
		return $this->period;
	}
	public function getBuyStrategy() {
		return $this->buyStrategy;
	}
	public function getSellStrategy() {
		return $this->sellStrategy;
	}
	public function getValues(){
		return $this->values;
	}
	public function getPositions(){
		return $this->positions;
	}

	public function __construct($name, $yahooId, $period, $buyStrategy, $sellStrategy, $generation=1)
	{
		$this->name = $name;
		$this->period = $period;
		$this->generation = $generation;
		$this->security = new \sve\serie\Closing($yahooId,$period);
		$this->buyStrategy=$buyStrategy;
		$this->sellStrategy=$sellStrategy;
		$this->values= new \sve\Serie();
		$this->positions= new \sve\Serie();

		$this->mToString.="Date\t".$yahooId."\t";

		$this->initIterators();
		$this->mToString.="Position\tPerformance\n";

		$this->compute();
	}


	/**
	 *
	 */
	private function compute()
	{

		$current = $this->security->getFirst();

		// TODO : externaliser dans un teneur de compte
		$money = 1;
		$shares = 0;

		while(!is_null($current))
		{
			$this->mToString.=$current->getDate()->format('Y-m-d')."\t";
			$this->mToString.=$current->getValue()."\t";

			$position=$this->evaluatePosition($current);
			//echo $this->positions."\n...........\n";
			//$this->positions->addLast($position);
			$this->mToString.=$position."\t";

			if (!is_null($current->getNext()))
			{
				$tomorrowVal = $current->getNext()->getValue();
				switch ($position)
				{
					case Position.BUY:
						if($money>0){
							$this->countMovements++;
							$shares += $money/$tomorrowVal;
							$money = 0;
						}
						break;
					case Position.SELL:
						if($shares>0){
							$this->countMovements++;
							$money += $shares * $tomorrowVal;
							$shares = 0;
						}
						break;
				}
			}

			$accountValue=$money+$shares*$current->getValue();
			$this->mToString.=$accountValue."\n";

			$this->values->addLast(new \sve\Value(
					$current->getDate(),
					$accountValue
			));

			$current=$current->getNext();
		}
	}


	private function evaluatePosition($current)
	{
		$position=Position.BUY;

		// Buying strategy : All indicators must be green
		foreach($this->buyIterators as &$it) //Warning : passing by reference
		{
			$itCopy=$it;
			$it=$it->getValidValue($current->getDate());
			if (is_null($it)) {
				$it=$itCopy;
				$this->mToString.="\t";
				return Position.HOLD;
			}

			$this->mToString.=$it->getValue()."\t";
			if($it->getValue()<0)
			{
				$position=Position.HOLD;
			}
		}

		// Selling strategy : Only one red indicator needed
		foreach($this->sellIterators as &$it) //Warning : passing by reference
		{
			$itCopy=$it;
			$it=$it->getValidValue($current->getDate());
			if (is_null($it)) {
				$it=$itCopy;
				$this->mToString.="\t";
				return Position.HOLD;
			}

			$this->mToString.=$it->getValue()."\t";
			if($it->getValue()<0)
			{
				$position=Position.SELL;
			}
		}
		return $position;
	}

	private $buyIterators=array();
	private $sellIterators=array();
	private function initIterators()
	{
		$this->buyIterators=array();
		$this->sellIterators=array();

		foreach($this->buyStrategy as $s)
		{
			$this->mToString.=$s->getFullName()."\t";
			$this->buyIterators []= $s->getFirst();
		}

		foreach($this->sellStrategy as $s)
		{
			$this->mToString.=$s->getFullName()."\t";
			$this->sellIterators []= $s->getFirst();
		}
	}

	private $mToString="";
	public function __toString()
	{
		return $this->mToString;
	}

	public function buildAllele()
	{
		$newBuyStrategy=array();
		$newSellStrategy=array();

		foreach($this->buyStrategy as $s)
		{
			$newBuyStrategy []= $s->buildAllele();
		}

		foreach($this->sellStrategy as $s)
		{
			$newSellStrategy []= $s->buildAllele();
		}

		return new Agent(
				$this->getName()."'",
				$this->security->getYahooId(),
				$this->period,
				$newBuyStrategy,
				$newSellStrategy
		);
	}

	// TODO : externaliser
	public function giveMeChild($wife)
	{
		$newName = NameManager::getName();
		$newBuyStrategy = self::buildDerivedStrategy($this->buyStrategy, $wife->buyStrategy);
		$newSellStrategy = self::buildDerivedStrategy($this->sellStrategy, $wife->sellStrategy);
		$newGeneration=max($this->getGeneration(), $wife->getGeneration())+1;

		return new Agent(
				$newName,
				$this->security->getYahooId(),
				$this->period,
				$newBuyStrategy,
				$newSellStrategy,
				$newGeneration
		);
	}

	// 1 or 2
	const MIN_STRATEGY_LENGTH=1;

	public static function buildDerivedStrategy($strategy1, $strategy2)
	{
		$tmp = array_merge($strategy1, $strategy2);
		// Mélange le tableau
		shuffle($tmp);


		// Merging two array of 1 element may result in array of 1 element due to doubles erasing
		if (count($tmp)<1)
		{
			throw new \Exception("Parents doesn't get enough strategy (".count($tmp).")");
		}

		$strategy = array();
		$strategyLentgh=count($tmp);
		if(count($tmp)>self::MIN_STRATEGY_LENGTH)
		{
			$strategyLentgh=rand(self::MIN_STRATEGY_LENGTH,count($tmp));
		}

		for($i=0;$i<$strategyLentgh;$i++)
		{
			$s=$tmp[$i]->buildAllele();
			// Prevent doubles
			$strategy [$s->getFullName()]=$s;
		}

		if (count($strategy)<self::MIN_STRATEGY_LENGTH)
		{
			throw new \Exception("Child doesn't get enough strategy (".count($strategy).")");
		}

		return $strategy;
	}

	public function getResult()
	{
		return $this->getValues()->getLast()->getValue();
	}
}

class Position{
	public static $HOLD=0;
	public static $BUY = 1;
	public static $SELL = -1;
}

class NameManager
{
	static $generation = 0;
	static $names=array();
	static public function getName()
	{
		if (empty(self::$names))
		{
			self::$names=file('ressources/names.csv',FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
			shuffle(self::$names);
			self::$generation++;
		}
		return array_pop(self::$names)." ".self::rome(self::$generation);
	}

	static public function rome($N)
	{
		$c='IVXLCDM';
		for($a=5,$b=$s='';$N;$b++,$a^=7)
		{
			for($o=$N%$a,$N=$N/$a^0;$o--;$s=$c[$o>2?$b+$N-($N&=-2)+$o=1:$b].$s);
		}
		return $s;
	}
}