<?php

include("katego.php");
include("../4fcfg/e_cfg.inc.php");

/*****************************************************************************\
   Datei: liste.php

   benötigte Dateien:

   Beschreibung:

   (C) Hajo Landmesser
   mailto://hajo.landmesser@eStab.de
\*****************************************************************************/
if (debug) {
  echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b>  Vor Klassendeklaration <br>";
}

class Listen extends kategorien
{
  /******************************************************************************\
   $welche ~= Art der Liste die Ausggeben werden soll. Möglich sind:
     FMA    - Fernmeldeausgangsliste
     STUSER - Stabbenutzer
     STSI   - Stab Sichter
     FMNWE  - Fernmelde Nachweis Eingang
     FMNWA  - Fernmelde Nachweis Ausgang
     ADMIN  - Administrative Liste
\******************************************************************************/

  public $listenart;
  public $benutzer;
  public $flt_status;
  public $flt_msg_pro_seite;
  public $flt_start_msg;
  public $flt_gelesen;
  public $flt_erledigt;
  public $stab_fkt;
  public $db_tbl;

  /***************************************************************************/

  public $query;
  public $query_select;
  public $query_from_arg;
  public $query_from_ma_arg;
  public $query_from_fk_arg;
  public $query_from_us_arg;



  // Listengestaltung

  /******************************************************************************\

\******************************************************************************/
  public  function explodereceiver($empf)
  {
    $receiver = explode(",", $empf);
    for ($i = 0; $i < count($receiver); $i++) {
      $hilfeaus = explode("_", $receiver[$i]);
      $fktcopycolor[$hilfeaus[0]] = $hilfeaus[1] ?? '';
    }
    return $fktcopycolor;
  }



  /******************************************************************************\

\******************************************************************************/
  public  function __construct($welche, $user)
  {
    if (debug) {
      echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><big><big><big><big>Liste construct  ".$welche."</big></big></big></big><br>";
    }
    $this->listenart = $welche;
    $this->benutzer  = $user;
    $this->flt_status = $_SESSION["filter_darstellung"] ?? null;
    $this->flt_msg_pro_seite = $_SESSION["filter_anzahl"] ?? null;
    $this->flt_start_msg = $_SESSION["startmit"] ?? null;
    $this->flt_gelesen = $_SESSION["gelesene"] ?? null;
    $this->flt_erledigt = $_SESSION["erledigte"] ?? null;

    /*    if (isset($_SESSION["filter_darstellung"])) { $this->flt_status   = $_SESSION["filter_darstellung"]; } else { $this->flt_status = NULL; } ;
    if (isset($_SESSION["filter_anzahl"])) { $this->flt_msg_pro_seite = $_SESSION["filter_anzahl"] ;     } else { $this->flt_msg_pro_seite = NULL; } ;
    if (isset($_SESSION["startmit"])) { $this->flt_start_msg          = $_SESSION["startmit"];           } else { $this->flt_start_msg = NULL; } ;
    if (isset($_SESSION["gelesene"])) { $this->flt_gelesen            = $_SESSION["gelesene"] ;          } else { $this->flt_gelesen  = NULL; } ;
    if (isset($_SESSION["erledigte"])) { $this->flt_erledigt          = $_SESSION["erledigte"] ;         } else { $this->flt_erledigt = NULL; } ;*/
  }

  /******************************************************************************\

\******************************************************************************/
  public  function darstellungs_art($what)
  {

    include("../4fcfg/config.inc.php");

    switch ($what) {    // $this->listenart
      /*************************************************************************\
                               FFFFF M   M  AAA
                               F     MM MM A   A
                               FFF   M M M AAAAA
                               F     M   M A   A
                               F     M   M A   A
      \*************************************************************************/
      case "FMA":
        /***** F M A ****/
        break;

      /*************************************************************************\
        SSSSS  TTTTT   AAA  BBBB   l
        S        T    A   A B   B  l
        SSSSS    T    AAAAA BBBBB  l 
            S    T    A   A B   B  l
        SSSSS    T    A   A BBBB   l esen
      \*************************************************************************/
      case "Stab_lesen":  // ******  S T A B    l e s e n *****
        if (debug) {
          echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b> case Stab lesen <br>";
        }

        echo "\n<form action=\"" . $conf_4f["MainURL"] . "\" method=\"GET\" target=\"mainframe\">\n";
        echo "<table><tbody>";
        echo "<tr>";
        echo "<td>";
        echo "<big><b>" . ($_SESSION["filter_start"] + 1) . "|" . ($_SESSION["filter_start"] + $_SESSION["filter_anzahl"]) . "|<big>" . ($_SESSION["filter_rescount"]) . "</big></b></big>";
        echo "</td>";
        echo "<td>";
        echo "Meldung/Seite:<br>\n";

        // Voreinstellung für die Meldungen pro Seite
        if (empty($_SESSION["filter_anzahl"])) {
          $_SESSION["filter_anzahl"] = 5;
        }

        echo "<table border=\"0\" ><tbody>";
        echo "<tr>";

        echo "<td>";
        echo "<div  style=\"border-top-color:#DCDCFF; border-left-color:#DCDCFF; border-right-color:#DCDCFF; border-bottom-color:#000000; border-width:1px; border-style:solid; padding:0px\">";
        for ($pps = 5; $pps <= 25; $pps += 5) {
          if ($_SESSION["filter_anzahl"] == $pps) {
            echo "<a href=\"" . $conf_4f["MainURL"] . "?filter_anzahl_x=1&filter_anzahl=" . $pps . "\"><img src=\"button.php?type=icon&status=AUS&text=" . $pps . "&bg=blue\" border=\"0\" alt=\"Anzahl" . $pps . "EIN\"></a>";
          } else {
            echo "<a href=\"" . $conf_4f["MainURL"] . "?filter_anzahl_x=1&filter_anzahl=" . $pps . "\"><img src=\"button.php?type=icon&status=EIN&text=" . $pps . "&bg=lighterblue\" border=\"0\" alt=\"Anzahl" . $pps . "AUS\"></a>";
          }
        }
        echo "</div>";
        echo "</td>";
        echo "</tr>";
        echo "</tbody></table>";
        echo "</td>";
        echo "<td>";
        if ($_SESSION["filter_unerledigt"] == 0) {
          echo "<div>";
          echo "<input type=\"image\" name=\"filter_unerledigt_ein\" src=\"button.php?type=push&textpos=buttom&status=AUS&text=un-\" alt=\"Filter: für mein Sachgebiet unerledigt\" title=\"Filter: für mein Sachgebiet unerledigt\">\n";
          echo "</div>";
        } else {
          echo "<div>";
          echo "<input type=\"image\" name=\"filter_unerledigt_aus\" src=\"button.php?type=push&textpos=buttom&status=EIN&text=un-\" alt=\"Filter: für mein Sachgebiet unerledigt\" title=\"Filter: für mein Sachgebiet unerledigt\">\n";
          echo "</div>";
        }
        echo "</td>";
        echo "<td>";
        if ($_SESSION["filter_erledigt"] == 0) {
          echo "<div>";
          echo "<input type=\"image\" name=\"filter_erledigt_ein\" src=\"button.php?type=push&textpos=buttom&status=AUS&text=erledigt\" alt=\"Filter: für mein Sachgebiet erledigt\" title=\"Filter: für mein Sachgebiet erledigt\">\n";
          echo "</div>";
        } else {
          echo "<div>";
          echo "<input type=\"image\" name=\"filter_erledigt_aus\" src=\"button.php?type=push&textpos=buttom&status=EIN&text=erledigt\" alt=\"Filter: für mein Sachgebiet erledigt\" title=\"Filter: für mein Sachgebiet erledigt\">\n";
          echo "</div>";
        }
        echo "</td>";
        echo "<td>";
        if ((isset($_SESSION["flt_find_mask"])) && ($_SESSION["flt_find_mask"] == 0)) {
          echo "<div>";
          echo "<input type=\"image\" name=\"flt_find_mask_ein\" src=\"button.php?type=push&textpos=buttom&status=AUS&text=finden\" alt=\"In Nachrichten suchen\" title=\"In Nachrichten suchen\">\n";
          echo "</div>";
        } else {
          echo "<div>";
          echo "<input type=\"image\" name=\"flt_find_mask_aus\" src=\"button.php?type=push&textpos=buttom&status=EIN&text=finden\" alt=\"In Nachrichten suchen\" title=\"In Nachrichten suchen\">\n";
          echo "</div>";
        }
        echo "</td>";
        echo "</form>\n";

        echo "<!-- liste.php 233 -->";
        echo "<td>";
        echo "<table><tbody>";
        echo "<tr>";
        if ((isset($_SESSION["flt_find_mask"])) && ($_SESSION["flt_find_mask"] == 1)) {
          echo "\n<form action=\"" . $conf_4f["MainURL"] . "\" method=\"GET\" target=\"mainframe\">\n";
          echo "<td>";
          if (isset($_SESSION["flt_search"])) {
            $defvalue = $_SESSION["flt_search"];
          } else {
            $defvalue = "";
          }
          echo "<div>";
          echo "<p>Suchbegriff: <input name=\"flt_search\" value=\"" . $defvalue . "\" type=\"text\" size=\"30\" maxlength=\"30\" onkeydown=\"if(event.key==='Enter'){event.preventDefault();this.form.requestSubmit();}if(event.key==='Escape'){event.preventDefault();var b=document.querySelector('input[name=flt_find_mask_aus]');if(b)b.click();}\"></p>";
          echo "<div>";
          echo "</td>";
          echo "<td>";
          echo "<input name=\"filter_suche\" value=\"suchen\" type=\"submit\">\n";
          echo "</td>";
          echo "</form>\n";
          echo "</div>";
        }
        echo "</tr>";
        echo "</tbody></table>";
        echo "</td>";
        echo "</tr>";
        echo "</tbody></table>";
        break;


      case "Stab_sichten":
        /*********** S t a b   s i c h t e n ************/
        break;
      /*************************************************************************\
               SSSSS III  AAA  DDDD  M   M III N   N
               S      I  A   A D   D MM MM  I  NN  N
               SSSSS  I  AAAAA D   D M M M  I  N N N
                   S  I  A   A D   D M   M  I  N  NN
               SSSSS III A   A DDDD  M   M III N   N
      \*************************************************************************/
      case "SIADMIN":  // ***************  SICHTER ADMINISTRATOR  *********************
      case "FMADMIN":
        if (debug) {
          echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b>  fkt:darstellungsart - switch (this->listenart):SIADMIN/FMADMIN <br>";
        }
        echo "\n<form action=\"" . $conf_4f["MainURL"] . "\" method=\"GET\" target=\"mainframe\">\n";
        echo "<table><tbody>";
        echo "<tr>";
        echo "<td>";
        echo "<big><b>" . ($_SESSION["filter_start"] + 1) . "|" . ($_SESSION["filter_start"] + $_SESSION["filter_anzahl"]) . "|<big>" . ($_SESSION["filter_rescount"]) . "</big></b></big>";
        echo "</td>";
        echo "<td>";
        echo "Meldung/Seite:<br>\n";

        // Voreinstellung für die Meldungen pro Seite
        if (
          !(isset($_SESSION["filter_anzahl"])) or
          ($_SESSION["filter_anzahl"] == "")
        ) {
          $_SESSION["filter_anzahl"] = 5;
        }

        echo "<table border=\"0\" ><tbody>";
        echo "<tr>";

        echo "<td>";
        echo "<div  style=\"border-top-color:#DCDCFF; border-left-color:#DCDCFF; border-right-color:#DCDCFF; border-bottom-color:#000000; border-width:1px; border-style:solid; padding:0px\">";
        for ($pps = 5; $pps <= 25; $pps += 5) {
          if ($_SESSION["filter_anzahl"] == $pps) {
            echo "<a href=\"" . $conf_4f["MainURL"] . "?filter_anzahl_x=1&filter_anzahl=" . $pps . "\"><img src=\"button.php?type=icon&status=AUS&text=" . $pps . "&bg=blue\" border=\"0\" alt=\"Anzahl" . $pps . "EIN\"></a>";
          } else {
            echo "<a href=\"" . $conf_4f["MainURL"] . "?filter_anzahl_x=1&filter_anzahl=" . $pps . "\"><img src=\"button.php?type=icon&status=EIN&text=" . $pps . "&bg=lighterblue\" border=\"0\" alt=\"Anzahl" . $pps . "AUS\"></a>";
          }
        }
        echo "</div>";
        echo "</td>";
        echo "</tr>";
        echo "</tbody></table>";
        echo "</td>";


        echo "<td>";
        if ((isset($_SESSION["flt_find_mask"])) && ($_SESSION["flt_find_mask"] == 0)) {
          echo "<div>";
          echo "<input type=\"image\" name=\"flt_find_mask_ein\" src=\"button.php?type=push&textpos=buttom&status=AUS&text=finden\" alt=\"In Nachrichten suchen\" title=\"In Nachrichten suchen\">\n";
          echo "</div>";
        } else {
          echo "<div>";
          echo "<input type=\"image\" name=\"flt_find_mask_aus\" src=\"button.php?type=push&textpos=buttom&status=EIN&text=finden\" alt=\"In Nachrichten suchen\" title=\"In Nachrichten suchen\">\n";
          echo "</div>";
        }
        echo "</td>";
        echo "</form>\n";

        echo "<!-- liste.php 233 -->";
        echo "<td>";
        echo "<table><tbody>";
        echo "<tr>";
        if ((isset($_SESSION["flt_find_mask"])) && ($_SESSION["flt_find_mask"] == 1)) {
          echo "\n<form action=\"" . $conf_4f["MainURL"] . "\" method=\"GET\" target=\"mainframe\">\n";
          echo "<td>";
          if (isset($_SESSION["flt_search"])) {
            $defvalue = $_SESSION["flt_search"];
          } else {
            $defvalue = "";
          }
          echo "<div>";
          echo "<p>Suchbegriff: <input name=\"flt_search\" value=\"" . $defvalue . "\" type=\"text\" size=\"30\" maxlength=\"30\" onkeydown=\"if(event.key==='Enter'){event.preventDefault();this.form.requestSubmit();}if(event.key==='Escape'){event.preventDefault();var b=document.querySelector('input[name=flt_find_mask_aus]');if(b)b.click();}\"></p>";
          echo "<div>";
          echo "</td>";
          echo "<td>";
          echo "<input name=\"filter_suche\" value=\"suchen\" type=\"submit\">\n";
          echo "</td>";
          echo "</form>\n";
          echo "</div>";
        }
        echo "</tr>";
        echo "</tbody></table>";
        echo "</td>";
        echo "</tr>";
        echo "</tbody></table>";


        break;
    }
    if (debug) {
      echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b> fkt:darstellungsart_ENDE  <br>";
    }
  }

