<?php
require_once("RomanNumber.php");
/**
 * Class for testing RomanNumber Class on correctness
 */
class RomanTester{
  private $romanNumbers = array("CXII" => 112,
                        "MDCCLXXVI" => 1776,
                        "MCMLIV" => 1954,
                        "MCMXC" => 1990,
                        "MMXIV" => 2014,
                        "XXXIX" => 39,
                        "WRONG" => false,
                        "CIA" => false,
                        "IM" => false,
                        "MCCCCIV" => false);

  public function testSamples(){
      foreach ($this->romanNumbers as $roman => $value) {
        $number = new RomanNumber($roman);

        if($number->isValidRomanNumber() === true){
          $val = $number->getIntValue();

          if ($val == $value) {
            echo "<font color='green'>ok! </font>right calculation <br>";
          }else{
            echo "<font color='red'>error: </font>wrong calculation <br>";
          }
        }else{
          if ($value == false) {
             echo "<font color='green'>ok! </font>Found out that the roman number is wrong. <br>";
          }else{
             echo "<font color='red'>error: </font>Didn't find out that the roman number is wrong. <br>";
          }
        }
      }
  }

}

$test = new RomanTester();
$test->testSamples();


?>
