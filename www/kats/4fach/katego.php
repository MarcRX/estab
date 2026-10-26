<?php

/*******************************************************************************
  Klasse: kategorien

  Funktionen:
    - Auflisten der Kategorien
    - neu eintragen von Kategorien
    - editieren/ändern der Kategorien
    - löschen von Kategorien
    - speichern der Kategorien
    - Zuweisung einer Kategorie zu einer Meldung

 ********************************************************************************/
class kategorien
{

  public $db_server;
  public $db_benutzer;
  public $db_passwort;
  public $db_name;

  public $db_hndl;

  public $db_tablname;
  public $db_tablnamelk;

  public $db_master_katego;

  public $sqlquery;

  public $result;
  public $resultcount;

  public $redcopy2;
  public $dbtyp;
  public $db_tbl;
  public $grundkatego;

  public $stab_fkt;

  public $bg;
  /*******************************************************************************
  KONSTRUKTOR
   ********************************************************************************/
  public function __construct($table)
  {
    if (debug) {
      echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__;
      echo "<br><b><big><big>*** constructor ***</b></big></big><br>\n";
    }
    include("../4fcfg/config.inc.php");
    include("../4fcfg/dbcfg.inc.php");
    include("../4fcfg/e_cfg.inc.php");
    include("../4fcfg/fkt_rolle.inc.php");
    $this->redcopy2  = $redcopy2;

    if (!isset($_SESSION["vStab_funktion"])) {
      if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start(); // Start session only if not already started
      }
    }

    $this->stab_fkt  = $_SESSION["vStab_funktion"];
    $this->dbtyp = $table;
    switch ($table) {
      case "master":
        $this->db_master_katego = $conf_4f_tbl["masterkatego"];
        $this->db_tablname      = $conf_4f_tbl["masterkatego"];
        $this->db_tablnamelk    = $conf_4f_tbl["masterkategolk"];
        break;
      case "fkt":
        $this->db_tbl = $conf_4f_tbl["usrtblprefix"] . "_fkt_" .
          strtolower($_SESSION["vStab_funktion"]);
        $this->db_tablname   = $this->db_tbl . "_katego";
        $this->db_tablnamelk = $this->db_tbl . "_kategolink";
        break;
      case "user":
        $this->db_tbl = $conf_4f_tbl["usrtblprefix"] .
          strtolower($_SESSION["vStab_funktion"]) . "_" .
          strtolower($_SESSION["vStab_kuerzel"]);
        $this->db_tablname   = $this->db_tbl . "_katego";
        $this->db_tablnamelk = $this->db_tbl . "_kategolink";
        break;
    }

    $this->db_server   = $conf_4f_db["server"];
    $this->db_benutzer = $conf_4f_db["user"];
    $this->db_passwort = $conf_4f_db["password"];
    $this->db_name     = $conf_4f_db["datenbank"];
    $this->grundkatego = array(
      1 => array(
        "kategorie"    => "Alle",
        "beschreibung" => "ohne Berücksichtigung der Kategorien"
      ),
      2 => array(
        "kategorie"    => "ohne",
        "beschreibung" => "Ohne Kategorie"
      )
    );