  /******************************************************************************\
    Funktion:  listen_navi ()
  SELECT * FROM `nv_nachrichten` WHERE `00_lfd` IN

  (SELECT msg FROM `nv_masterkategolink` WHERE `katego` = (

  SELECT lfd FROM `nv_masterkatego` WHERE `kategorie` = "2m"));

  \******************************************************************************/
  public  function  listen_navi()
  {
    include("../4fcfg/config.inc.php");
    echo "<input type=\"image\" name=\"flt_start\" src=\"" . $conf_design_path . "/go_start.gif\" alt=\"Anfang\">\n";
    echo "<input type=\"image\" name=\"flt_back\" src=\"" . $conf_design_path . "/go_back.gif\" alt=\"zurueck\">\n";
    echo "<input type=\"image\" name=\"flt_for\" src=\"" . $conf_design_path . "/go_forward.gif\" alt=\"vor\">\n";
    echo "<input type=\"image\" name=\"flt_end\" src=\"" . $conf_design_path . "/go_end.gif\" alt=\"Ende\">\n";
  }


  /******************************************************************************\
    Funktion:  kategoliste
  SELECT * FROM `nv_nachrichten` WHERE `00_lfd` IN

  (SELECT msg FROM `nv_masterkategolink` WHERE `katego` = (

  SELECT lfd FROM `nv_masterkatego` WHERE `kategorie` = "2m"));

  \******************************************************************************/

  public $db_server;
  public $db_benutzer;
  public $db_passwort;
  public $db_name;
  public $db_tablname;
  public $db_tablnamelk;

  public $db_master_katego;

  public $sqlquery;
  public $db_hndl;
  public $masterresult;
  public $fktresult;
  public $userresult;
  public $resultcount;
  public $result;

  public $redcopy2;
  public $dbtyp;

  public $grundkatego;

  public  function set_katego_para($table)
  {

    include("../4fcfg/config.inc.php");
    include("../4fcfg/dbcfg.inc.php");
    include("../4fcfg/e_cfg.inc.php");
    include("../4fcfg/fkt_rolle.inc.php");
    $this->redcopy2  = $redcopy2;

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
  }

  /***********************************************************************************

   ***********************************************************************************/
  public  function kategoliste()
  {

    if ($_SESSION["filter_darstellung"] == "1") {
      include("../4fcfg/config.inc.php");

      $this->set_katego_para("master");   // MASTER
      /*      $this->sqlquery = "SELECT * FROM `".$this->db_tablname."` WHERE 1  ORDER BY `kategorie`;";

      if ( debug ){ echo "<b>!File:". __FILE__ ."  Line:". __LINE__ ."</b> <big><big><b> this->sqlquery = "; var_dump($this->sqlquery); echo "</b></big></big>";}

      $query_result = mysqli_query ($this->db_hndl, $this->sqlquery) or
         die("[query_table_MASTER] <br>$this->sqlquery<br>103-".mysqli_error($this->db_hndl)." ".mysqli_errno($this->db_hndl));

      $this->resultcount = mysqli_num_rows($query_result);
      
      $this->masterresult = NULL;
      
      for ($i=1; $i<=$this->resultcount; $i++){
        $this->masterresult[$i] = mysqli_fetch_assoc($query_result);
      }
      mysqli_free_result($query_result);
*/

      $this->sqlquery = "SELECT lfd, kategorie, beschreibung FROM `{$this->db_tablname}` ORDER BY `kategorie`"; // nur relevante Spalten

      $query_result = mysqli_query($this->db_hndl, $this->sqlquery);
      if (!$query_result) {
        // Fehlerprotokollierung, ggf. Exception werfen
        error_log("[query_table_MASTER] {$this->sqlquery}: " . mysqli_error($this->db_hndl));
        return false;
      }

      $this->masterresult = [];
      while ($row = mysqli_fetch_assoc($query_result)) {
        $this->masterresult[] = $row;
      }
      $this->resultcount = count($this->masterresult);

      if (debug) {
        echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b> <big><big><b>";
        echo " this->sqlquery = ";
        var_dump($this->sqlquery);
        echo "</b></big></big>";
        echo " this->masterresult = ";
        var_dump($this->masterresult);
        echo "</b></big></big>";
      }


      mysqli_free_result($query_result);


      $this->set_katego_para("fkt");     // FUNKTION
      $this->sqlquery = "SELECT * FROM `" . $this->db_tablname . "` WHERE 1  ORDER BY `kategorie`;";

      $query_result = mysqli_query($this->db_hndl, $this->sqlquery) or
        die("[query_table_FUNKTION405] <br>$this->sqlquery<br>-" . mysqli_error($this->db_hndl) . " " . mysqli_errno($this->db_hndl));
      $this->resultcount = mysqli_num_rows($query_result);
      $this->fktresult = NULL;
      for ($i = 0; $i < $this->resultcount; $i++) {
        $this->fktresult[$i] = mysqli_fetch_assoc($query_result);
      }
      mysqli_free_result($query_result);


      $this->set_katego_para("user");     // USER
      $this->sqlquery = "SELECT * FROM `" . $this->db_tablname . "` WHERE 1  ORDER BY `kategorie`;";
      $query_result = mysqli_query($this->db_hndl, $this->sqlquery) or
        die("[query_table_USER] <br>$this->sqlquery<br>103-" . mysqli_error($this->db_hndl) . " " . mysqli_errno($this->db_hndl));
      $this->resultcount = mysqli_num_rows($query_result);
      $this->userresult = NULL;
      for ($i = 0; $i < $this->resultcount; $i++) {
        $this->userresult[$i] = mysqli_fetch_assoc($query_result);
      }
      mysqli_free_result($query_result);

      //      $mastercount = COUNT($this->masterresult) ;
      if (is_array($this->masterresult)) {
        $mastercount = count($this->masterresult);
      } else {
        $mastercount = 0;
      }

      //      $fktcount    = COUNT($this->fktresult) ;
      if (is_array($this->fktresult)) {
        $fktcount = count($this->fktresult);
      } else {
        $fktcount = 0;
      }

      //      $usercount   = COUNT($this->userresult) ;
      if (is_array($this->userresult)) {
        $usercount = count($this->userresult);
      } else {
        $usercount = 0;
      }

      if (isset($_SESSION["global_katego"])) {
        $kategoselected = $_SESSION["global_katego"];
      }


      if (debug) {
        echo "<br><b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b>  ### <br>";
        echo "this->masterresult = <br>";
        var_dump($this->masterresult);
        echo " <br><br>\n";
      }
      if (debug) {
        echo "<br><b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b>  ### <br>";
        echo "this->fktresult = <br>";
        var_dump($this->fktresult);
        echo "<br><br>\n";
      }
      if (debug) {
        echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b>  ### <br>";
        echo "this->userresult = ";
        var_dump($this->userresult);
        echo " <br>\n";
      }

      // MASTER KATEGORIE
      if ($mastercount != 0) {
        echo "<div style=\"height:20px;
                           background-color:#FFC8C8;
                           border-top-color:#FFC8C8;
                           border-left-color:#FFC8C8;
                           border-right-color:#FFC8C8;
                           border-bottom-color:#000000;
                           border-width:2px;
                           border-style:solid;
                           padding-top:10px;
                           padding-bottom:0px;\">\n";
        if (!isset($_SESSION["ma_katego"])) {
          $color = "red";
        } else {
          $color = "lightred";
        }

        echo "<a href=\"" . $conf_4f["MainURL"] . "?ma_ktgotyp=global&ma_ktgo=alle\">
              <img src=\"./kategobutton.php?icontext=ALLE&color=" . $color . "\"
                   alt=\"Alle\"
                   border=\"0\"
                   title=\"Alle\"></a>\n";

        for ($i = 0; $i < $mastercount; $i++) {
          if ($this->masterresult[$i]["kategorie"] == "") {
            echo "<a><img src=\"null.gif\" alt=\"leer\"></a>\n";
          } else {
            /*if ( ($_SESSION ["ma_katego"] == $this->masterresult[$i]["kategorie"]) AND
                 ($_SESSION ["ma_kategotyp"] == "global") ){$color = "red";}else{$color = "lightred";}*/
            if ((($_SESSION["ma_katego"] ?? null) == $this->masterresult[$i]["kategorie"]) &&
              (($_SESSION["ma_kategotyp"] ?? null) == "global")
            ) {
              $color = "red";
            } else {
              $color = "lightred";
            }

            echo "<a href=\"" . $conf_4f["MainURL"] . "?ma_ktgotyp=global&ma_ktgo=" . $this->masterresult[$i]["kategorie"] . "\">
              <img src=\"./kategobutton.php?icontext=" . $this->masterresult[$i]["kategorie"] . "&color=" . $color . "\"
                   alt=\"" . $this->masterresult[$i]["beschreibung"] . "\"
                   border=\"0\"
                   title=\"" . $this->masterresult[$i]["beschreibung"] . "\"></a>\n";
          }
        }
        echo "</div>";
      }

      // FUNKTIONS KATEGORIE
      if ($fktcount != 0) {
        echo "<div style=\"height:20px;
                           background-color:#C8C8FF;
                           border-top-color:#C8C8FF;
                           border-left-color:#C8C8FF;
                           border-right-color:#C8C8FF;
                           border-bottom-color:#000000;
                           border-width:2px;
                           border-style:solid;
                           padding-top:10px;
                           padding-bottom:1px;
                           margin-bottom:0px;\">\n";
        if (!isset($_SESSION["fk_katego"])) {
          $color = "blue";
        } else {
          $color = "lightblue";
        }

        echo "<a href=\"" . $conf_4f["MainURL"] . "?fk_ktgotyp=fkt&fk_ktgo=alle\">";
        echo "<img src=\"./kategobutton.php?icontext=ALLE&color=" . $color . "\"
                   alt=\"Alle\"
                   border=\"0\"
                   title=\"Alle\"></a>\n";
        for ($i = 0; $i < $fktcount; $i++) {
          if ($this->fktresult[$i]["kategorie"] == "") {
            echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
          } else {
            /*if ( ($_SESSION ["fk_katego"] == $this->fktresult[$i]["kategorie"]) AND
                 ($_SESSION ["fk_kategotyp"] == "fkt" ) ){
               $color = "blue";
            }else{
               $color = "lightblue";
            }*/
            if (
              isset($_SESSION["fk_katego"], $_SESSION["fk_kategotyp"]) &&
              $_SESSION["fk_katego"] == $this->fktresult[$i]["kategorie"] &&
              $_SESSION["fk_kategotyp"] == "fkt"
            ) {
              $color = "blue";
            } else {
              $color = "lightblue";
            }

            echo "<a href=\"" . $conf_4f["MainURL"] . "?fk_ktgotyp=fkt&fk_ktgo=" . $this->fktresult[$i]["kategorie"] . "\">
              <img src=\"./kategobutton.php?icontext=" . $this->fktresult[$i]["kategorie"] . "&color=" . $color . "\"
                   alt=\"" . $this->fktresult[$i]["beschreibung"] . "\"
                   border=\"0\"
                   title=\"" . $this->fktresult[$i]["beschreibung"] . "\"></a>";
          }
        }
        echo "</div>";   //echo "<p>";
      }

      // USER KATEGORIE
      if ($usercount != 0) {
        echo "<div style=\"height:20px; background-color:#C8FFC8; border-top-color:#C8FFC8; border-left-color:#C8FFC8; border-right-color:#C8FFC8; border-bottom-color:#000000; border-width:2px; border-style:solid; padding-top:10px; padding-bottom:1px; margin-bottom:10px;\">\n";
        if (!isset($_SESSION["us_katego"])) {
          $color = "green";
        } else {
          $color = "lightgreen";
        }

        echo "<a href=\"" . $conf_4f["MainURL"] . "?us_ktgotyp=user&us_ktgo=alle\">";
        echo "<img src=\"./kategobutton.php?icontext=ALLE&color=" . $color . "\"
                   alt=\"Alle\"
                   border=\"0\"
                   title=\"Alle\"></a>\n";
        for ($i = 0; $i < $usercount; $i++) {
          if ($this->userresult[$i]["kategorie"] == "") {
            echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
          } else {
            /* if (($_SESSION["us_katego"] == $this->userresult[$i]["kategorie"]) and
              ($_SESSION["us_kategotyp"] == "user")
            ) {*/
            $us_katego = $_SESSION["us_katego"] ?? null;
            if ($us_katego == $this->userresult[$i]["kategorie"]) {
              $color = "green";
            } else {
              $color = "lightgreen";
            }
            echo "<a href=\"" . $conf_4f["MainURL"] . "?us_ktgotyp=user&us_ktgo=" . $this->userresult[$i]["kategorie"] . "\">
              <img src=\"./kategobutton.php?icontext=" . $this->userresult[$i]["kategorie"] . "&color=" . $color . "\"
                   alt=\"" . $this->userresult[$i]["beschreibung"] . "\"
                   border=\"0\"
                   title=\"" . $this->userresult[$i]["beschreibung"] . "\"></a>";
          }
        }
        echo "</div>";   //echo "<p>";
      }
    }
  }



