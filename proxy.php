<?php
interface Calculator{
    public function add();
    public function subtract();
}


class CashCalculator implements Calculator{
    private $facadeCalculator;
    
    public function __construct($facade){
        $this->facadeCalculator = $facade;
    }
    
    public function add() {
        if(!apc_exists('add-'.json_encode($this->facadeCalculator->getParams()))){
            apc_add('add-'.json_encode($this->facadeCalculator->getParams()), $this->facadeCalculator->add());   
        }
        return apc_exists('add-'.json_encode($this->facadeCalculator->getParams()));
    }
    
    public function subtract() {
        if(!apc_exists('subtract-'.json_encode($this->facadeCalculator->getParams()))){
            apc_add('subtract-'.json_encode($this->facadeCalculator->getParams()), $this->facadeCalculator->subtract());   
        }
        return apc_exists('subtract-'.json_encode($this->facadeCalculator->getParams()));
    }
}


class FacadeCalculator implements Calculator {
    private static $client;
    private $params;
    private function __construct() {}
    public static function getClient(){
        if(self::$client === null){
            self::$client = new SoapClient('http://www.dneonline.com/calculator.asmx?WSDL');
        }
        return self::$client;
    }
    
    public function setParams($a, $b){
        $this->params = array('intA' => $a, 'intB' => $b);
    }
    
    public function add()
    {
        $objResult = $client->Add($this->$params);
        $result = $objResult->AddResult;
        return $result;
    }

    public function subtract()
    {
        $objResult = $client->Subtract($this->$params);
        $result = $objResult->SubtractResult;
        return $result;
    }
}




$client = FacadeCalculator::getClient();
$client->setParams(3, 20);
echo $client->add();
echo $client->subtract(); 

$calc = new CashCalculator($client);
var_dump($calc->add());