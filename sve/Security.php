<?php
namespace sve;

class YahooCol
{
    //Date,Open,High,Low,Close,Volume,Adj Close
    const Date=0;
    const Open=1;
    const High=2;
    const Low=3;
    const Close=4;
    const Volume=5;
    const AdjClose=6;
}

class Security implements \Countable
{
    //http://ichart.finance.yahoo.com/table.csv?s=ACA.PA&a=11&b=14&c=2001&d=03&e=22&f=2014&g=d&ignore=.csv
    //http://ichart.yahoo.com/table.csv?s=%s&a=%s&b=%s&c=%s&g=d
    const YAHOO_HIST_URL = 'http://ichart.finance.yahoo.com/table.csv?s=%s&a=%s&b=%s&c=%s&d=%s&e=%s&f=%s&g=d&ignore=.csv';


    private $yahooId;
    private $data = array();
    private $from;
    private $period;

    public function getYahooId(){return $this->yahooId;}

    public function __construct(string $yahooId, $period)
    {
        
        if (!is_int($period) || $period<10) throw new \Exception ("Period must be an integer > 10, get [$period]");
        
        $this->period = $period;
        $this->yahooId = $yahooId;
        $this->from = new \DateTime();
        $this->from->sub(new \DateInterval('P'.$period.'D'));
        
        $this->dl();

        $this->load();
    }

    public function getYahooUrl()
    {
        $today=new \DateTime();
        return sprintf (self::YAHOO_HIST_URL,
            $this->getYahooId(),
            $this->from->format('m')-1,
            $this->from->format('d'),
            $this->from->format('Y'),
            $today->format('m')-1,
            $today->format('d'),
            $today->format('Y'));
    }

    public function getDlFilename()
    {
        return 'ressources/dl/'.$this->getYahooId().'.'.$this->period;
    }

    public function getDlDate()
    {
        return date('Y-m-d',filemtime($this->getDlFilename()));
    }

    public function count()
    {
        return count($this->data);
    }

    public function getData()
    {
        return $this->data;
    }

    private function dl()
    {
        if (file_exists($this->getDlFilename()))
        {
            $today = date('Y-m-d');
            if ($today == $this->getDlDate()) return;
        }
        $url = $this->getYahooUrl();
        $outputfile = $this->getDlFilename();
        $cmd = "wget -q \"$url\" -O $outputfile";
        exec($cmd);
    }

    private function load()
    {
        $filename = $this->getDlFilename();

        if (($handle = fopen($filename, "r")) !== FALSE) {
            $row = 1;
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if ($row>1) // first row contain header
                {
                    $this->data[]=$data;
                    //print_r($data[YahooCol::Date] . " - " . $data[YahooCol::Close]."\r");
                }

                $row++;
            }
        fclose($handle);
        }
    }

}