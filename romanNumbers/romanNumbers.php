<?php
require_once("RomanNumber.php");
require_once("FileHandler.php");
require_once("Website.php");

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
