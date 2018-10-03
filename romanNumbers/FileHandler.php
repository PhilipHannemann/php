<?php
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

?>
