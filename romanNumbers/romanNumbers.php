<?php // upload.php
/**
 *
 */

class RomanNumber{

  private $number = "";
  private $validLiterals = array('I' => 1,'V' => 5, 'X' => 10,
  	'L' => 50,	'C' => 100,	'D' => 500,	'M' => 1000);

    function __construct($number){
      $number = trim($number);
      $this->number = str_split($number);
    }

  private function usedKorrektLiterals(){
    foreach ($this->number as $character) {
      if (empty($this->validLiterals[$character])) {
        return false;
      }
    }

    return true;
  }

  private function usedRightDistance(){
     $valueToProof = array_keys($this->validLiterals);

     foreach ($this->number as $pos => $character) {
       $key = array_search($character, $valueToProof);

       $number1 = $this->validLiterals[$character];
       $number2 = $this->validLiterals[$this->number[$pos+1]];

       $validation = $number2/$number1;

       if ($validation > 10) {
         return flase;
       }
     }

     return true;
   }

   private function usedRightOrder(){
      $val;
      $direction = true;
      foreach ($this->number as $pos => $character) {

        if (!empty($val)) {
          if ($this->number[$pos+1] > $character) {
            if ($direction === false) {
              return flase;
            }
            $direction = false;
          }else{
            $direction = true;
          }

        }else{
          $val = $this->number[$character];
        }

      }

      return true;
    }

   private function usedRightFrequency(){
     $count = 0;
     $val = "";
     foreach ($this->number as $character) {

       if($character === $val){
         $count++;
       }else{
         $count = 1;
         $val = $character;
       }

       if ($count > 3) {
         return flase;
       }
     }
     return true;
   }


  public function isValidRomanNumber(){

    if (true !== $this->usedKorrektLiterals()) {
        return false;
    }

    if (true !== $this->usedRightDistance()) {
       return false;
    }

    if (true !== $this->usedRightFrequency()) {
       return false;
    }

    if (true !== $this->usedRightOrder()) {
       return false;
    }

    return true;
  }


  public function getIntValue(){
    $sum;
    foreach ($this->number as $pos => $character) {
      $number = $this->validLiterals[$character];
      $nextNumber = $this->validLiterals[$this->number[$pos+1]];

      if ($number < $nextNumber) {
        $number = -$number;
      }

      $sum += $number;
    }

    return $sum;
  }
}




$romanNumber = new RomanNumber("XCIV");


if ($romanNumber->isValidRomanNumber() === true) {
  echo "Die Zahl ist ok. ";
  $val = $romanNumber->getIntValue();
  echo "Der Wert ist: $val";

}else{
  echo "Die Zahl ist nicht so ganz römisch";
}



?>
