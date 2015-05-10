<?php
/**
 * A CDice class to play dice game.
 *
 */
class CDice {

  private $roll;
  private $totalpoints;

  /**
   * Calling validSession and setting $this->totalpoints to $_SESSION['totalpoints']
   */
  public function __construct() {
    $this->validSession();
    $this->totalpoints = $_SESSION['totalpoints'];
  }

  /**
  * setting $_SESSION['totalpoints']
  */
  public function validSession() {
    $_SESSION['totalpoints'];

    //if $_SESSION['totalpoints'] is null then set $_SESSION['totalpoints'] to 0.
    if($_SESSION['totalpoints'] == null) {
      $_SESSION['totalpoints'] = 0;
    }
  }

  /**
   * Roll the dice
   *
   */
  public function Roll() {
    $this->roll = rand(1, 6);
  }

  /**
   * getRoll returns HTML markup with representing dice-img.
   * @return $html
   */
  public function getRoll() {
    $this->checkRoll();
    $html = '
      <img src="img.php?src=dices/' . $this->roll . '.png" alt="' . $this->roll . '"/>
    ';
    
    //If $this->totalpoints > 29 = WIN.
    if($this->totalpoints > 29) {
      $html = $this->winScreen();
      $_SESSION['totalpoints'] = 0;
    }
    return $html;
  }

  /**
   * checkRoll either resets points or add dice roll to consisting points.
   */
  private function checkRoll() {
    $html = "";
    if($this->roll == 1) {
      $_SESSION['totalpoints'] = null;
      $this->totalpoints = 0;
    } else {
      $this->totalpoints = $this->totalpoints + $this->roll;
      $_SESSION['totalpoints'] = $this->totalpoints;
    }
  }

   /**
   * HTML-markup for winning. Prize: Pulp Fiction (therefore image of that movie)
   * @return $html
   */
  private function winScreen() {
    $html = "
      <p style='color:green; font-size:2em;'>Grattis! Du vann...</p>
      <img src='img.php?src=movie/pulp-fiction.jpg&width=150&height=150' alt='pulp-fiction'/>
      <p>Pulp Fiction</p>
    ";
    return $html;
  }

  /**
   * HTML-markup for gameBoard
   * @return $html
   */
  public function gameBoard() {
    $html = '
          <p>
            <a href="dice.php?game=roll">
              Slå Tärning
            </a>
          </p>

          <p>
            Din totala poäng är: ' . $this->totalpoints . '
          </p>
    ';

    return $html;
  }

}