    $this->db_hndl = mysqli_connect($this->db_server, $this->db_benutzer, $this->db_passwort)
      or die("[connection] katego.php 73 Konnte keine Verbindung zur Datenbank herstellen");
    mysqli_query($this->db_hndl, 'SET NAMES utf8mb4');
    $db_check = mysqli_select_db($this->db_hndl, $this->db_name)
      or die("[read_table] Auswahl der Datenbank fehlgeschlagen");
    /*$result = mysqli_ping  ($this->db_hndl); ab 8.4 depracted*/
    return ($result);
  }

  /*******************************************************************************
   Funktion: lese_kategorien ()
   ********************************************************************************/
  /*  function lese_kategorien (){
    $this->sqlquery = "SELECT * FROM `".$this->db_tablname."` WHERE 1  ORDER BY `kategorie`;";

    $query_result = mysqli_query ($this->db_hndl, $this->sqlquery) or
       die("[query_table] <br>$this->sqlquery<br>103-".mysqli_error($this->db_hndl)." ".mysqli_errno($this->db_hndl));
    $this->resultcount = mysqli_num_rows($query_result);
    $this->result = [];
    for ($i=1; $i<=$this->resultcount; $i++){
      $this->result[$i] = mysqli_fetch_assoc($query_result);
    }
    mysqli_free_result($query_result);
  }*/

  function lese_kategorien()
  {
    if (debug) { echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><big><big><b>LESE_kategorien</b></big></big><br>";  }
    $sql = "SELECT * FROM `{$this->db_tablname}` ORDER BY `kategorie`;";
    $result = mysqli_query($this->db_hndl, $sql);

    if (!$result) {
      throw new Exception(
        "[query_table] {$sql} - " . mysqli_error($this->db_hndl),
        mysqli_errno($this->db_hndl)
      );
    }
    if (debug) {
      echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><br>";
      echo "SQL    = "; var_dump($sql);    echo "<br>\n";
      echo "result = "; var_dump($result); echo "<br>\n";
    }

    $this->result = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $this->result[] = $row;
    }

    $this->resultcount = count($this->result);

    if (debug) {
      echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><br>";
      echo "this->result = ";
      var_dump($this->result);
      echo "<br>\n";
      echo "resultcount  = ";
      var_dump($this->resultcount);
      echo "<br>\n";
    }

    mysqli_free_result($result);
  }


  /*******************************************************************************
   Funktion: db_get ( $lfd )
   ********************************************************************************/
  function db_get($lfd)
  {
    $this->sqlquery = "SELECT `lfd`,`kategorie`,`beschreibung` FROM `" . $this->db_tablname . "`
                        WHERE `" . $this->db_tablname . "`.`lfd` = '" . $lfd . "'
                        LIMIT 1;";

    $query_result = mysqli_query($this->db_hndl, $this->sqlquery) or
      die("[query_table] <br>sqlquery<br>103-" . mysqli_error($this->db_hndl) . " " . mysqli_errno($this->db_hndl));
    $this->result = "";
    $this->resultcount = mysqli_num_rows($query_result);
    $this->result = mysqli_fetch_assoc($query_result);

    return ($this->result);

    mysqli_free_result($query_result);
  }

  /*******************************************************************************
   Funktion: db_get_kategobymsg ()
   ********************************************************************************/
  public function db_get_kategobymsg($lfd)
  {
    if ( debug ){ echo "<b>!File:". __FILE__ ."  Line:". __LINE__ ."  query_usrtable  </b><br>\n"; var_dump ($this->sqlquery); echo "<br>";}

    $this->db_hndl = mysqli_connect($this->db_server,$this->db_benutzer, $this->db_passwort)
       or die ("<b>!File:". __FILE__ ."  Line:". __LINE__ ." Konnte keine Verbindung zur Datenbank herstellen".mysqli_error($this->db_hndl)." ".mysqli_errno($this->db_hndl));

    mysqli_query($this->db_hndl, 'SET NAMES utf8mb4');

    $db_check = mysqli_select_db ($this->db_hndl, $this->db_name)
       or die ("<b>!File:". __FILE__ ."  Line:". __LINE__ ." Auswahl der Datenbank fehlgeschlagen".mysqli_error($this->db_hndl)." ".mysqli_errno($this->db_hndl));

    $this->sqlquery = "SELECT `lfd`,`kategorie`,`beschreibung` FROM `" . $this->db_tablname . "`, `" . $this->db_tablnamelk . "`
                        WHERE `" . $this->db_tablname . "`.`lfd` = (
                        SELECT `katego`
                        FROM `" . $this->db_tablnamelk . "`
                        WHERE `" . $this->db_tablnamelk . "`.`msg` = '" . $lfd . "')
                        LIMIT 1;";
    $query_result = mysqli_query($this->db_hndl, $this->sqlquery) or
      die("[query_table] <br>$this->sqlquery<br>133-" . mysqli_error($this->db_hndl) . " " . mysqli_errno($this->db_hndl));

    $this->resultcount = mysqli_num_rows($query_result);
    $this->result = mysqli_fetch_assoc($query_result);
    
    if (debug) {
      echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><big><big><br>";
      echo "this->sqlquery = "; var_dump($this->sqlquery); echo "<br>";
      echo "query_result   = "; var_dump($query_result); echo "<br>";
      echo "resultcount    = "; var_dump($this->resultcount); echo "<br>";
      echo "this->result   = "; var_dump($this->result);   echo "</big></big><br>";
    }

    return ($this->result);
    mysqli_free_result($query_result);
  }


  /******************************************************************************\
  Funktion : get_data ( $no )

  $no =
    1 - Nur die Kategorien
    2 - Nur die Beschreibungen
    3 - Kategortie und Beschreibung
\******************************************************************************/
  function get_data($no)
  {
    switch ($no) {
      case 1: // Kategorie
        $this->sqlquery = "SELECT `kategorie` FROM `" . $this->db_tablname . "`
                          WHERE 1 ;";
        break;
      case 2: // Beschreibung
        $this->sqlquery = "SELECT `beschreibung` FROM `" . $this->db_tablname . "`
                          WHERE 1 ;";
        break;
      case 3: // Kategorie und Beschreibung
        $this->sqlquery = "SELECT `lfd`,`kategorie`,`beschreibung` FROM `" . $this->db_tablname . "`
                          WHERE 1 ;";
        break;
      case 4: // Lfd, Kategorie
        $this->sqlquery = "SELECT `lfd`,`kategorie` FROM `" . $this->db_tablname . "`
                          WHERE 1 ;";
        break;
      case 5: // Lfd, Beschreibung
        $this->sqlquery = "SELECT `lfd`,`beschreibung` FROM `" . $this->db_tablname . "`
                          WHERE 1 ;";
        break;
      default:
        $this->sqlquery = "SELECT `lfd`,`kategorie`,`beschreibung` FROM `" . $this->db_tablname . "`
                          WHERE 1 ;";
        break;
    }
    $query_result = mysqli_query($this->db_hndl, $this->sqlquery) or
      die("[query_table] <br>query<br>103-" . mysqli_error($this->db_hndl) . " " . mysqli_errno($this->db_hndl));

    $this->resultcount = mysqli_num_rows($query_result);

    for ($i = 0; $i < $this->resultcount; $i++) {
      $this->result[$i] = mysqli_fetch_assoc($query_result);
    }
    return ($this->result);
    mysqli_free_result($query_result);
  }



  /*******************************************************************************
   Funktion: db_aendern ()
   ********************************************************************************/
  function db_aendern($lfd, $kategorie, $beschreibung)
  {
    $this->sqlquery = "UPDATE `" . $this->db_tablname . "`
                    SET   `kategorie` = \"" . $kategorie . "\",
                          `beschreibung` = \"" . $beschreibung . "\"
                    WHERE `lfd` = \"" . $lfd . "\";";
    $query_result = mysqli_query($this->db_hndl, $this->sqlquery) or
      die("[query_table] <br>$this->sqlquery<br>103-" . mysqli_error($this->db_hndl) . " " . mysqli_errno($this->db_hndl));
  }


  /*******************************************************************************
   Funktion: db_neu()
   ********************************************************************************/
  function db_neu($kategorie, $beschreibung)
  {
    $this->sqlquery = "INSERT INTO `" . $this->db_tablname . "`
                       SET `kategorie` = \"" . $kategorie . "\",
                           `beschreibung` = \"" . $beschreibung . "\"";

    $query_result = mysqli_query($this->db_hndl, $this->sqlquery) or
      die("[query_table] <br>$this->sqlquery<br>103-" . mysqli_error($this->db_hndl) . " " . mysqli_errno($this->db_hndl));
  }

  /*******************************************************************************
   Funktion: db_delete()
   ********************************************************************************/
  function db_delete($lfd)
  {
    $this->sqlquery = "DELETE FROM `" . $this->db_tablname . "`
                        WHERE `" . $this->db_tablname . "`.`lfd` = \"" . $lfd . "\"
                        LIMIT 1;";

    $query_result = mysqli_query($this->db_hndl, $this->sqlquery) or
      die("[query_table] <br>$this->sqlquery<br>103-" . mysqli_error($this->db_hndl) . " " . mysqli_errno($this->db_hndl));
  }


  /*******************************************************************************
   Funktion: liste_kategorien ()
   ********************************************************************************/
  function liste_kategorien()
  {
    include("../4fcfg/config.inc.php");

    if (debug) { echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "<big><big><b>liste_kategorien</b></big></big><br>";  }
    $this->lese_kategorien(); // hole die Liste der Kategorien
    

    if ($this->result != NULL) {
      if (debug) {
        echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b>";
        echo "<br><b><big><big>*** MARKE ***</b></big></big><br>\n";
        echo "<br><b><big><big>this->result = ";
        var_dump($this->result);
        echo "</b></big></big><br>\n";
      }

      // Tabelle bauen
      echo "<FORM action=\"" . $_SERVER['PHP_SELF'] . "\" method=\"get\" target=\"_self\">\n";

      echo "<TABLE border=\"1\" cellspacing=\"5\" cellpeding=\"50\">\n";
      echo "<THEAD>\n";
      echo "<TR>\n";
      echo "<TH>Kategorie</TH>\n";
      echo "<TH>Beschreibung</TH>\n";
      echo "<TH>Aktion</TH>\n";
      echo "</TR>\n";
      echo "</THEAD>\n";
      echo "<TBODY>\n";
      //     for ($i=1; $this->result[$i]!= NULL; $i++){
      foreach ($this->result as $res) {
        echo "<TR>\n";
        echo "<TD>\n";
        echo "<big><b>" . $res['kategorie'] . "</b></big>\n";
        echo "</TD>\n";
        echo "<TD>\n";
        echo "<big>" . $res['beschreibung'] . "</big>\n";
        echo "</TD>\n";
        echo "<TD>\n";
        echo "<TABLE>";
        echo "<TBODY>";
        echo "<TR>";
        echo "<TD align=\"center\">";
        echo "<a href=\"" . $_SERVER['PHP_SELF'] . "?kate_todo=editrecord&lfd=" . $res['lfd'] . "&dbtyp=" . $this->dbtyp . "\">";
        echo "<img class=\"icon\" width=\"16\" height=\"16\" src=\"" . $conf_design_path . "/edit.gif\" alt=\"editieren\" title=\"Edit\" border=\"0\" /></a>";
        echo "</TD>";
        echo "<TD align=\"center\">";
        echo "<a href=\"" . $_SERVER['PHP_SELF'] . "?kate_todo=deleterecord&lfd=" . $res['lfd'] . "&dbtyp=" . $this->dbtyp . "\">";
        echo "<img class=\"icon\" width=\"16\" height=\"16\" src=\"" . $conf_design_path . "/delete.gif\" alt=\"l&ouml;schen\" title=\"L&ouml;schen\" border=\"0\" /></a>";
        echo "</TD>";
        echo "</TR>";
        echo "</TBODY>";
        echo "</TABLE>";
        echo "</TD>";
        echo "</TR>\n";
      }
      echo "</TBODY>\n";
      echo "</TABLE>\n";
      echo "</FORM>\n";
    }
  }

  /*******************************************************************************
   Funktion: zeige_kategorien ()
   ********************************************************************************/
  function zeige_kategorien($lfd)
  {
    include("../4fcfg/config.inc.php");
    $this->db_get($lfd); // hole die Liste der Kategorien

    if ($this->result != NULL) {

      echo "<td style=\"width: 90px; background-color: " . $this->bg[9] . ";\">\n";
      echo "<div style=\"text-align: center; font-size:24px; font-weight:900;\"><big><big><b>";
      echo $this->result["kategorie"];
      echo "</b></big></big></div>";
    }
  }

  /*******************************************************************************
   Funktion: pulldown_kategorien ()
   ********************************************************************************/
  function pulldown_kategorien($katego_no, $mit_leer, $ordnum)
  {
    if (debug) {
      echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "<big><big><b>pulldown_kategorien</b></big></big><br>";
      echo "katego_no ="; var_dump($katego_no); echo "<br>" ;
      echo "mit_leer  ="; var_dump($mit_leer); echo "<br>" ;
      echo "ordnum    ="; var_dump($ordnum); echo "<br>" ;
    }

    include("../4fcfg/config.inc.php");

    $this->lese_kategorien(); // hole die Liste der Kategorien

    if (debug) {
      echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "<big><big><b>pulldown_kategorien</b></big></big><br>";
      echo "this_result  = "; var_dump($this->result); echo "<br><br>";
    }

    if ($this->result != NULL) {
      //echo "\n<select ".$param." name=\"kategorien_".$this->dbtyp."_".$ordnum."\">\n";
      echo "\n<select name=\"kategorien_" . $this->dbtyp . "_" . $ordnum . "\">\n";

      if ($mit_leer) {
        if ($katego_no == "") {
          $sel = " selected ";
        } else {
          $sel = "";
        }
        echo "<option value=\"\" " . $sel . ">  </option>\n";
      }
      $kategoselected = "";
      foreach ($this->result as $katego) {
        if ($katego_no == $katego["lfd"]) {
          $sel = " selected ";
          $kategoselected = $katego["kategorie"];
        } else {
          $sel = "";
        }
        echo "<option value=\"" . $katego["kategorie"] . "\" " . $sel . ">" . $katego["kategorie"] . "</option>\n";
      }
      echo "</select>\n";

      echo "<input type=\"hidden\" name=\"kategorien_" . $this->dbtyp . "\" value=\"" . $kategoselected . "\">\n";
    }
  }

  /*******************************************************************************\

\*******************************************************************************/
  function eingabezeile($posttask, $lfd, $kategorie, $beschreibung)
  {
    include("../4fcfg/config.inc.php");
    echo "<FORM action=\"" . $_SERVER['PHP_SELF'] . "\" method=\"get\" target=\"_self\">\n";
    echo "<TABLE border=\"1\" cellspacing=\"5\" cellpeding=\"5\">\n";
    echo "<THEAD>\n";
    echo "<TR>\n";
    echo "<TH>Kategorie</TH>\n";
    echo "<TH>Beschreibung</TH>\n";
    echo "<TH>Aktion</TH>\n";
    echo "<TH>Aktion</TH>\n";
    echo "</TR>\n";
    echo "</THEAD>\n";
    echo "<TBODY>";
    echo "<TR>\n";
    echo "<TD>\n";
    echo "<input type=\"hidden\" name=\"kate_todo\" value=\"" . $posttask . "\">\n";
    echo "<input type=\"hidden\" name=\"kate_tbl\" value=\"" . $this->db_tablname . "\">\n";
    echo "<input type=\"hidden\" name=\"kate_dbtbl\" value=\"" . $this->dbtyp . "\">\n";
    echo "<input type=\"hidden\" name=\"lfd\" value=\"" . $lfd . "\">\n";

    echo "<input style=\"font-size:16px; font-weight:900;\" maxlength=\"10\" size=\"10\" name=\"kategorie\" value=\"" . $kategorie . "\">\n";
    echo "</TD>\n";
    echo "<TD>\n";
    echo "<input style=\"font-size:16px; font-weight:900;\" maxlength=\"254\" size=\"50\" name=\"beschreibung\" value=\"" . $beschreibung . "\">\n";
    echo "</TD>\n";
    echo "<TD align=\"center\" bgcolor=$color_button_ok>";
    echo "<input type=\"image\" name=\"katego_absenden\" src=\"" . $conf_design_path . "/ok.gif\" alt=\"OK\">\n";
    echo "</TD>";
    echo "<TD align=\"center\" bgcolor=$color_button_nok>";
    echo "<input type=\"image\" name=\"katego_abbrechen\" src=\"" . $conf_design_path . "/cancel.gif\" alt=\"Abbrechen\">\n";
    echo "</TD>";
    echo "</TR>\n";
    echo "</TBODY>\n";
    echo "</TABLE>\n";
    echo "</FORM>";
  }


  /******************************************************************************\

    ?kate_todo=
       neu      &$_GET["kategorie"]
                 $_GET["beschreibung"]

       delete   &$_GET["lfd"]

       edit     &$_GET["lfd"]

       update   &$_GET ["lfd"], $_GET ["kategorie"], $_GET ["beschreibung"]

       pulldown

       zeige   &lfd=

\******************************************************************************/
  function kategorie_menue(
    $todo,
    $katego_lfd,
    $kategorie,
    $beschreibung
  ) {
    if (isset($todo)) {
      switch ($todo) {
        case "liste": // Liste der Kategorien und Eingabemöglichkeit einer neuen
          $this->liste_kategorien();
          $this->eingabezeile("neu", "", "", "");

          break;

        case "neu":
          // INSERT
          if ($kategorie != "") {
            $this->db_neu($kategorie, $beschreibung);
          }
          if (!debug) header("Location: " . $_SERVER['PHP_SELF']);
          break;
        case "deleterecord":
          $this->db_delete($katego_lfd);
          break;
        case "editrecord":
          $this->db_get($katego_lfd);

          $this->eingabezeile(
            "update",
            $this->result["lfd"],
            $this->result["kategorie"],
            $this->result["beschreibung"]
          );
          break;
        case "update":
          $this->db_aendern($katego_lfd, $kategorie, $beschreibung);
          break;
        case "pulldown":
          $this->pulldown_kategorien("zeigen", true, 1);
          break;
        case "zeige":
          $this->zeige_kategorien($katego_lfd);
          break;
      } // switch
    }
  }



  /*******************************************************************************
   Funktion: dblk_neu()
   INSERT INTO nv_masterkategolink
SET `msg` = "3", `katego` = (

SELECT lfd FROM nv_masterkatego WHERE nv_masterkatego.kategorie = "EA1")
   ********************************************************************************/
  function dblk_neu($msg_no, $kategorie)
  {
    $this->sqlquery = "INSERT INTO `" . $this->db_tablnamelk . "`
                       SET `msg`    = \"" . $msg_no . "\",
                           `katego` = (
                       SELECT `lfd` FROM `" . $this->db_tablname . "`
                       WHERE `" . $this->db_tablname . "`.`kategorie` = \"" . $kategorie . "\");";

    if (debug) {
      echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b>QUERY dblk_neu=" . $this->sqlquery . "<br>";
    }

    $query_result = mysqli_query($this->db_hndl, $this->sqlquery) or
      die("[query_table] <br>$this->sqlquery<br>460-" . mysqli_error($this->db_hndl) . " " . mysqli_errno($this->db_hndl));
  }

  /*******************************************************************************
   Funktion: dblk_aendern ()
   ********************************************************************************/
  function dblk_aendern($msg_no, $kategorie)
  {
    $this->sqlquery = "UPDATE " . $this->db_tablnamelk . "
                    SET   `katego` = (
                       SELECT `lfd` FROM `" . $this->db_tablname . "`
                       WHERE `" . $this->db_tablname . "`.`kategorie` = \"" . $kategorie . "\")
                    WHERE `msg` = \"" . $msg_no . "\";";

    if (debug) {
      echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b>dblk_aendern_QUERY=====<br><br>";
      var_dump($this->sqlquery);
      echo "<br><br>";
    }

    $query_result = mysqli_query($this->db_hndl, $this->sqlquery) or
      die("[query_table] <br>$this->sqlquery<br>103-" . mysqli_error($this->db_hndl) . " " . mysqli_errno($this->db_hndl));
  }

  /*******************************************************************************
   Funktion: dblk_aendern ()
   DELETE FROM `estab_25082008`.`nv_masterkategolink` WHERE `nv_masterkategolink`.`msg` = 1 LIMIT 1
   ********************************************************************************/
  function dblk_loeschen($msg_no)
  {
    $this->sqlquery = "DELETE FROM `" . $this->db_tablnamelk . "`
                        WHERE `msg` = \"" . $msg_no . "\";";

    if (debug) {
      echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><br><br>QUERY==="; var_dump($this->sqlquery); echo "<br><br>";
    }

    $query_result = mysqli_query($this->db_hndl, $this->sqlquery) or
      die("[query_table] <br>$this->sqlquery<br>103-" . mysqli_error($this->db_hndl) . " " . mysqli_errno($this->db_hndl));
  }
} // class kategorien