  public  function get_list($listenart)
  {

    $query_select_fk_arg = "";  // leere Zeichenkette als Startwert
    $query_from_fk_arg   = "";  // leere Zeichenkette als Startwert
    $query_select_us_arg = "";
    $query_from_us_arg   = "";

    if (debug) {
      echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b> fkt:get_list <br>";
    }

    include("../4fcfg/config.inc.php");
    include("../4fcfg/para.inc.php");
    include("../4fcfg/dbcfg.inc.php");
    include("../4fcfg/e_cfg.inc.php");

    switch ($listenart) {
      case "Stab_lesen":
      case "global":
        if (debug) {
          echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b> fkt:get_list - sw:listenart - Stab Lesen/global<br>";
        }
        $tblusername = $conf_4f_tbl["usrtblprefix"] . strtolower($_SESSION["vStab_funktion"]) .
          "_" . strtolower($_SESSION["vStab_kuerzel"]);

        $tblfktname  = $conf_4f_tbl["usrtblprefix"] . "_fkt_" . strtolower($_SESSION["vStab_funktion"]);

        $dbaccess = new db_access(
          $conf_4f_db["server"],
          $conf_4f_db["datenbank"],
          $conf_4f_tbl["benutzer"],
          $conf_4f_db["user"],
          $conf_4f_db["password"]
        );

        if (debug) {
          echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b> fkt:get_list - sw:listenart - Stab Lesen/global nach db_access <br>";
        }

        if (($_SESSION["vStab_funktion"] ?? "") !== "A/W" && ($_SESSION["vStab_kuerzel"] ?? "") !== "") {
          $dbaccess->ensure_user_table($tblusername, $tblfktname);
        }

        $query_select_arg = $conf_4f_tbl["nachrichten"] . ".`00_lfd`, " .
          $conf_4f_tbl["nachrichten"] . ".`09_vorrangstufe`, " .
          $conf_4f_tbl["nachrichten"] . ".`04_richtung`, " .
          $conf_4f_tbl["nachrichten"] . ".`04_nummer`, " .
          $conf_4f_tbl["nachrichten"] . ".`10_anschrift`, " .
          $conf_4f_tbl["nachrichten"] . ".`12_abfzeit`, " .
          $conf_4f_tbl["nachrichten"] . ".`12_inhalt`, " .
          $conf_4f_tbl["nachrichten"] . ".`13_abseinheit`, " .
          $conf_4f_tbl["nachrichten"] . ".`14_funktion`, " .
          $conf_4f_tbl["nachrichten"] . ".`16_empf`, " .
          $conf_4f_tbl["nachrichten"] . ".`03_datum`, " .
          $conf_4f_tbl["nachrichten"] . ".`03_zeichen`, " .
          $conf_4f_tbl["nachrichten"] . ".`15_quitdatum`, " .
          $conf_4f_tbl["nachrichten"] . ".`15_quitzeichen`, " .
          $conf_4f_tbl["nachrichten"] . ".`X00_status`, " .
          $conf_4f_tbl["nachrichten"] . ".`x01_abschluss` ";

        $query_from_arg   = $conf_4f_tbl["nachrichten"];
        // Kategorien Master und User die im Query erscheinen müssen

        if (debug) {
          echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b> fkt:get_list - sw:listenart <br>";
        }

        if (debug) {
          echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "<br><br>\n";
          echo "GET=";
          var_dump($_GET);
          echo "<br><br>\n";
          echo "POST=";
          var_dump($_POST);
          echo "<br><br>\n";
          echo "COOKIE=";
          var_dump($_COOKIE);
          echo "<br><br>\n";
          echo "SESSION=";
          var_dump($_SESSION);
          echo "<br>\n";
        }

        if (($_SESSION["filter_darstellung"] == "1") and !(isset($_SESSION["flt_search"]))) {

          if (debug) {
            echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b> fkt:get_list - sw:listenart - Stab Lesen/global filterdarstellung UND !flt_search <br>";
          }

          if (isset($_SESSION["ma_katego"])) {

            if (debug) {
              echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b> fkt:get_list - sw:listenart <br>";
            }
            if (!isset($query_select_ma_arg)) {$query_select_ma_arg = "";}
            $query_select_ma_arg .= "," . $conf_4f_tbl["masterkatego"] . ".`kategorie`";
            if (!isset($query_from_ma_arg)) {$query_from_ma_arg = "";}
            $query_from_ma_arg .= "," . $conf_4f_tbl["masterkatego"] . "," . $conf_4f_tbl["masterkategolk"];
          }
          if (isset($_SESSION["fk_katego"])) {

            if (debug) {
              echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b> fkt:get_list - sw:listenart <br>";
            }

            $query_select_fk_arg .= "," . $tblfktname . "_katego.`kategorie`";
            $query_from_fk_arg .= "," . $tblfktname . "_katego, " . $tblfktname . "_kategolink ";
          }
          if (isset($_SESSION["us_katego"])) {

            if (debug) {
              echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b> fkt:get_list - sw:listenart <br>";
            }

            $query_select_us_arg .= "," . $tblusername . "_katego.`kategorie`";
            $query_from_us_arg .= "," . $tblusername . "_katego, " . $tblusername . "_kategolink ";
          }
        }

        if (debug) {
          echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b> fkt:get_list - sw:listenart <br>";
        }

        // Für wenn sind die Meldungen bestimmt
        $query_where_arg1 = "(( `" . $conf_4f_tbl["nachrichten"] . "`.`16_empf` like \"%" . $_SESSION["vStab_funktion"] . "%\" ) OR
                          ( `" . $conf_4f_tbl["nachrichten"] . "`.`16_empf` like \"%alle%\" ))";

        if (debug) {
          echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b> fkt:get_list - sw:listenart - Stab Lesen/global VOR filter darstellung <br>";
        }

        if ($_SESSION["filter_darstellung"] == "1") {

          if (debug) {
            echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b> fkt:get_list - sw:listenart - Stab Lesen/global filter_darstellung == 1 <br>";
          }


          if ((isset($_SESSION["filter_gelesen"]) ? $_SESSION["filter_gelesen"] : null) && ($_SESSION["filter_gelesen"]  == 1)) {

            $query_where_arg2 = " AND (`" . $conf_4f_tbl["nachrichten"] . "`.`00_lfd` " . $readwhat . " IN
                              ( select `" . $tblusername . "_read`.`nachnum` from `" . $tblusername . "_read` where 1))";
          } else {
            $query_where_arg2 = "";
          }
          if (($_SESSION["filter_erledigt"] == 1) or ($_SESSION["filter_unerledigt"] == 1)) {

            if (debug) {
              echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b> fkt:get_list - sw:listenart - Stab Lesen/global nach filter erledigt / filter unerledigt  <br>";
            }

            $query_where_arg3 = " AND ((";
            $donewhat = ''; // eingefügt wegen Warning
            if ($_SESSION["filter_erledigt"] == 1) {
              $query_where_arg3 .= "(`" . $conf_4f_tbl["nachrichten"] . "`.`00_lfd` " . $donewhat . " IN
                    ( select `" . $tblfktname . "_erl`.`nachnum` from `" . $tblfktname . "_erl` where 1))";
            }
            if (($_SESSION["filter_erledigt"] == 1) and
              ($_SESSION["filter_unerledigt"] == 1)
            ) {
              $query_where_arg3 .= ") OR (";
            }
            if ($_SESSION["filter_unerledigt"] == 1) {
              $query_where_arg3 .= "(`" . $conf_4f_tbl["nachrichten"] . "`.`00_lfd` " . $donewhat . " NOT IN
                    ( select `" . $tblfktname . "_erl`.`nachnum` from `" . $tblfktname . "_erl` where 1))";
            }
            $query_where_arg3 .= " ))";
          } else {
            $query_where_arg3 = " ";
          }
          $query_where_arg4 = "";
          if (isset($_SESSION["ma_katego"])) {
            $query_where_arg4 = " AND (" . $conf_4f_tbl["nachrichten"] . ".`00_lfd` = `" . $conf_4f_tbl["masterkategolk"] . "`.`msg`)" .
              " AND (" . $conf_4f_tbl["masterkatego"] . ".`lfd` = `" . $conf_4f_tbl["masterkategolk"] . "`.`katego`)" .
              " AND (" . $conf_4f_tbl["masterkatego"] . ".`kategorie` = \"" . $_SESSION["ma_katego"] . "\")";
          }
          $query_where_arg5 = "";
          if (isset($_SESSION["fk_katego"])) {
            $query_where_arg5 = " AND (`" . $conf_4f_tbl["nachrichten"] . "`.`00_lfd` = `" . $tblfktname . "_kategolink`.`msg`)" .
              " AND (`" . $tblfktname . "_katego`.`lfd` = `" . $tblfktname . "_kategolink`.`katego`)" .
              " AND  (`" . $tblfktname . "_katego`.`kategorie` = \"" . $_SESSION["fk_katego"] . "\")";
          }
          $query_where_arg6 = "";
          if (isset($_SESSION["us_katego"])) {
            $query_where_arg6 = " AND (`" . $conf_4f_tbl["nachrichten"] . "`.`00_lfd` = `" . $tblusername . "_kategolink`.`msg`)" .
              " AND (`" . $tblusername . "_katego`.`lfd` = `" . $tblusername . "_kategolink`.`katego`)" .
              " AND  (`" . $tblusername . "_katego`.`kategorie` = \"" . $_SESSION["us_katego"] . "\")";
          }
        } else {
          $query_where_arg2 = "";
          $query_where_arg3 = "";
          $query_where_arg4 = "";
          $query_where_arg5 = "";
          $query_where_arg6 = "";
        }
        $query_orderby_arg = "`04_nummer` DESC, `09_vorrangstufe` DESC ";
        if (isset($_SESSION["flt_search"])) {
          $query_search = "(" .
            "(" . $conf_4f_tbl["nachrichten"] . ".`04_nummer` LIKE \"%" . $_SESSION["flt_search"] . "%\") OR " .
            "(" . $conf_4f_tbl["nachrichten"] . ".`10_anschrift` LIKE \"%" . $_SESSION["flt_search"] . "%\") OR " .
            "(" . $conf_4f_tbl["nachrichten"] . ".`12_abfzeit` LIKE \"%" . $_SESSION["flt_search"] . "%\") OR " .
            "(" . $conf_4f_tbl["nachrichten"] . ".`12_inhalt` LIKE \"%" . htmlentities($_SESSION["flt_search"]) . "%\") OR " .
            "(" . $conf_4f_tbl["nachrichten"] . ".`13_abseinheit` LIKE \"%" . $_SESSION["flt_search"] . "%\") )";
          $query_from_ma_arg = $query_from_ma_arg ?? '';
          $query_from_fk_arg = $query_from_fk_arg ?? '';
          $query_from_us_arg = $query_from_us_arg ?? '';
          $query_select_arg    = $query_select_arg    ?? '';
          $query_select_ma_arg = $query_select_ma_arg ?? '';
          $query_select_fk_arg = $query_select_fk_arg ?? '';
          $query_select_us_arg = $query_select_us_arg ?? '';
          $querycount = "SELECT COUNT(*) FROM " . $query_from_arg . $query_from_ma_arg . $query_from_fk_arg . $query_from_us_arg . " WHERE " .
            $query_where_arg1 . " AND " . $query_search . ";";

          $query = "SELECT " . $query_select_arg . $query_select_ma_arg . $query_select_fk_arg . $query_select_us_arg . " FROM " . $query_from_arg . $query_from_ma_arg . $query_from_fk_arg . $query_from_us_arg . " WHERE " .
            $query_where_arg1 . " AND " . $query_search . " ORDER BY " . $query_orderby_arg;
        } else {
          $query_search = "";
          $query_from_arg    = $query_from_arg    ?? '';
          $query_from_ma_arg = $query_from_ma_arg ?? '';
          $query_from_fk_arg = $query_from_fk_arg ?? '';
          $query_from_us_arg = $query_from_us_arg ?? '';

          $query_select_arg    = $query_select_arg    ?? '';
          $query_select_ma_arg = $query_select_ma_arg ?? '';
          $query_select_fk_arg = $query_select_fk_arg ?? '';
          $query_select_us_arg = $query_select_us_arg ?? '';

          $querycount = "SELECT COUNT(*) FROM " . $query_from_arg . $query_from_ma_arg . $query_from_fk_arg . $query_from_us_arg . " WHERE " .
            $query_where_arg1 . " " . $query_where_arg2 . " " . $query_where_arg3 . " " . $query_where_arg4 . $query_where_arg6 . ";";

          $query = "SELECT " . $query_select_arg . $query_select_ma_arg . $query_select_fk_arg . $query_select_us_arg . " FROM " . $query_from_arg . $query_from_ma_arg . $query_from_fk_arg . $query_from_us_arg . " WHERE " .
            $query_where_arg1 . " " . $query_where_arg2 . " " . $query_where_arg3 . " " . $query_where_arg4 . $query_where_arg5 . $query_where_arg6 . " ORDER BY " . $query_orderby_arg;
        }

