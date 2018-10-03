<?php
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

?>
