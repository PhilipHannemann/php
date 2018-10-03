<?php
/**
 *  Class for checking and converting roman numericals
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

/**
 * Class for managing files
 */
class FileHandler{
  private $fileName;
  private $content;

  function __construct($name){
    $this->fileName = $name;
  }

  public function safeFile(){
    $name = $this->fileName['filename']['name'];
    move_uploaded_file($this->fileName['filename']['tmp_name'], $name);
  }

  public function exists(){
    return $this->fileName;
  }

  public function getNextLine(){
    return fgets($this->content);
  }

  public function open(){
    $this->content = fopen($this->fileName['filename']['tmp_name'], 'r+') or die("Failed to open file");
  }

}

/**
 * Class for showing content in HTML
 */
class Website{

  public function showFileInput(){
    echo <<<_END
        <html><head><title>PHP Form Upload</title></head><body>
        <form method='post' action='romanNumbers.php' enctype='multipart/form-data'>
          Select File: <input type='file' name='filename' size='10'>
          <input type='submit' value='Upload'>
        </form>
_END;
  }
  public function uploadSuccessful(){
    echo "File has been uploaded.<br><br>";
  }

  public function transaltedIntoArabic($line, $val){
    echo "<b>$line</b> in the arabic numerical system equals: <b>$val</b><br>";
  }

  public function transaltionFailed($line){
    echo "<b>$line</b> is not a valid roman numeral!<br>";
  }

  public function close(){
    echo "</body></html>";
  }
}

$webseite = new Website();
$webseite->showFileInput();

$file = new FileHandler($_FILES);

if ($file->exists()){
  $webseite->uploadSuccessful();
  $file->open();

  while ($line = $file->getNextLine()) {
    $romanNumber = new RomanNumber($line);

    if ($romanNumber->isValidRomanNumber() === true) {
      $val = $romanNumber->getIntValue();
      $webseite->transaltedIntoArabic($line, $val);

    }else{
      if (empty(trim($line))) {
        continue;
      }
      $webseite->transaltionFailed($line);
    }
  }

}

$webseite->close();

?>