        /*******************************************************************************************************************************************************************************************/

        if (debug) {
          echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "fkt:get_list  QUERYCOUNT = " . $querycount . "<br>tblfktname = " . $tblfktname . "</b><br><br>";
        }
        if (debug) {
          echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "fkt:get_list  QUERY   =   " . $query . "</b><br><br>";
        }


        if ($_SESSION["filter_darstellung"] == "1") {
          $tmp = $dbaccess->query_table_wert($querycount);
          $anzahl = $tmp[0];

          $_SESSION["filter_rescount"] = $anzahl;
          if (debug) {
            echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "fkt:get_list  Anzahl =" . $anzahl . "</b><br>";
          }

          if (isset($_SESSION["flt_navi"])) {
            switch ($_SESSION["flt_navi"]) {
              // ANFANG
              case "start":
                if (debug) {
                  echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "fkt:get_list - switch (_SESSION[flt_navi]):start </b><br>";
                }
                $_SESSION["filter_start"] = 0;
                break;
              // Eine Seite zurück
              case "back":
                if (debug) {
                  echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "fkt:get_list - switch (_SESSION[flt_navi]):back </b><br>";
                }
                $_SESSION["filter_start"] -= $_SESSION["filter_anzahl"];
                if ($_SESSION["filter_start"] < 0) {
                  $_SESSION["filter_start"] = 0;
                }
                break;
              // Eine Seite vor
              case "for":
                if (debug) {
                  echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "fkt:get_list - switch (_SESSION[flt_navi]):for </b><br>";
                }
                if ($anzahl < $_SESSION["filter_anzahl"]) {
                  $_SESSION["filter_start"] = 0;
                } else {
                  $_SESSION["filter_start"] += $_SESSION["filter_anzahl"];
                  if ($_SESSION["filter_start"] >= $anzahl) {
                    $_SESSION["filter_start"] = $anzahl - 1;
                  }
                }
                break;
              // Letzte Seite
              case "end":
                if (debug) {
                  echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "fkt:get_list - switch (_SESSION[flt_navi]):end </b><br>";
                }
                if ($anzahl < $_SESSION["filter_anzahl"]) {
                  $_SESSION["filter_start"] = 0;
                } else {
                  $seiten = floor($anzahl / $_SESSION["filter_anzahl"]);
                  $_SESSION["filter_start"] = $seiten * $_SESSION["filter_anzahl"];
                }
                break;
            }
            unset($_SESSION["flt_navi"]);
          }
          $query .= " LIMIT " . $_SESSION["filter_start"] . "," . $_SESSION["filter_anzahl"];
        }
        // Zunächst holen wir alls Meldungen mit den entsprechenden Kriterien
        $query_select = $query_select ?? '';
        $query = $query_select . $query;

        if (debug) {
          echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "fkt:get_list  QUERY  =" . $query . "</b><br><br>";
        }

        break;
      /************************************************************************************************************/
      case "FMADMIN":
      case "SIADMIN":
        if (debug) {
          echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "fkt:get_list  case FMADMIN, SIADMIN</b><br>";
        }
        include("../4fcfg/fkt_rolle.inc.php");
        $dbaccess = new db_access(
          $conf_4f_db["server"],
          $conf_4f_db["datenbank"],
          $conf_4f_tbl["benutzer"],
          $conf_4f_db["user"],
          $conf_4f_db["password"]
        );

        if (isset($_SESSION["flt_search"])) {
          $query_search = "(" .
            "(" . $conf_4f_tbl["nachrichten"] . ".`04_nummer` LIKE \"%" . $_SESSION["flt_search"] . "%\") OR " .
            "(" . $conf_4f_tbl["nachrichten"] . ".`10_anschrift` LIKE \"%" . $_SESSION["flt_search"] . "%\") OR " .
            "(" . $conf_4f_tbl["nachrichten"] . ".`12_abfzeit` LIKE \"%" . $_SESSION["flt_search"] . "%\") OR " .
            "(" . $conf_4f_tbl["nachrichten"] . ".`12_inhalt` LIKE \"%" . htmlentities($_SESSION["flt_search"]) . "%\") OR " .
            "(" . $conf_4f_tbl["nachrichten"] . ".`13_abseinheit` LIKE \"%" . $_SESSION["flt_search"] . "%\") )";
        } else {
          $query_search = " 1 ";
        }

        $query = "SELECT `00_lfd`,
                         `04_richtung`,
                         `04_nummer`,
                         `09_vorrangstufe`,
                         `10_anschrift`,
                         `12_abfzeit`,
                         `13_abseinheit`,
                         `12_inhalt`,
                         `16_empf`
                   FROM `" . $conf_4f_tbl["nachrichten"] . "`
				   WHERE" . $query_search . " order by 04_nummer DESC, 09_vorrangstufe DESC ";          //

        if (debug) {
          echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "fkt:get_list  case SIADMIN, FMADMIN query =" . $query . "</b><br>";
        }

        if (($_SESSION["filter_darstellung"] ?? null) == "1") {
          $querycount = "SELECT COUNT(*) FROM `" . $conf_4f_tbl["nachrichten"] . "` WHERE " . $query_search;
          $tmp = $dbaccess->query_table_wert($querycount);
          if (debug) {
            echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "fkt:get_list  case SIADMIN, FMADMIN tmp =";
            print_r($querycount);
            echo "</b><br>";
          }
          $anzahl = (is_array($tmp) ? (int) ($tmp[0] ?? 0) : 0);

          $_SESSION["filter_rescount"] = $anzahl;
          if (debug) {
            echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "fkt:get_list  Anzahl =" . $anzahl . "</b><br>";
          }

          if (isset($_SESSION["flt_navi"])) {
            switch ($_SESSION["flt_navi"]) {
              // ANFANG
              case "start":
                if (debug) {
                  echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "fkt:get_list - switch (_SESSION[flt_navi]):start </b><br>";
                }
                $_SESSION["filter_start"] = 0;
                break;
              // Eine Seite zurück
              case "back":
                if (debug) {
                  echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "fkt:get_list - switch (_SESSION[flt_navi]):back </b><br>";
                }
                $_SESSION["filter_start"] -= $_SESSION["filter_anzahl"];
                if ($_SESSION["filter_start"] < 0) {
                  $_SESSION["filter_start"] = 0;
                }
                break;
              // Eine Seite vor
              case "for":
                if (debug) {
                  echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "fkt:get_list - switch (_SESSION[flt_navi]):for </b><br>";
                }
                if ($anzahl < $_SESSION["filter_anzahl"]) {
                  $_SESSION["filter_start"] = 0;
                } else {
                  $_SESSION["filter_start"] += $_SESSION["filter_anzahl"];
                  if ($_SESSION["filter_start"] >= $anzahl) {
                    $_SESSION["filter_start"] = $anzahl - 1;
                  }
                }
                break;
              // Letzte Seite
              case "end":
                if (debug) {
                  echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "fkt:get_list - switch (_SESSION[flt_navi]):end </b><br>";
                }
                if ($anzahl < $_SESSION["filter_anzahl"]) {
                  $_SESSION["filter_start"] = 0;
                } else {
                  $seiten = floor($anzahl / $_SESSION["filter_anzahl"]);
                  $_SESSION["filter_start"] = $seiten * $_SESSION["filter_anzahl"];
                }
                break;
            }
            unset($_SESSION["flt_navi"]);
          }
          $query .= " LIMIT " . $_SESSION["filter_start"] . "," . $_SESSION["filter_anzahl"];
        }
        $query .= ";";

