<?php
/**
 * Class for showing content in HTML
 */
class Website{


  public function close(){
    echo "</body></html>";
  }

  private function head(){
    echo <<<_END
          <title>Malware Detection</title>
          <link rel='stylesheet' href='style.css'>
          <script src="script.js"></script>

          <center>
            <h1>Malware Detection</h1>
          </center>
_END;
  }


  public function signUpPage(){
    $this->head();

    echo <<<_END

          <br><br><br>

          <form method="post" action="validate.php" onsubmit="return validate(this)" align="center">
            <table border="0" cellpadding="2" bgcolor="#eeeeee" align="center">
              <th colspan="2" align="center">Please signup:<th>
              <tr>
                <td>UserName:</td>
                <td><input type="text" maxlength="20" name="username" value=""></td>
              </tr>
              
              <tr>
                <td>Email:</td>
                <td><input type="text" maxlength="32" name="email" value=""></td>
              </tr>
              <tr>
                <td>Password:</td>
                <td><input type="text" maxlength="32" name="password" value=""></td>
              </tr>
              <tr>
                <td colspan="2" align="center">
                  <input type="submit" value="signup">
                </td>
              </tr>
            </table>
          </form> 

_END;


  }

}

?>