        break;
    }

    if (debug) {
      echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "fkt:get_list QUERY [get_list] =   " . $query . "</b><br>";
    }

    $result = $dbaccess->query_table($query);

    if (debug) {
      echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "fkt:get_list_ENDE Result = ";
      var_dump($result);
      echo "</b><br>";
    }

    return ($result);
  }

  /******************************************************************************\
    Gleiche Warteschlange wie die Sichterliste (Stab_sichten):
    noch nicht quittiert, und je nach si_in_out nur Eingänge
    oder Eingänge plus vom A/W bereits bearbeitete Ausgänge.
  \******************************************************************************/
  private function message_in_sichter_queue(array $row, bool $si_in_out): bool
  {
    $quitdatum = trim((string)($row["15_quitdatum"] ?? ""));
    $not_quittiert = ($quitdatum === "" || $quitdatum === "0" || str_starts_with($quitdatum, "0000-00-00"));
    if (!$not_quittiert) {
      return false;
    }
    if (($row["04_richtung"] ?? "") === "E") {
      return true;
    }
    if (!$si_in_out) {
      return false;
    }
    $fm_datum = trim((string)($row["03_datum"] ?? ""));
    $fm_zeichen = trim((string)($row["03_zeichen"] ?? ""));
    $fm_done = ($fm_datum !== "" && $fm_datum !== "0" && !str_starts_with($fm_datum, "0000-00-00"))
      && ($fm_zeichen !== "");
    return $fm_done;
  }

  /******************************************************************************\

Funktion:	 createlist

\******************************************************************************/
  public  function createlist()
  {
    if (debug) {
      echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "   fkt:createlist</b><br>";
    }
    include("../4fcfg/config.inc.php");
    include("../4fcfg/para.inc.php");
    include("../4fcfg/dbcfg.inc.php");
    include("../4fcfg/e_cfg.inc.php");
    if (debug) {
      echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "   " . $this->listenart . "</b><br>";
    }
    switch ($this->listenart) {
      case "FMA":
        /***** F M A ****/
        if (debug) {
          echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b> ### - fkt:createlist - switch(listenart) -- case (FMA)<br>";
        }
        $dbaccess = new db_access(
          $conf_4f_db["server"],
          $conf_4f_db["datenbank"],
          $conf_4f_tbl["benutzer"],
          $conf_4f_db["user"],
          $conf_4f_db["password"]
        );
        $query = "SELECT `00_lfd`,`07_durchspruch`, `08_befhinweis`, `08_befhinwausw`,`09_vorrangstufe`, `10_anschrift`, `12_abfzeit`, `12_inhalt` FROM `" . $conf_4f_tbl["nachrichten"] . "`
                  WHERE ((`04_richtung` = \"A\") AND (`03_datum` = 0) AND (`03_zeichen` = \"\")) order by `09_vorrangstufe` DESC, `12_abfzeit` ; ";
        $result = $dbaccess->query_table($query);
        if ($result != "") {
          echo "<table style=\"text-align: center; background-color: rgb(255, 255, 255); \" border=\"1\" cellpadding=\"10\" cellspacing=\"1\">\n<tbody>\n";
          echo "<tr style=\"background-color: rgb(0,0,0); color:#FFFFFF; font-weight:bold;\">\n";
          echo "<td>ZEIT</td>\n";
          echo "<td>Vorst</td>\n";
          echo "<td>Anschr</td>\n";
          echo "<td>Inhalt</td>\n";
          echo "</tr>";

          if (debug) {
            echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><br>";
            echo "<br><br>\n------------------------Info Block Start------------------------------<br>\n";
            echo "query  =";
            var_dump($query);
            echo "<br>\n";
            echo "result = ";
            var_dump($result);
            echo "<br>\n";
            echo "<br>\n------------------------Info Block Ende------------------------------<br>\n";
          }

          foreach ($result as $row) {
            if (($row["09_vorrangstufe"] != "") and ($row["09_vorrangstufe"] != "eee")) {
              echo "<tr style=\"background-color: rgb(255,255,100); color:#FFFFFF; font-weight:bold;\">\n";
            }
            $abfzeit = convdatetimeto($row["12_abfzeit"]);
            echo "<td>";
            if (($row["12_abfzeit"] != "")) {
              echo "<a href=\"mainindex.php?fm=meldung&00_lfd=" . $row["00_lfd"] . "\" target=\"_self\">" . $abfzeit["stak"] . "</a>\n";
            } else {
              echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
            }
            echo "</td>\n";
            echo "<td>";
            if (($row["09_vorrangstufe"] != "")) {
              echo "<a href=\"mainindex.php?fm=meldung&00_lfd=" . $row["00_lfd"] . "\" target=\"_self\">" . $row["09_vorrangstufe"] . "</a>\n";
            } else {
              echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
            }
            echo "</td>\n";
            echo "<td>";
            if (($row["10_anschrift"] != "")) {
              echo "<a href=\"mainindex.php?fm=meldung&00_lfd=" . $row["00_lfd"] . "\" target=\"_self\">" . $row["10_anschrift"] . "</a>\n";
            } else {
              echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
            }
            echo "</td>\n";
            echo "<td align=\"left\">";
            if (($row["12_inhalt"] != "")) {
              echo "<a href=\"mainindex.php?fm=meldung&00_lfd=" . $row["00_lfd"] . "\" target=\"_self\">" . $row["12_inhalt"] . "</a>\n";
            } else {
              echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
            }
            echo "</td>\n";
            echo "</tr>";
          }
          echo "</tbody></table>";
        } else { // if $result != ""
          echo "<big><big><big>Zur Zeit keine Meldung im Ausgang</big></big></big><br>";
        }
        break;

      case "Stab_lesen":  // ******  S T A B    l e s e n *****
        if (debug) {
          echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . " fkt:createlist - switch(listenart) -- case" . $this->listenart . "</b><br>";
        }
        /*
          Hole die Liste der gelesenen und der erledigten Nachrichten
        */
        $result_get_list = $this->get_list("global");

        if (debug) {
          echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><br>";
          echo "<br><br>\n------------------------Info Block Start------------------------------<br>\n";
          echo "result = ";
          var_dump($result_get_list);
          echo "<br>\n";
          echo "<br>\n------------------------Info Block Ende------------------------------<br>\n";
        }

        $this->darstellungs_art($this->listenart);
        $dbschongelesen = list_of_readed_msg();
        $dbschonerledigt = list_of_done_msg();
        $this->kategoliste();
        $this->listen_navi();  //Navigationsbutton

        if (debug) {
          echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><br>";
          echo "<br><br>\n------------------------Info Block Start------------------------------<br>\n";
          echo "result = ";
          var_dump($result_get_list);
          echo "<br>\n";
          echo "<br>\n------------------------Info Block Ende------------------------------<br>\n";
        }


        if ($result_get_list != "") {

          if (debug) {
            echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><br>";
            echo "<br><br>\n------------------------Info Block Start------------------------------<br>\n";
            echo "result_get_list = ";
            var_dump($result_get_list);
            echo "<br>\n";
            echo "<br>\n------------------------Info Block Ende------------------------------<br>\n";
          }

          echo "<table style=\"text-align: center; background-color: rgb(255,255,255); \" border=\"2\" cellpadding=\"2\" cellspacing=\"2\">\n<tbody>\n";
          echo "<tr style=\"background-color: rgb(240,240,200); color:#000000; font-weight:bold;\">\n";
          // gelesen ?
          echo "<th align=\"center\">";
          echo "<p><img src=\"" . $conf_design_path . "/info.gif\" alt=\"Meine Bearbeitung\" title=\"Meine Bearbeitung\"></p>";
          echo "</th>\n";
          // erledigt
          echo "<th align=\"center\">";
          echo "<p><img src=\"" . $conf_design_path . "/checked.gif\" alt=\"Bearbeitung in meinem Sachgebiet\" title=\"Bearbeitung in meinem Sachgebiet\"></p>";
          echo "</td>\n";
          // Transport
          echo "<th align=\"center\">";
          echo "<p><img src=\"" . $conf_design_path . "/transport.gif\" alt=\"Transportstatus\" title=\"Transportstatus\"></p>";
          echo "</td>\n";
          echo "<th>Vorrang</th>\n";
          echo "<th>E/A</th>\n";
          echo "<th>Num</th>\n";
          echo "<th>Von</th>";
          echo "<th>An</th>";
          echo "<th>Abfasszeit</th>\n";
          echo "<th>Inhalt</th>\n";
          echo "</tr>";
          // zeilenweise Anzeige der Datenbankanfrage
          $result = $result ?? []; // für php 8.x
          foreach ($result_get_list as $row) { // $result 
            $hilf = $this->explodereceiver($row["16_empf"]);
            $receivercolor = $hilf[$_SESSION["vStab_funktion"]]; // Empfaenger dieser Zeile
            switch ($receivercolor) {
              case "rt":
                $receiverbackground = $cfg["lbg"]["rt"];
                break;
              case "gn":
                $receiverbackground = $cfg["lbg"]["gn"];
                break;
              case "bl":
                $receiverbackground = $cfg["lbg"]["bl"];
                break;
              case "ge":
                $receiverbackground = $cfg["lbg"]["ge"];
                break;
              default:
                $receiverbackground = $cfg["lbg"]["dflt"];
            }
            echo "<tr style=\"background-color: " . $receiverbackground . "; color:#FFFFFF; font-weight:bold;\">\n";
            // Liegt eine Vorrangstufe vor!!!
            $vorrang = (($row["09_vorrangstufe"] != "") and ($row["09_vorrangstufe"] != "eee"));
            // 1. Spalte schon gelesen?
            $schongelesen = false;
            if ($dbschongelesen != "") {
              if (in_array($row["00_lfd"], $dbschongelesen)) {
                $schongelesen = true;
              }
            }

            if ($vorrang) {
              if ($schongelesen) {
                echo "<td align=\"center\">";
                echo "<a href=\"mainindex.php?action=gelesen&00_lfd=" . $row["00_lfd"] . "&todo=unset\" target=\"_self\">\n";
                echo "<img src=\"" . $conf_design_path . "/000.gif\" alt=\"lesen\" border=\"0\"></a>";
                echo "</td>\n";
              } else {
                echo "<td align=\"center\">";
                echo "<a href=\"mainindex.php?action=gelesen&00_lfd=" . $row["00_lfd"] . "&todo=set\" target=\"_self\">\n";
                echo "<img src=\"" . $conf_design_path . "/mail_prio_unread.gif\" alt=\"von mir ungelesen + VORRANGSTUFE\" title=\"von mir ungelesen + VORRANGSTUFE\" border=\"0\"></a>";
                echo "</td>\n";
              }
            } else {
              if ($schongelesen) {  // ==> wurde schon gelesen
                echo "<td align=\"center\">";
                echo "<a href=\"mainindex.php?action=gelesen&00_lfd=" . $row["00_lfd"] . "&todo=unset\" target=\"_self\">\n";
                echo "<img src=\"" . $conf_design_path . "/000.gif\" alt=\"lesen\" border=\"0\"></a>";
                echo "</td>\n";
              } else {
                echo "<td align=\"center\">";
                echo "<a href=\"mainindex.php?action=gelesen&00_lfd=" . $row["00_lfd"] . "&todo=set\" target=\"_self\">\n";
                echo "<img src=\"" . $conf_design_path . "/mail_unread.gif\" alt=\"von mir ungelesen\" title=\"von mir ungelesen\" border=\"0\"></a>";
                echo "</td>\n";
              }
            }

            $schonerledigt = false;

            if ($dbschonerledigt != "") {
              if (in_array($row["00_lfd"], $dbschonerledigt)) {
                $schonerledigt = true;
              }
            }


            if ($schonerledigt) {  // ==> wurde schon erledigt
              echo "<td style=\"text-align: center; vertical-align: middle;\">";
              echo "<a href=\"mainindex.php?action=erledigt&00_lfd=" . $row["00_lfd"] . "&todo=unset\" target=\"_self\">\n";
              echo "<img src=\"" . $conf_design_path . "/task_done.gif\" alt=\"In meinem Sachgebiet erledigt\" title=\"In meinem Sachgebiet erledigt\" border=\"0\"></a>";
              echo "</td>\n";
            } else {
              echo "<td style=\"text-align: center; vertical-align: middle;\">";
              echo "<a href=\"mainindex.php?action=erledigt&00_lfd=" . $row["00_lfd"] . "&todo=set\" target=\"_self\">\n";
              echo "<p><img src=\"" . $conf_design_path . "/task_due.gif\" alt=\"Vom Sachgebiet unerledigt\" title=\"Vom Sachgebiet unerledigt\" border=\"0\"></p>";
              echo "</td>\n";
            }

            // Transportstatus: rot nur, solange die Meldung in der Sichter-Warteschlange liegt
            echo "<td align=\"center\">";
            if ($this->message_in_sichter_queue($row, !empty($conf_4f["si_in_out"]))) {
              echo "<p><img src=\"" . $conf_design_path . "/status_red.gif\" alt=\"Vor dem Sichter\" title=\"Vor dem Sichter\"></p>";
            } elseif (($row["04_richtung"] == "A")) {
              switch ((int)$row["X00_status"]) {
                case 2: // liegt vor dem A/W
                  echo "<p><img src=\"" . $conf_design_path . "/status_yellow.gif\" alt=\"Vor dem A/W\" title=\"Vor dem A/W\"></p>";
                  break;
                case 8: // vollständig transportiert
                  echo "<p><img src=\"" . $conf_design_path . "/status_green.gif\" alt=\"Vollst&auml;ndig transportiert\" title=\"Vollst&auml;ndig transportiert\"></p>";
                  break;
                default:
                  echo "<p><img src=\"" . $conf_design_path . "/null.gif\" alt=\"fremd\"></p>";
                  break;
              }
            } else {
              echo "<p><img src=\"" . $conf_design_path . "/null.gif\" alt=\"fremd\"></p>";
            }
            echo "</td>\n";

            echo "<td>";
            // Vorrangstufe
            if (($row["09_vorrangstufe"] != "") and ($row["09_vorrangstufe"] != "eee")) {
              echo "<a href=\"mainindex.php?stab=meldung&00_lfd=" . $row["00_lfd"] . "\" target=\"_self\">" . $row["09_vorrangstufe"] . "</a>\n";
            } else {
              echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
            }
            echo "</td>\n";
            // Eingang / Ausgang
            echo "<td>";
            if (($row["04_richtung"] != "")) {
              echo "<a href=\"mainindex.php?stab=meldung&00_lfd=" . $row["00_lfd"] . "\" target=\"_self\">" . $row["04_richtung"] . "</a>\n";
            } else {
              echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
            }
            echo "</td>\n";
            // Nachweisnummer
            echo "<td>";
            if (($row["04_nummer"] != "")) {
              echo "<a href=\"mainindex.php?stab=meldung&00_lfd=" . $row["00_lfd"] . "\" target=\"_self\">" . $row["04_nummer"] . "</a>\n";
            } else {
              echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
            }
            echo "</td>\n";
            // Muss der Absender oder die Absendende Einheit unter von / an
            if ($row["04_richtung"] == "A") { // von = 14_funktion an=10_anschrift
              // Ausgang VON
              echo "<td>";
              if (($row["14_funktion"] != "")) {
                echo "<a href=\"mainindex.php?stab=meldung&00_lfd=" . $row["00_lfd"] . "\" target=\"_self\">" . $row["14_funktion"] . "</a>\n";
              } else {
                echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
              }
              echo "</td>\n";

              // Ausgang AN
              echo "<td>";
              if (($row["10_anschrift"] != "")) {
                echo "<a href=\"mainindex.php?stab=meldung&00_lfd=" . $row["00_lfd"] . "\" target=\"_self\">" . $row["10_anschrift"] . "</a>\n";
              } else {
                echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
              }
              echo "</td>\n";
            }

            if ($row["04_richtung"] == "E") {  // von = 13_abseinheit/14_funktion an=10_anschrift
              echo "<td>";
              if (($row["13_abseinheit"] != "")) {
                echo "<a href=\"mainindex.php?stab=meldung&00_lfd=" . $row["00_lfd"] . "\" target=\"_self\"><big>" . $row["13_abseinheit"] . "</big></a>\n";
              } else {
                echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
              }
              echo "</td>\n";


              echo "<td>";
              if (($row["10_anschrift"] != "")) {
                echo "<a href=\"mainindex.php?stab=meldung&00_lfd=" . $row["00_lfd"] . "\" target=\"_self\"><big>" . $row["10_anschrift"] . "</big></a>\n";
              } else {
                echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
              }
              echo "</td>\n";
            }
            echo "<td>";
            if (($row["12_abfzeit"] != "")) {
              $arr    = convdatetimeto($row["12_abfzeit"]);
              $abzeit = $arr["stak"];
              echo "<a href=\"mainindex.php?stab=meldung&00_lfd=" . $row["00_lfd"] . "\" target=\"_self\"><big>" . $abzeit . "</big></a>\n";
            } else {
              echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
            }
            echo "</td>\n";

            echo "<td align=\"left\">";
            if (($row["12_inhalt"] != "")) {
              echo "<a href=\"mainindex.php?stab=meldung&00_lfd=" . $row["00_lfd"] . "\" target=\"_self\"><big>" . $row["12_inhalt"] . "</big></a>\n";
            } else {
              echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
            }
            echo "</td>\n";
            echo "</tr>";
          }  // foreach $result
        }
        echo "</tbody></table>";
        $this->listen_navi();  //Navigationsbutton

        if (debug) {
          echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . " fkt:createlist - switch(listenart) STAB LESEN</b><br>";
        }

        break; // ******  S T A B    l e s e n *****



      case "Stab_sichten":
        /*********** S t a b   s i c h t e n ************/
        if (debug) {
          echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "   fkt:createlist - switch(listenart) -- case (Stab_sichten) </b><br>";
        }
        $dbaccess = new db_access(
          $conf_4f_db["server"],
          $conf_4f_db["datenbank"],
          $conf_4f_tbl["benutzer"],
          $conf_4f_db["user"],
          $conf_4f_db["password"]
        );

        $WHERE_inout = "WHERE ( ( `15_quitdatum` = 0 ) AND ( `15_quitzeichen` = 0 ) ) AND ( ( `04_richtung` =\"E\") OR ( `03_datum` != 0 ) AND ( `03_zeichen` != \"\" ) )";
        $WHERE_in    = "WHERE ( ( `15_quitdatum` = 0 ) AND ( `15_quitzeichen` = 0 ) ) AND ( `04_richtung` =\"E\")";

        //order by `09_vorrangstufe` DESC, `12_abfzeit`; 


        if ($conf_4f["si_in_out"]) {  //  Ein- und Ausänge sichten
          if (debug) {
            echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b> ### Ein- und Ausgänge sichten<br>";
          }
          $query = "SELECT `00_lfd`,`07_durchspruch`,
                         `08_befhinweis`,
                         `08_befhinwausw`,
                         `09_vorrangstufe`,
                         `10_anschrift`,
                         `12_abfzeit`,
                         `12_inhalt` FROM `" . $conf_4f_tbl["nachrichten"] . "` " . $WHERE_inout . "
                       order by `09_vorrangstufe` DESC, `12_abfzeit`; ";
        } else {
          if (debug) {
            echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b> ### nur Eingänge sichten<br>";
          }
          $query = "SELECT `00_lfd`,`07_durchspruch`,
                         `08_befhinweis`,
                         `08_befhinwausw`,
                         `09_vorrangstufe`,
                         `10_anschrift`,
                         `12_abfzeit`,
                         `12_inhalt` FROM `" . $conf_4f_tbl["nachrichten"] . "` " . $WHERE_in . "
                        order by `09_vorrangstufe` DESC, `12_abfzeit`; ";
        }
        if (debug) {
          echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b>Stab_sichten:" . $query . " </b><br>";
        }
        $result = $dbaccess->query_table($query);
        if ($result != "") {
          echo "<table style=\"text-align: center; background-color: rgb(255, 255, 255); \" border=\"2\" cellpadding=\"2\" cellspacing=\"2\">\n<tbody>\n";
          echo "<tr style=\"background-color: rgb(240,240,200); color:#000000; font-weight:bold;\">\n";
          echo "<td>ZEIT</td>\n";
          echo "<td>Vorst</td>\n";
          echo "<td>Anschrift</td>\n";
          echo "<td>Inhalt / Text</td>\n";
          echo "</tr>";
          foreach ($result as $row) {
            if (($row["09_vorrangstufe"] != "") and ($row["09_vorrangstufe"] != "eee")) {
              echo "<tr style=\"background-color: rgb(220,0,0); color:#FFFFFF; font-weight:bold;\">\n";
            }
            $abfzeit = convdatetimeto($row["12_abfzeit"]);
            echo "<td>";
            if (($row["12_abfzeit"] != "")) {
              echo "<a href=\"mainindex.php?sichter=meldung&00_lfd=" . $row["00_lfd"] . "\" target=\"_self\">" . $abfzeit["stak"] . "</a>\n";
            } else {
              echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
            }
            echo "</td>\n";
            echo "<td>";
            if (($row["09_vorrangstufe"] != "")) {
              echo "<a href=\"mainindex.php?sichter=meldung&00_lfd=" . $row["00_lfd"] . "\" target=\"_self\">" . $row["09_vorrangstufe"] . "</a>\n";
            } else {
              echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
            }
            echo "</td>\n";
            echo "<td>";
            if (($row["10_anschrift"] != "")) {
              echo "<a href=\"mainindex.php?sichter=meldung&00_lfd=" . $row["00_lfd"] . "\" target=\"_self\">" . $row["10_anschrift"] . "</a>\n";
            } else {
              echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
            }
            echo "</td>\n";
            echo "<td align=\"left\">";
            if (($row["12_inhalt"] != "")) {
              echo "<a href=\"mainindex.php?sichter=meldung&00_lfd=" . $row["00_lfd"] . "\" target=\"_self\">" . $row["12_inhalt"] . "</a>\n";
            } else {
              echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
            }
            echo "</td>\n";
            echo "</tr>";
          } // foreach result row
          echo "</tbody></table>";
        } else { // if isset $result
          echo "<big><big><big>Zur Zeit keine Meldung zu sichten</big></big></big>";
        }


        break; //*********** S t a b   s i c h t e n ***********

      /*************************************************************************\
               SSSSS III  AAA  DDDD  M   M III N   N
               S      I  A   A D   D MM MM  I  NN  N
               SSSSS  I  AAAAA D   D M M M  I  N N N
                   S  I  A   A D   D M   M  I  N  NN
               SSSSS III A   A DDDD  M   M III N   N
      \*************************************************************************/
      case "SIADMIN":  // ***************  SICHTER ADMINISTRATOR  *********************
      case "FMADMIN":
        if (debug) {
          echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "fkt:createlist - switch(listenart) -- case (SIADMIN & FMADMIN) ></b><br>";
        }

        include("../4fcfg/fkt_rolle.inc.php");

        /*
          Hole die Liste der gelesenen und der erledigten Nachrichten
        */
        $result = $this->get_list($this->listenart);
        $this->darstellungs_art($this->listenart);
        $this->listen_navi();  //Navigationsbutton

        /*******************************************************************************************************************************/
        if ($result != "") {
          echo "<table style=\"text-align: center; background-color: rgb(250,250, 250); \" border=\"2\" cellpadding=\"2\" cellspacing=\"2\">\n<tbody>\n";
          echo "<tr style=\"background-color: rgb(240,240,200); color:fm=meldung&0000FF; font-weight:bold;\">\n";
          echo "<td>Vorst</td>\n";
          echo "<td>E/A</td>\n";
          echo "<td>Nw-Nr.</td>\n";
          echo "<td>Von/An</td>";
          echo "<td>Abfasszeit</td>\n";
          // Funktionen und Farben
          for ($i = 1; $i <= count($conf_empf); $i++) {
            if (($conf_empf[$i]["fkt"] != "Si") and ($conf_empf[$i]["fkt"] != "A/W")) {
              echo "<td>";
              echo $conf_empf[$i]["fkt"];
              echo "</td>\n";
            }
          }
          echo "<td>Inhalt</td>\n";
          echo "</tr>";

          foreach ($result as $row) {
            // VORRANGSTUFE
            if (($row["09_vorrangstufe"] != "") and ($row["09_vorrangstufe"] != "eee")) {
              echo "<tr style=\"background-color: rgb(255,255,0); color:fm=meldung&FFFFFF; font-weight:bold;\">\n";
            }
            echo "<td>";
            if (($row["09_vorrangstufe"] != "") and ($row["09_vorrangstufe"] != "eee")) {
              echo "<a href=\"mainindex.php?fm=SI-Adminmeldung&00_lfd=" . $row["00_lfd"] . "\" target=\"_self\">" . $row["09_vorrangstufe"] . "</a>\n";
            } else {
              echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
            }
            echo "</td>\n";

            // RICHTUNG Eingang / Ausgang
            echo "<td>";
            if (($row["04_richtung"] != "")) {
              echo "<a href=\"mainindex.php?fm=SI-Adminmeldung&00_lfd=" . $row["00_lfd"] . "\" target=\"_self\">" . $row["04_richtung"] . "</a>\n";
            } else {
              echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
            }
            echo "</td>\n";

            // N a c h w e i s n u m m e r
            echo "<td>";
            if (($row["04_richtung"] != "")) {
              echo "<a href=\"mainindex.php?fm=SI-Adminmeldung&00_lfd=" . $row["00_lfd"] . "\" target=\"_self\">" . $row["04_nummer"] . "</a>\n";
            } else {
              echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
            }
            echo "</td>\n";

            if ($row["04_richtung"] == "A") {
              echo "<td>";
              if (($row["10_anschrift"] != "")) {
                echo "<a href=\"mainindex.php?fm=SI-Adminmeldung&00_lfd=" . $row["00_lfd"] . "\" target=\"_self\">" . $row["10_anschrift"] . "</a>\n";
              } else {
                echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
              }
              echo "</td>\n";
            } else {
              echo "<td>";

              // Absender / Einheit / Stelle / ...
              if (($row["13_abseinheit"] != "")) {
                echo "<a href=\"mainindex.php?fm=SI-Adminmeldung&00_lfd=" . $row["00_lfd"] . "\" target=\"_self\">" . $row["13_abseinheit"] . "</a>\n";
              } else {
                echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
              }
              echo "</td>\n";
            }
            echo "<td>";
            // Abfassungs Z E I T
            if (($row["12_abfzeit"] != "")) {
              $abfzeit = convdatetimeto($row["12_abfzeit"]);
              echo "<a href=\"mainindex.php?fm=SI-Adminmeldung&00_lfd=" . $row["00_lfd"] . "\" target=\"_self\">" . $abfzeit["stak"] . "</a>\n";
            } else {
              echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
            }
            echo "</td>\n";

            // Funktionen und Farben
            $empfcolor = extraiereempfaenger($row["16_empf"]);

            if (debug) {
              echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><br>";
              echo " empfcolor = ";
              var_dump($empfcolor);
              echo "<br><br>";
              echo " conf_empf = ";
              var_dump($conf_empf);
              echo "<br><br>";
            }

            /*            for ($i = 1; $i <= count($conf_empf); $i++) {
              if (($conf_empf[$i]["fkt"] != "Si") and ($conf_empf[$i]["fkt"] != "A/W")) {
                switch ($empfcolor[$conf_empf[$i]["fkt"]]) {*/

            for ($i = 1; $i <= count($conf_empf); $i++) {
              if (
                isset($conf_empf[$i]["fkt"]) &&
                $conf_empf[$i]["fkt"] != "Si" &&
                $conf_empf[$i]["fkt"] != "A/W"
              ) {
                $fkt = $conf_empf[$i]["fkt"];
                $color = $empfcolor[$fkt] ?? null;
                switch ($color) {

                  case "rt":
                    echo "<td style=\"text-align: center; background-color: " . $cfg["vbg"]["rt"] . "; \">";
                    echo "X";
                    break;
                  case "gn":
                    echo "<td style=\"text-align: center; background-color: " . $cfg["vbg"]["gn"] . "; \">";
                    echo "X";
                    break;
                  case "bl":
                    echo "<td style=\"text-align: center; background-color: " . $cfg["vbg"]["bl"] . "; \">";
                    echo "X";
                    break;
                  default:
                    echo "<td style=\"text-align: center; background-color: rgb(250, 250, 250); \">";
                    echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
                }
                echo "</td>";
              }
            }

            // I N H A L T !
            echo "<td align=\"left\">";
            if (($row["12_inhalt"] != "")) {
              echo "<a href=\"mainindex.php?fm=SI-Adminmeldung&00_lfd=" .
                $row["00_lfd"] . "\" target=\"_self\">" .
                substr($row["12_inhalt"], 0, $conf_4f_liste["inhalt"]) . " ..." . "</a>\n";
            } else {
              echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
            }
            echo "</td>\n";
            echo "</tr>";
          }
        }
        echo "</tbody></table>";
        /*********************************************************************************************************************************/

        $this->listen_navi();  //Navigationsbutton		


        /******************************************************************************************************************************************
        $this->darstellungs_art ( $this->listenart );
		$this->listen_navi () ;  //Navigationsbutton
        include ("../4fcfg/fkt_rolle.inc.php");
        $dbaccess = new db_access ($conf_4f_db ["server"], $conf_4f_db ["datenbank"],
                             $conf_4f_tbl ["benutzer"], $conf_4f_db ["user"],  $conf_4f_db ["password"] );
        $query = "SELECT `00_lfd`,
                         `04_richtung`,
                         `04_nummer`,
                         `09_vorrangstufe`,
                         `10_anschrift`,
                         `12_abfzeit`,
                         `13_abseinheit`,
                         `12_inhalt`,
                         `16_empf`
                   FROM `".$conf_4f_tbl ["nachrichten"]."`
                               WHERE 1 order by 04_nummer DESC, 09_vorrangstufe DESC ; ";          //

        $result = $dbaccess->query_table ($query);
        if  ($result != ""){
          echo "<table style=\"text-align: center; background-color: rgb(250,250, 250); \" border=\"2\" cellpadding=\"2\" cellspacing=\"2\">\n<tbody>\n";
          echo "<tr style=\"background-color: rgb(240,240,200); color:fm=meldung&0000FF; font-weight:bold;\">\n";
          echo "<td>Vorst</td>\n";
          echo "<td>E/A</td>\n";
          echo "<td>Nw-Nr.</td>\n";
          echo "<td>Von/An</td>";
          echo "<td>Abfasszeit</td>\n";
          // Funktionen und Farben
          for ( $i=1; $i<= count ($conf_empf); $i++ ) {
            if ( ( $conf_empf [$i]["fkt"] != "Si" ) and ( $conf_empf [$i]["fkt"] != "A/W" ) ) {
              echo "<td>";
              echo $conf_empf [$i]["fkt"];
              echo "</td>\n";
            }
          }
          echo "<td>Inhalt</td>\n";
          echo "</tr>";

          foreach ($result as $row){
             // VORRANGSTUFE
             if ( ( $row["09_vorrangstufe"] != "") and ( $row["09_vorrangstufe"] != "eee" ) ){
               echo "<tr style=\"background-color: rgb(255,255,0); color:fm=meldung&FFFFFF; font-weight:bold;\">\n";
             }
             echo "<td>";
             if ( ( $row["09_vorrangstufe"] != "") and ( $row["09_vorrangstufe"] != "eee" ) ) {
               echo "<a href=\"mainindex.php?fm=SI-Adminmeldung&00_lfd=".$row["00_lfd"]."\" target=\"_self\">".$row["09_vorrangstufe"]."</a>\n" ;
             } else {
               echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
             }
             echo "</td>\n";

             // RICHTUNG Eingang / Ausgang
             echo "<td>";
             if (($row["04_richtung"] != "")) {
               echo "<a href=\"mainindex.php?fm=SI-Adminmeldung&00_lfd=".$row["00_lfd"]."\" target=\"_self\">".$row["04_richtung"]."</a>\n";
             } else {
               echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
             }
             echo "</td>\n";

             // N a c h w e i s n u m m e r
             echo "<td>";
             if (($row["04_richtung"] != "")) {
               echo "<a href=\"mainindex.php?fm=SI-Adminmeldung&00_lfd=".$row["00_lfd"]."\" target=\"_self\">".$row["04_nummer"]."</a>\n";
             } else {
               echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
             }
             echo "</td>\n";

             if ($row["04_richtung"] == "A" ) {
               echo "<td>";
               if (($row["10_anschrift"] != "")) {
                 echo "<a href=\"mainindex.php?fm=SI-Adminmeldung&00_lfd=".$row["00_lfd"]."\" target=\"_self\">".$row["10_anschrift"]."</a>\n";
               } else {
                 echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
               }
               echo "</td>\n";
             } else {
               echo "<td>";

             // Absender / Einheit / Stelle / ...
             if (($row["13_abseinheit"] != "")) {
               echo "<a href=\"mainindex.php?fm=SI-Adminmeldung&00_lfd=".$row["00_lfd"]."\" target=\"_self\">".$row["13_abseinheit"]."</a>\n";
               } else {
                 echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
               }
               echo "</td>\n";
             }
             echo "<td>";
             // Abfassungs Z E I T
             if (($row["12_abfzeit"] != "")) {
               $abfzeit = convdatetimeto ($row["12_abfzeit"]);
               echo "<a href=\"mainindex.php?fm=SI-Adminmeldung&00_lfd=".$row["00_lfd"]."\" target=\"_self\">".$abfzeit[stak]."</a>\n";
             } else {
               echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
             }
             echo "</td>\n";

             // Funktionen und Farben
             $empfcolor = extraiereempfaenger ( $row ["16_empf"] ) ;
             for ( $i=1; $i<= count ($conf_empf); $i++ ) {
               if ( ( $conf_empf [$i]["fkt"] != "Si" ) and ( $conf_empf [$i]["fkt"] != "A/W" ) ) {
                 switch ($empfcolor [$conf_empf [$i][fkt]]) {
                  case "rt":
                   echo "<td style=\"text-align: center; background-color: ".$cfg["vbg"]["rt"]."; \">";
                   echo "X";
                  break;
                  case "gn":
                   echo "<td style=\"text-align: center; background-color: ".$cfg["vbg"]["gn"]."; \">";
                   echo "X";
                  break;
                  case "bl":
                   echo "<td style=\"text-align: center; background-color: ".$cfg["vbg"]["bl"]."; \">";
                   echo "X";
                  break;
                  default:
                   echo "<td style=\"text-align: center; background-color: rgb(250, 250, 250); \">";
                   echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
                 }
                 echo "</td>";
               }
             }

             // I N H A L T !
             echo "<td align=\"left\">";
             if (($row["12_inhalt"] != "")) {
               echo "<a href=\"mainindex.php?fm=SI-Adminmeldung&00_lfd=".
                        $row["00_lfd"]."\" target=\"_self\">".
                        substr($row["12_inhalt"], 0, $conf_4f_liste ["inhalt"])." ..."."</a>\n";
             } else {
               echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
             }
             echo "</td>\n";
             echo "</tr>";
          }
        }
        echo "</tbody></table>";
         ************************************************************************************************************************************/

        break; // ***************  SICHTER ADMINISTRATOR  *********************


      case "FmNwE":  // *****  F M N W E ingang ******
        if (debug) {
          echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "fkt:createlist - switch(listenart) -- case (FmNwE) ></b><br>";
        }
        $dbaccess = new db_access(
          $conf_4f_db["server"],
          $conf_4f_db["datenbank"],
          $conf_4f_tbl["benutzer"],
          $conf_4f_db["user"],
          $conf_4f_db["password"]
        );
        $query = "SELECT `00_lfd`,`09_vorrangstufe`,`04_richtung`, `04_nummer`, `10_anschrift`,
                         `12_abfzeit`, `12_inhalt`, `13_abseinheit`, `x01_abschluss`
                  FROM `" . $conf_4f_tbl["nachrichten"] . "`
                  WHERE 04_richtung = \"E\" order by 04_nummer ASC ; ";
        $result = $dbaccess->query_table($query);
        echo "<p align=\"center\"><big><big><big><b>Nachweisung Eingang</b></big></big></big></p>";
        if ($result != "") {
          echo "<table style=\"text-align: center; background-color: rgb(255,255,255); \" border=\"2\" cellpadding=\"2\" cellspacing=\"2\">\n<tbody>\n";
          echo "<tr style=\"background-color: rgb(240,240,200); color:#000000; font-weight:bold;\">\n";
          echo "<td>Vorrang</td>\n";
          echo "<td>E/A</td>\n";
          echo "<td>Num</td>\n";
          echo "<td>Von/An</td>";
          echo "<td>Abfasszeit</td>\n";
          echo "<td>Inhalt</td>\n";
          echo "</tr>";
          if ($result != "") {
            foreach ($result as $row) {
              echo "<td>";
              if (($row["09_vorrangstufe"] != "") and
                ($row["09_vorrangstufe"] != "eee")
              ) {
                echo "<a>" . $row["09_vorrangstufe"] . "</a>\n";
              } else {
                echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
              }
              echo "</td>\n";
              echo "<td>";
              if (($row["04_richtung"] != "")) {
                echo "<a>" . $row["04_richtung"] . "</a>\n";
              } else {
                echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
              }
              echo "</td>\n";
              echo "<td>";
              if (($row["04_nummer"] != "")) {
                echo "<a>" . $row["04_nummer"] . "</a>\n";
              } else {
                echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
              }
              echo "</td>\n";
              if ($row["04_richtung"] == "A") {
                echo "<td>";
                if (($row["10_anschrift"] != "")) {
                  echo "<a>" . $row["10_anschrift"] . "</a>\n";
                } else {
                  echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
                }
                echo "</td>\n";
              } else {
                echo "<td>";
                if (($row["13_abseinheit"] != "")) {
                  echo "<a>" . $row["13_abseinheit"] . "</a>\n";
                } else {
                  echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
                }
                echo "</td>\n";
              }
              echo "<td>";
              if (($row["12_abfzeit"] != "")) {
                $arr    = convdatetimeto($row["12_abfzeit"]);
                $abzeit = $arr["stak"];
                echo "<a>" . $abzeit . "</a>\n";
              } else {
                echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
              }
              echo "</td>\n";
              echo "<td align=\"left\">";
              if (($row["12_inhalt"] != "")) {
                echo "<a>" . $row["12_inhalt"] . "</a>\n";
              } else {
                echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
              }
              echo "</td>\n";
              echo "</tr>";
            }  // foreach $result
          } // if 2. result == ""
          echo "</tbody></table>";
        } else { // Result ist leer
          echo "<big><big><big>Keine Daten vorhanden!</big></big></big>";
        }
        break; // *****  F M N W E ingang ******

      case "FmNwA":  // *****  F M N W A usgang ******
        if (debug) {
          echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "fkt:createlist - switch(listenart) -- case (FmNwA) ></b><br>";
        }
        $dbaccess = new db_access(
          $conf_4f_db["server"],
          $conf_4f_db["datenbank"],
          $conf_4f_tbl["benutzer"],
          $conf_4f_db["user"],
          $conf_4f_db["password"]
        );
        $query = "SELECT `00_lfd`,`09_vorrangstufe`,`04_richtung`, `04_nummer`, `10_anschrift`,
                         `12_abfzeit`, `12_inhalt`, `13_abseinheit`, `x01_abschluss`
                  FROM `" . $conf_4f_tbl["nachrichten"] . "`
                  WHERE 04_richtung = \"A\" order by 04_nummer ASC ; ";
        $result = $dbaccess->query_table($query);
        echo "<p align=\"center\"><big><big><big><b>Nachweisung Ausgang</b></big></big></big></p>";
        if ($result != "") {
          echo "<table style=\"text-align: center; background-color: rgb(255,255,255); \" border=\"2\" cellpadding=\"2\" cellspacing=\"2\">\n<tbody>\n";
          echo "<tr style=\"background-color: rgb(240,240,200); color:#000000; font-weight:bold;\">\n";
          echo "<td>Vorrang</td>\n";
          echo "<td>E/A</td>\n";
          echo "<td>Num</td>\n";
          echo "<td>Von/An</td>";
          echo "<td>Abfasszeit</td>\n";
          echo "<td>Inhalt</td>\n";
          echo "</tr>";
          if ($result != "") {
            foreach ($result as $row) {
              echo "<td>";
              if (($row["09_vorrangstufe"] != "") and ($row["09_vorrangstufe"] != "eee")) {
                echo "<a>" . $row["09_vorrangstufe"] . "</a>\n";
              } else {
                echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
              }
              echo "</td>\n";
              echo "<td>";
              if (($row["04_richtung"] != "")) {
                echo "<a>" . $row["04_richtung"] . "</a>\n";
              } else {
                echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
              }
              echo "</td>\n";
              echo "<td>";
              if (($row["04_nummer"] != "")) {
                echo "<a>" . $row["04_nummer"] . "</a>\n";
              } else {
                echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
              }
              echo "</td>\n";
              if ($row["04_richtung"] == "A") {
                echo "<td>";
                if (($row["10_anschrift"] != "")) {
                  echo "<a>" . $row["10_anschrift"] . "</a>\n";
                } else {
                  echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
                }
                echo "</td>\n";
              } else {
                echo "<td>";
                if (($row["13_abseinheit"] != "")) {
                  echo "<a>" . $row["13_abseinheit"] . "</a>\n";
                } else {
                  echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
                }
                echo "</td>\n";
              }
              echo "<td>";
              if (($row["12_abfzeit"] != "")) {
                $arr    = convdatetimeto($row["12_abfzeit"]);
                $abzeit = $arr["stak"];
                echo "<a>" . $abzeit . "</a>\n";
              } else {
                echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
              }
              echo "</td>\n";
              echo "<td align=\"left\">";
              if (($row["12_inhalt"] != "")) {
                echo "<a>" . $row["12_inhalt"] . "</a>\n";
              } else {
                echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
              }
              echo "</td>\n";
              echo "</tr>";
            }  // foreach $result
          }
          echo "</tbody></table>";
        } else { // Result ist leer
          echo "<big><big><big>Keine Daten vorhanden!</big></big></big>";
        }
        break; // *****  F M N W A usgang ******

      case "FmNw":  // *****  F M N W  ******
        if (debug) {
          echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "fkt:createlist - switch(listenart) -- case (FmNw) ></b><br>";
        }
        $dbaccess = new db_access(
          $conf_4f_db["server"],
          $conf_4f_db["datenbank"],
          $conf_4f_tbl["benutzer"],
          $conf_4f_db["user"],
          $conf_4f_db["password"]
        );
        $query = "SELECT `00_lfd`,`01_datum`,`01_zeichen`,`02_zeit`,`03_datum`, `09_vorrangstufe`,`04_richtung`,
                         `04_nummer`, `10_anschrift`, `12_inhalt`, `13_abseinheit`,`14_zeichen`, `x01_abschluss`
                  FROM `" . $conf_4f_tbl["nachrichten"] . "`
                  WHERE 1 order by 04_nummer ASC ; ";
        $result = $dbaccess->query_table($query);
        echo "<p align=\"center\"><big><big><big><b>Einsatz " . $conf_4f_db["datenbank"] . " - " . $conf_4f["anschrift"] . "</big><br>Nachweisung Eingang / Ausgang</b></big></big></p>";
        if ($result != "") {
          echo "<table style=\"text-align: center; background-color: rgb(255,255,255); \" border=\"2\" cellpadding=\"2\" cellspacing=\"2\">\n<tbody>\n";
          echo "<tr style=\"background-color: rgb(240,240,200); color:#000000; font-weight:bold;\">\n";
          echo "<td>Vorrang</td>\n";
          echo "<td>E/A</td>\n";
          echo "<td>Num</td>\n";
          echo "<td>Von/An</td>";
          echo "<td>Aufnahme</td>\n";
          //          echo "<td>Aufn.Z</td>\n";
          echo "<td>Annahme</td>\n";
          echo "<td>Bef&ouml;rder</td>\n";
          //          echo "<td>Verfasser</td>\n";
          echo "<td>Inhalt</td>\n";
          echo "</tr>";
          if ($result != "") {
            foreach ($result as $row) {
              echo "<td>";
              if (($row["09_vorrangstufe"] != "") and ($row["09_vorrangstufe"] != "eee")) {
                echo "<a>" . $row["09_vorrangstufe"] . "</a>\n";
              } else {
                echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
              }
              echo "</td>\n";
              echo "<td>";
              if (($row["04_richtung"] != "")) {
                echo "<a>" . $row["04_richtung"] . "</a>\n";
              } else {
                echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
              }
              echo "</td>\n";
              echo "<td>";
              if (($row["04_nummer"] != "")) {
                echo "<a>" . $row["04_nummer"] . "</a>\n";
              } else {
                echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
              }
              echo "</td>\n";
              if ($row["04_richtung"] == "A") {
                echo "<td>";
                if (($row["10_anschrift"] != "")) {
                  echo "<a>" . $row["10_anschrift"] . "</a>\n";
                } else {
                  echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
                }
                echo "</td>\n";
              } else {
                echo "<td>";
                if (($row["13_abseinheit"] != "")) {
                  echo "<a>" . $row["13_abseinheit"] . "</a>\n";
                } else {
                  echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
                }
                echo "</td>\n";
              }


              echo "<td>";
              if (($row["01_datum"] != "") and ($row["01_datum"] != "0000-00-00 00:00:00")) {
                $arr    = convdatetimeto($row["01_datum"]);
                $abzeit = $arr["stak"];
                echo "<a>" . $abzeit . "</a>\n";
              } else {
                echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
              }
              echo "</td>";
              // Aufn. Zeichen
              // Aufn. Zeichen
              /*              echo "<td>";
               if (($row["01_zeichen"] != "")) {
                 echo "<a>".$row["01_zeichen"]."</a>\n"; }
               else {
                 echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
               }
               echo "</td>\n";
*/
              /*
               echo "<td>";
               if (($row["01_zeichen"] != "")) {
                 echo "<a>".$row["01_zeichen"]."</a>\n"; }
               else {
                 echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
               }
               echo "</td>\n";
*/
              echo "<td>";
              if (($row["02_zeit"] != "") and ($row["02_zeit"] != "0000-00-00 00:00:00")) {
                $arr    = convdatetimeto($row["02_zeit"]);
                $abzeit = $arr["stak"];
                echo "<a>" . $abzeit . "</a>\n";
              } else {
                echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
              }
              echo "</td>";

              echo "<td>";
              if (($row["03_datum"] != "") and ($row["03_datum"] != "0000-00-00 00:00:00")) {
                $arr    = convdatetimeto($row["03_datum"]);
                $abzeit = $arr["stak"];
                echo "<a>" . $abzeit . "</a>\n";
              } else {
                echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
              }
              echo "</td>\n";
              // Verfasser
              /*               echo "<td>";
               if (($row["14_zeichen"] != "")) {
                 echo "<a>".$row["14_zeichen"]."</a>\n"; }
               else {
                 echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
               }
               echo "</td>\n";
*/
              echo "<td align=\"left\">";
              if (($row["12_inhalt"] != "")) {
                echo "<a>" . $row["12_inhalt"] . "</a>\n";
              } else {
                echo "<p><img src=\"null.gif\" alt=\"leer\"></p>";
              }
              echo "</td>\n";
              echo "</tr>";
            }  // foreach $result
          }
          echo "</tbody></table>";
        } else { // Result ist leer
          echo "<big><big><big>Keine Daten vorhanden!</big></big></big>";
        }
        break;  // *****  F M N W  ******
    } // switch
    if (debug) {
      echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b> ### - fkt:createlist - switch_ENDE(listenart) ></b><br>";
    }
  }
  //echo "</form>\n";

} // class  Listen
