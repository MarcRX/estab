<?php declare(strict_types=1);
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start(); // Start session only if not already started
}

/*****************************************************************************\
   Datei: mainindex.php

   benoetigte Dateien: config.inc.php, protokoll.php, db_operation.php,
                      4fachform.php, liste.php, data_hndl.php, menue.php
   Beschreibung:
           HAUPTSTEUERUNGSDATEI

   (C) Hajo Landmesser
   mailto://hajo.landmesser@oberbruch.de
\*****************************************************************************/
/*
/var/wwwssl/kats/4fach/mainindex.php:80:
array (size=26)
  
'kate_todo' => string 'speichern' (length=9)
  
  'msglfd' => string '5' (length=1)
  'kategorien_master_oben' => string 'EA West' (length=7)
  'kategorien_master' => string 'EA West' (length=7)
  'kategorien_fkt_oben' => string '' (length=0)
  'kategorien_fkt' => string '' (length=0)

  '4fachkatego_absenden_x' => string '53' (length=2)
  
  '4fachkatego_absenden_y' => string '6' (length=1)
  '00_lfd' => string '5' (length=1)
  'task' => string 'Stab_lesen' (length=10)
  '01_medium' => string '' (length=0)
  '04_richtung' => string 'A' (length=1)
  '04_nummer' => string '5' (length=1)
  '06_befwegausw' => string '' (length=0)
  '07_durchspruch' => string '' (length=0)
  '08_befhinwausw' => string '' (length=0)
  '09_vorrangstufe' => string '' (length=0)
  '10_anschrift' => string 'EA West' (length=7)
  '11_gesprnotiz' => string '' (length=0)
  '12_inhalt' => string 'Geben sie Lagemeldung' (length=21)
  '12_abfzeit' => string '140952sep2025' (length=13)
  '13_abseinheit' => string 'EL' (length=2)
  '14_zeichen' => string 'kl' (length=2)
  '14_funktion' => string 'S2' (length=2)
  'kategorien_master_unten' => string 'EA West' (length=7)
  'kategorien_fkt_unten' => string '' (length=0)

*/

if (!defined('debug')) {   // true = gibt debuginformationen aus
    define('debug', false);
}

if (debug) {
  ini_set('display_errors', '1');
  error_reporting(E_ALL);

  echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><big><big>Dateianfang</big></big><br>";
}

define("create_vordrucke", true);   // Erstellt PDF oder/und PNG Dokumente für die Rückfallebene

/*if (debug) {
  echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><big><big>Steuerungsdatei</big></big><br>SESSION = ";
  echo "<big><big>SESSION = </big></big>"; var_dump($_SESSION);
  echo "<big><big>GET = </big></big>"; var_dump($_GET);
  echo "<big><big>POST = </big></big>"; var_dump($_POST);
  echo "<big><big>SERVER = </big></big>"; var_dump($_SERVER);
}*/

/*if ((
     (!isset ($_GET) and !isset($_POST)) or
     ($_GET[session] == "reset")) and (isset($_SESSION)))
     {
        if ( debug ){ echo "<b>!File:". __FILE__ ."  Line:". __LINE__ ."</b><big><big>vor session_destroy</big></big><br>";  }
        session_destroy();
        if ( debug ){ echo "<b>!File:". __FILE__ ."  Line:". __LINE__ ."</b><big><big>nach session_destroy</big></big><br>";  }
      }*/
/*******************************************************************************************************/
if (
  (
    (!empty($_GET) && !empty($_POST)) ||
    (isset($_GET["session"]) && $_GET["session"] === "reset")
  ) && isset($_SESSION)
) {
  session_destroy();
  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><big><big><b>session_destroy</b></big></big><br>";
  }
}
/*******************************************************************************************************/

$returnValue = null; // first set returnValue to a defined state
$formdata    = array();
$returnValue = !empty($_POST) ? $_POST : (!empty($_GET) ? $_GET : []);
$sessiondata = !empty($_SESSION) ? $_SESSION : array();

/*

$pre_01medium = "Fu";

if()
Für Marc beim Southside Festival zum schnelleren Eintragen von Nachrichten
Weiter unten wird bei einem Nachrichteneingang das Medium Fe Fu Me Fax @ gesetzt.
*/

if (debug) {
  echo "<br><br>\n------------------------Info Block Start------------------------------<br>\n";
  echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><br>";
  echo "returnValue=";
  var_dump($returnValue);
  echo "<br>\n";
  echo "GET=";
  var_dump($_GET);
  echo "<br>\n";
  echo "POST=";
  var_dump($_POST);
  echo "<br>\n";
  echo "COOKIE=";
  var_dump($_COOKIE);
  echo "<br>\n";
  echo "sessiondata=";
  var_dump($sessiondata);
  echo "<br>\n";
  echo "SESSION=";
  var_dump($_SESSION);
  echo "<br>\n------------------------Info Block Ende------------------------------<br>\n";
}

include("../4fcfg/config.inc.php");    // Konfigurationseinstellungen und Vorgaben
include("../4fcfg/dbcfg.inc.php");     // Datenbankparameter

if (debug) {
  echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b>vor include fkt_rolle_inc_php<br>";
}

include("../4fcfg/fkt_rolle.inc.php"); // Mitspieler

if (debug) {
  echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><big>nach include fkt_rolle_inc_php</big><br>";
}

if (debug) {
  echo "<br><br>\n------------------------Info Block Start------------------------------<br>\n";
  echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><br>";
  echo "SESSION=";
  var_dump($_SESSION);
  echo "<br>\n------------------------Info Block Ende------------------------------<br>\n";
}

include_once("protokoll.php");         // Protokolllierung in der Datenbank
include("db_operation.php");           // Datenbank operationen
include("4fachform.php");              // Formular Behandlung 4fach Vordruck
include("liste.php");                  // erzeuge Ausgabelisten
include("data_hndl.php");              // Schnittstelle zur Datenbank

$kats_table_css = "table, table th, table td, table a, table b, table big, table input, table select, table span { font-family: 'Liberation Sans' !important; font-size: 16px !important; font-weight: normal !important; }";

if (debug) {
  echo "<br><br>\n------------------------Info Block Start------------------------------<br>\n";
  echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><br>";
  echo "SESSION=";
  var_dump($_SESSION);
  echo "<br>\n------------------------Info Block Ende------------------------------<br>\n";
}

/*
 Prüfe Datenbankverbindung
*/
$db = mysqli_connect($conf_4f_db["server"], $conf_4f_db["user"], $conf_4f_db["password"]);
mysqli_query($db, 'SET NAMES utf8mb4');
try {
    $db->query('DO 1');
} catch (mysqli_sql_exception $e) {
  echo "<h1>Es besteht keine Verbindung zur Datenbank.</h1>";
  echo "<big><b>Mögliche Ursachen:<br></b>";
  echo " 1. Datenbankserver ist nicht erreichbar weil aus.<br>";
  echo " 2. Netzwerkfehler, wenn DB-Server auf anderem Server.<br>";
  echo " 3. Benutzer oder Passwort stimmen nicht.<br><br>";
  echo "Bitte unter \"administrative Massnahme\" - \"Datenbankparameter eingeben\" die Parameter einstellen.";
  echo "</big>";
  exit;
}
if (isset($db)) {
  mysqli_close($db);
  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><big>Close DB</big><br>";
  }
}

/**********************************************************************\
 * Funktion resetframeset
 * 
 * 
\**********************************************************************/
function resetframeset($rootpath)
{
  include("../4fcfg/config.inc.php");

  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b> -- resetframeset<br>conf_4f  = ";
    var_dump($conf_4f);
  }

  $nameversion = implode("", $conf_4f["NameVersion"]);

  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b> -- nameversion = ";
    var_dump($nameversion);
  }

  pre_html("reset", "Framereset " . $nameversion, ""); // Normaler Seitenaufbau mit Auffrischung
  echo "<body style=\"font-family: 'Liberation Sans';\" onLoad=\"FramesVeraendern('" . $rootpath . "/4fach/counter.php','counter','" . $rootpath . "/4fach/vorgaben.php','vorgaben','" . $rootpath . "/4fach/mainindex.php','mainframe')\">";
}

if (isset($returnValue["reset_record"])) {
  reset_record_lock($returnValue["reset_record"]);

  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><big><big>reset_record_lock</big></big><br>";
  }
}


/**********************************************************************\
    Es gab noch keinen Kontakt ==> Begruessung
  \**********************************************************************/
if (isset($returnValue["m2_parameter_x"])) {
  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><big><big>Begruessung Parametrierung </big></big><br>";
  }
  include("./edt_para.php");
  $para = new parametrierung();
}

/*******************************************************************
ANTWORT % WEITERLEITUNG
 *******************************************************************/
$weiterantwort = false;
if (((isset($returnValue["weiterleiten_x"])) or
    (isset($returnValue["antwort_x"]))) and
  (($returnValue["task"] == "FM-Ausgang") or
    ($returnValue["task"] == "FM-Ausgang_Sichter"))
) {
  $weiterantwort = true;
  $_SESSION["sw_data"] = $returnValue;
} elseif ((isset($returnValue["abbrechen_x"]) and (isset($_SESSION["sw_data"]))) and
  (($returnValue["task"] == "FM-Eingang") or ($returnValue["task"] == "FM-Eingang_Sichter"))
) {
  unset($_SESSION["sw_data"]);
}

/****************************************************************************\
    Kategorien
  \****************************************************************************/
// Kategorie Master
if (isset($returnValue["ma_ktgotyp"])) {
  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><big>isset returnValue [ma_ktgotyp]</big><br>";
    echo "SESSION=";
    var_dump($_SESSION);
  }
  
  if ($returnValue["ma_ktgo"] == "alle") {
    unset($_SESSION["ma_kategotyp"]);
    unset($_SESSION["ma_katego"]);
  } else {
    $_SESSION ["ma_kategotyp"]    = $returnValue["ma_ktgotyp"];
    $_SESSION ["ma_katego"]       = $returnValue["ma_ktgo"];
    $_SESSION ["filter_start"]    = 0 ;
    $_SESSION ["filter_position"] = 0 ;
  }
  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><br>";
    var_dump($_SESSION) ;
  }
}
// Kategorie FUNKTION
if (isset($returnValue["fk_ktgotyp"])) {
  if ($returnValue["fk_ktgo"] == "alle") {
    unset($_SESSION["fk_kategotyp"]);
    unset($_SESSION["fk_katego"]);
  } else {
    $_SESSION["fk_kategotyp"] = $returnValue["fk_ktgotyp"];
    $_SESSION["fk_katego"]    = $returnValue["fk_ktgo"];
    $_SESSION["filter_start"] = 0;
    $_SESSION["filter_position"] = 0;
  }
}
// Kategorie USER
if (isset($returnValue["us_ktgotyp"])) {
  if ($returnValue["us_ktgo"] == "alle") {
    unset($_SESSION["us_kategotyp"]);
    unset($_SESSION["us_katego"]);
  } else {
    $_SESSION["us_kategotyp"] = $returnValue["us_ktgotyp"];
    $_SESSION["us_katego"]    = $returnValue["us_ktgo"];
    $_SESSION["filter_start"] = 0;
    $_SESSION["filter_position"] = 0;
  }
}


if (isset($returnValue["4fachkatego_absenden_x"])) {
  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><big><big>vor katgoedt.php</big></big><br>";
  }
  include("../4fach/katgoedt.php");
  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><big><big>nach katgoedt.php</big></big><br>";
  }
}


// Aufruf von Anhang vom 4fach Vordruck aus ==> es könnte schon Inhalt im Formular vorhanden sein
if (isset($returnValue["anhang_plus_x"])) {
  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><big><big>anhang_plus_x  anhang.php einbinden</big></big><br>";
  }
  $_SESSION["anhang_menue"] = "100";
  include("anhang.php");
  exit;
}

/**********************************************************************\
    --- S T A B und F M Z   s c h r e i b e n   m i t  A n h a n g ---

    Oeffnet ein Fenster in dem Anhaenge ausgewaehlt werden koennen
  \**********************************************************************/
if ((isset($returnValue["stab_anhang_x"]) or  isset($returnValue["fm_anhang_x"])
) and  (!isset($returnValue["ah_auswahl_x"]))) {

  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><br>";
  }
  $_SESSION["anhang_menue"] = 100;
  include("anhang.php");
  $menue1 = "anhang";
  exit;
}




/**********************************************************************\
    Überprüfe ob die Listendarstellung geändert werden soll
  \**********************************************************************/
if (!isset($_SESSION["filter_darstellung"])) {
  $_SESSION["filter_darstellung"] = 1;
  $_SESSION["filter_erledigt"]    = 0;
  $_SESSION["filter_unerledigt"]  = 1;
  $_SESSION["filter_anzahl"]      = 15;
  $_SESSION["filter_start"]       = 0;
  $_SESSION["filter_position"]    = 0;
}
// filtern EIN / AUS
if ((isset($returnValue["filter_darstellung_aus_x"])) or
  (isset($returnValue["filter_darstellung_ein_x"]))
) {

  if (($_SESSION["filter_darstellung"] == 1) and (isset($returnValue["filter_darstellung_aus_x"]))) {
    $_SESSION["filter_darstellung"] = 0;
  } elseif (($_SESSION["filter_darstellung"] == 0) and (isset($returnValue["filter_darstellung_ein_x"]))) {
    $_SESSION["filter_darstellung"] = 1;
  }
}

// erledigte SICHTBAR UNSICHTBAR
if (((isset($returnValue["filter_erledigt_aus_x"])) or
  (isset($returnValue["filter_erledigt_ein_x"]))) and !isset($returnValue["flt_search"])) {

  if (($_SESSION["filter_erledigt"] == 1) and (isset($returnValue["filter_erledigt_aus_x"]))) {
    $_SESSION["filter_erledigt"] = 0;
  } elseif (($_SESSION["filter_erledigt"] == 0) and (isset($returnValue["filter_erledigt_ein_x"]))) {
    $_SESSION["filter_erledigt"] = 1;
  }
}
// unerledigte SICHTBAR UNSICHTBAR
if (((isset($returnValue["filter_unerledigt_aus_x"])) or
  (isset($returnValue["filter_unerledigt_ein_x"]))) and !isset($returnValue["flt_search"])) {

  if (($_SESSION["filter_unerledigt"] == 1) and (isset($returnValue["filter_unerledigt_aus_x"]))) {
    $_SESSION["filter_unerledigt"] = 0;
  } elseif (($_SESSION["filter_unerledigt"] == 0) and (isset($returnValue["filter_unerledigt_ein_x"]))) {
    $_SESSION["filter_unerledigt"] = 1;
  }
}

// finde Menü Ein und Ausschalten des Findemenüs
if (!isset($_SESSION["flt_find_mask"])) {
  $_SESSION["flt_find_mask"] = 0;
}
if ((isset($returnValue["flt_find_mask_aus_x"])) or
  (isset($returnValue["flt_find_mask_ein_x"]))
) {
  if (($_SESSION["flt_find_mask"] == 1) and (isset($returnValue["flt_find_mask_aus_x"]))) {
    $_SESSION["flt_search"] = NULL;
    unset($_SESSION["flt_search"]);
    $_SESSION["flt_find_mask"] = 0;
  } elseif (($_SESSION["flt_find_mask"] == 0) and (isset($returnValue["flt_find_mask_ein_x"]))) {
    $_SESSION["flt_find_mask"] = 1;
  }
}

if (isset($returnValue["filter_suche_reset"])) {
  unset($_SESSION["flt_search"]);
}

if (isset($returnValue["flt_search"]) and (($_SESSION["flt_find_mask"] ?? 0) == 1)) {
  if (($_SESSION["flt_search"] ?? null) != $returnValue["flt_search"]) {
    $_SESSION["filter_start"] = 0;
    $_SESSION["filter_position"] = 0;
    $_SESSION["flt_search"] = $returnValue["flt_search"];
  } else {
    $_SESSION["flt_search"] = $returnValue["flt_search"];
  }
}

if (isset($returnValue['filter_anzahl_x'])) {
  $_SESSION['filter_anzahl'] = $returnValue['filter_anzahl'];
}
if (isset($returnValue['flt_start_x'])) {
  $_SESSION['flt_navi'] = "start";
}
if (isset($returnValue['flt_back_x'])) {
  $_SESSION['flt_navi'] = "back";
}
if (isset($returnValue['flt_for_x'])) {
  $_SESSION['flt_navi'] = "for";
}
if (isset($returnValue['flt_end_x'])) {
  $_SESSION['flt_navi'] = "end";
}

/************************************************************************\

  \************************************************************************/
if (isset($returnValue["action"])) {
  // gelesen
  if ($returnValue["action"] == "gelesen")
    if ($returnValue["todo"] == "set") {
      set_msg_read($returnValue["00_lfd"]);
    } else {
      unset_msg_read($returnValue["00_lfd"]);
    }
  // erledigt
  if ($returnValue["action"] == "erledigt")
    if ($returnValue["todo"] == "set") {
      set_msg_done($returnValue["00_lfd"]);
    } else {
      unset_msg_done($returnValue["00_lfd"]);
    }
}

/**********************************************************************\
    Es gab noch keinen Kontakt ==> Begruessung
  \**********************************************************************/
if (!isset($_SESSION["menue"])) {
  $_SESSION["menue"] = "WELCOME";
}

/**********************************************************************\
    Der Anmelde Button wurde gedrueckt
  \**********************************************************************/
if (isset($returnValue["login"]) && $returnValue["login"] == "Anmelden") {
  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><big><big>anmelden</big></big><br>";
  }
  $_SESSION["menue"] = "LOGIN";
}
/*ma_ktgotyp=global
ma_ktgo=EA%20West*/

/**********************************************************************\
    Es kommen Anmeldedaten die geprueft und gespeichert werden muessen
  \**********************************************************************/
$doppelkennwort = false;
if (
  ((isset($returnValue["benutzer"])) &&
    (isset($returnValue["kuerzel"])) &&
    (isset($returnValue["funktion"])) &&
    ($_SESSION["menue"] == "LOGIN"))
) {
  // Es wurden beide Kennworte gesetzt
  if (
    (((isset($returnValue["2teskennwort"])) && ($returnValue["2teskennwort"] == "Yes")) ||
      (isset($returnValue["kennwort1"])) &&
      (isset($returnValue["kennwort2"])) &&
      ($returnValue["kennwort1"] != "") &&
      ($returnValue["kennwort2"] != "") &&
      ($returnValue["kennwort1"] == $returnValue["kennwort2"])) or

    ((isset($returnValue["2teskennwort"])) && ($returnValue["2teskennwort"] != "Yes") ||
      (isset($returnValue["kennwort1"])))
  ) {
    if (debug) {
      echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><br>";
    }
    $error = check_save_user();
    if (!$error) {
      $_SESSION["menue"] = "ROLLE";  //   führt zu Fehlern bei der Menüdarstellung
      if (debug) {
        echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><br>";
      }
      resetframeset($conf_urlroot . $conf_web["pre_path"]);
    } else {
      $menuename = $returnValue["benutzer"] ?? "";
      $menuekuerzel = $returnValue["kuerzel"] ?? "";
      $menuefunktion = $returnValue["funktion"] ?? "";
      // Bestehender Benutzer: nur ein Kennwortfeld, keine Bestätigung.
      if (isset($login_hinweis) && str_contains($login_hinweis, "Passwort falsch")) {
        $doppelkennwort = false;
      }
    }
    // Wenn Benutzer OK ==> SESSION ["menue"]="ROLLE" ; $_SESSION ["ROLLE"]= "Stab", Fernmelder...
  } else {
    if (debug) {
      echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><br>";
    }

    if (((isset($returnValue["2teskennwort"])) && ($returnValue["2teskennwort"] == "Yes")) &&
      (isset($returnValue["kennwort1"])) &&
      (isset($returnValue["kennwort2"])) &&
      ($returnValue["kennwort1"] != "") &&
      ($returnValue["kennwort2"] != "") &&
      ($returnValue["kennwort1"] != $returnValue["kennwort2"])
    ) {
      // Kennwort1 ungleich Kennwort2
      if (isset($returnValue["benutzer"])) {
        $menuename     = $returnValue["benutzer"];
      }
      if (isset($returnValue["kuerzel"])) {
        $menuekuerzel  = $returnValue["kuerzel"];
      }
      if (isset($returnValue["funktion"])) {
        $menuefunktion = $returnValue["funktion"];
      }
      $doppelkennwort = true;
      if (debug) {
        echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><br>";
      }
    } else {
      if (debug) {
        echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><br>";
      }
      if (isset($returnValue["benutzer"])) {
        $menuename     = $returnValue["benutzer"];
      }
      if (isset($returnValue["kuerzel"])) {
        $menuekuerzel  = $returnValue["kuerzel"];
      }
      if (isset($returnValue["funktion"])) {
        $menuefunktion = $returnValue["funktion"];
      }
      $doppelkennwort = false;
    }
  } // else
}
/* Wenn noch keine Anmeldung erfolgt ist sind die Variablen nicht vorhanden, um Warnungen zu vermeiden 
     werden leere Strings initialisiert. */
if (!isset($menuename)) {
  $menuename     = "";
}
if (!isset($menuekuerzel)) {
  $menuekuerzel  = "";
}
if (!isset($menuefunktion)) {
  $menuefunktion = "";
}

$gesprnotizsichter = false; // false == Voreinstellung fuer dieses Skript

/**********************************************************************
  Daten kommen vom Formular zurueck und koennen gespeichert bzw.
  verarbeitet werden.
  checkandsave befindet sich in data_hndl.php
 ***********************************************************************/

// Abbruch der Gesprächsnotiz beim Sichten
/*
  if ( ( $returnValue["abbrechen_x"] ) and ( $_SESSION ["gesprnoti"] ) ){
    unset ( $_SESSION ['gesprnoti'] );
  }
*/

if (!empty($returnValue["abbrechen_x"]) && isset($_SESSION["gesprnoti"])) {
  unset($_SESSION['gesprnoti']);
}

/*
  if ( ( ( $returnValue["task"] == "Stab_schreiben" ) or
         ( $returnValue["task"] == "Stab_gesprnoti" ) or
         ( $returnValue["task"] == "FM-Ausgang" ) or
         ( $returnValue["task"] == "FM-Ausgang_Sichter" ) or
         ( $returnValue["task"] == "FM-Admin" ) or
         ( $returnValue["task"] == "FM-Eingang" ) or
         ( $returnValue["task"] == "FM-Eingang_Sichter" ) or
         ( $returnValue["task"] == "FM-Eingang_Anhang" ) or
         ( $returnValue["task"] == "FM-Eingang_Anhang_Sichter" ) or
         ( $returnValue["task"] == "Stab_sichten" ) or
         ( $returnValue["task"] == "SI-Admin" ) )  and (
         ( !isset ($returnValue["abbrechen_x"])) or
            isset ($returnValue["antworten_x"]) or
            isset ($returnValue["weiterleiten_x"])
         )
         ) {*/
$allowedTasks = [
  "Stab_schreiben",
  "Stab_gesprnoti",
  "FM-Ausgang",
  "FM-Ausgang_Sichter",
  "FM-Admin",
  "FM-Eingang",
  "FM-Eingang_Sichter",
  "FM-Eingang_Anhang",
  "FM-Eingang_Anhang_Sichter",
  "Stab_sichten",
  "SI-Admin"
];

if (
  isset($returnValue["task"]) && in_array($returnValue["task"], $allowedTasks) &&
  (
    !isset($returnValue["abbrechen_x"]) ||
    isset($returnValue["antworten_x"]) ||
    isset($returnValue["weiterleiten_x"])
  )
) {

  $returndata = $returnValue;

  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><br> Daten kommen vom Formular und können gespeichert werden";
    echo "<br>\n";
  }

  /* if (($returnValue["11_gesprnotiz"] == "on") and
    (!$_SESSION["gesprnoti"]) and
    ($returnValue["task"] != "SI-Admin") and
    ($returnValue["task"] != "Stab_sichten")
  ) {*/
  if (
    (($returnValue["11_gesprnotiz"] ?? '') == "on") and
    empty($_SESSION["gesprnoti"]) and
    ($returnValue["task"] != "SI-Admin") and
    ($returnValue["task"] != "Stab_sichten")
    ) {

    // Bei Gesprächsnotiz 2. Vorlage beim Verfasser für Sichtung

    if (debug) {
      echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><br> ### Gesprächsnotiz == 2. Sichtung<br>\n";
    }

    $formdata = $returnValue;
    $formdata["01_zeichen"]      = $_SESSION["vStab_kuerzel"];
    $formdata["11_gesprnotiz"]   = "t";
    $formdata["16_empf"]         = $redcopy2 . "_rt," . $_SESSION["vStab_funktion"] . "_gn";
    $formdata["15_quitzeichen"]  = $_SESSION["vStab_kuerzel"];
    $formdata["task"]            = "Stab_gesprnoti";
    $form = new nachrichten4fach($formdata, "Stab_gesprnoti", "");
    $_SESSION["gesprnoti"] = true;
    $gesprnotizsichter = true;
  } else {

    if (debug) {
      echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b> ### 369 check and save";
      echo "<br>returndata = "; var_dump($returndata) ;
      echo "<br>\n";
    }

    check_and_save($returndata);

    // verhindert das erneute Speichern bei Betätigung von F5
    if (isset($_SESSION['gesprnoti'])) {
      unset($_SESSION['gesprnoti']);
    }
    if (create_vordrucke) {
      if (debug) {
        echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><br> ";
      }
      include("../4fbak/backup.php");
    }

    if (!$weiterantwort) {
      resetframeset($conf_urlroot . $conf_web["pre_path"]);
    }
  }
  /*   if (isset($returnValue["task"]) && in_array($returnValue["task"], $allowedTasks)) {
    // ...
}*/
} elseif (isset($returnValue["task"]) &&  ((($returnValue["task"] == "FM-Ausgang_Sichter") ||
  ($returnValue["task"] == "FM-Ausgang")
) &&
  ($returnValue["abbrechen_x"]))) {

  /************************************************************************\
FM-Ausgang (Sichter) abgebrochen
      \************************************************************************/

  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b> and re/set Sperre <br>\n";
  }

  $dbaccess = new db_access($conf_4f_db["server"], $conf_4f_db["datenbank"], $conf_4f_tbl["benutzer"], $conf_4f_db["user"],  $conf_4f_db["password"]);
  $query = "SELECT `x02_sperre`,`x03_sperruser` FROM `" . $conf_4f_tbl["nachrichten"] . "`
                 where `00_lfd` = " . $returnValue["00_lfd"];
  $result = $dbaccess->query_table($query);

  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><br>";
    echo "SQL-Result : result = ";
    var_dump($result);
    echo "<br>\n";
  }

  $row = (isset($result[0]) && is_array($result[0])) ? $result[0] : null;
  if ($row && (($row['x02_sperre'] == "f") or // Ist der Satz schon in Bearbeitung (sperre == FALSE)?
    (($row['x02_sperre'] == "t") and // Satz gesperrt
      ($_SESSION["vStab_kuerzel"] == $row['x03_sperruser']))
  )) { // Du bist der Bearbeiter

    $query = "UPDATE " . $conf_4f_tbl["nachrichten"] . "
                   SET x02_sperre = \"f\", x03_sperruser = \"\" where 00_lfd = " . $returnValue["00_lfd"];
    $result = $dbaccess->query_table_iu($query);

    if (debug) {
      echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><br>";
      echo "SQL-Result : result = ";
      var_dump($result);
      echo "<br>\n";
    }
  }
}

if (debug) {
  echo "<br><br>\n------------------------Info Block Start------------------------------<br>\n";
  echo "<big><big><b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b></big></big><br>";
  echo "returnValue=";
  var_dump($returnValue);
  echo "<br>\n";
  echo "GET=";
  var_dump($_GET);
  echo "<br>\n";
  echo "POST=";
  var_dump($_POST);
  echo "<br>\n";
  echo "COOKIE=";
  var_dump($_COOKIE);
  echo "<br>\n";
  echo "sessiondata=";
  var_dump($sessiondata);
  echo "<br>\n";
  echo "SESSION=";
  var_dump($_SESSION);
  echo "<br>\n------------------------Info Block Ende------------------------------<br>\n";
}
/**********************************************************************\
Daten kommen vom Formular und sollen als Antwort dienen.
\**********************************************************************/

// A N T W O R T
if ((isset($returnValue["antwort_x"])) and ($returnValue["task"] == "Stab_lesen")) {
  //  A N T W O R T  --  "Stab_lesen"

  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b> ### Antwort <br> ";
  }

  $formdata = $returnValue;
  $aushilfe = $formdata["10_anschrift"];
  $formdata["10_anschrift"] =  $formdata["13_abseinheit"] . "  " . $formdata["14_funktion"];
  $formdata["13_abseinheit"] = $aushilfe;
  $formdata["12_abfzeit"] = "";
  $formdata["14_zeichen"]     = $_SESSION["vStab_kuerzel"];
  $formdata["14_funktion"]    = $_SESSION["vStab_funktion"];
  $formdata["12_inhalt"] = "Zitat: von " . $formdata["04_richtung"] . " " . $formdata["04_nummer"] . " \n\"" . $formdata["12_inhalt"] . "\"\n";
  $formdata["04_richtung"] = "";
  $formdata["04_nummer"] = "";
  $form = new nachrichten4fach($formdata, "Stab_schreiben", "");
}

// Weiterleitung
if ((isset($returnValue["weiterleiten_x"])) and
  ($returnValue["task"] == "Stab_lesen") and
  (($returnValue["04_richtung"] == "E") or
    ($returnValue["04_richtung"] == "A"))
) {

  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><br> ### 225 WEITERLEITUNG - Stab_lesen";
    echo "<br>\n";
  }

  $formdata = $returnValue;
  // W E I T E R L E I T U N G  --  "Stab_lesen" ---- "E" Anschrift frei; Absender normal
  $formdata["10_anschrift"] = "";
  $formdata["12_inhalt"] = "Zitat: von " . $formdata["04_richtung"] . " " . $formdata["04_nummer"] . " \n\"" . $formdata["12_inhalt"] . "\"\n";
  $formdata["04_richtung"] = "";
  $formdata["04_nummer"] = "";
  $formdata["11_gesprnotiz"] = "";
  $formdata["13_abseinheit"]  = $conf_4f["anschrift"];
  $formdata["12_abfzeit"] = "";
  $formdata["14_zeichen"]     = $_SESSION["vStab_kuerzel"];
  $formdata["14_funktion"]    = $_SESSION["vStab_funktion"];
  $form = new nachrichten4fach($formdata, "Stab_schreiben", "");
}

if (isset($_SESSION["sw_data"])) {

  $formdata = $_SESSION["sw_data"];
  if ((isset($formdata["antwort_x"])) and
    (($formdata["task"] == "FM-Ausgang") or
      ($formdata["task"] == "FM-Ausgang_Sichter"))
  ) {

    if (debug) {
      echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b> ### antwort_x und FM_Ausgang(_Sichter) <br>\n";
    }
    //  A N T W O R T  --  "FM-Ausgang"  or "FM-Ausgang_Sichter"
    $aushilfe = $formdata["10_anschrift"];
    $formdata["01_zeichen"]  = $_SESSION["vStab_kuerzel"];
    $formdata["10_anschrift"] =  $formdata["13_abseinheit"] . "  " . $formdata["14_funktion"];
    $formdata["13_abseinheit"] = $aushilfe;
    $formdata["12_abfzeit"] = "";
    $formdata["12_inhalt"] = "Zitat: von " . $formdata["04_richtung"] . " " . $formdata["04_nummer"] . " \n\"" . $formdata["12_inhalt"] . "\"\n";
    $formdata["04_richtung"] = "";
    $formdata["04_nummer"] = "";
    $formdata["02_zeit"] = "";
    $formdata["02_zeichen"] = "";
    $formdata["03_datum"] = "";
    $formdata["03_zeichen"] = "";
    unset($_SESSION["sw_data"]);
    if (debug) {
      echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><br>";
    }

    if (sichter_online()) {
      $form = new nachrichten4fach($formdata, "FM-Eingang", "");
    } else {
      if ($conf_4f["si_in_out"] == true) { // Sichter alles oder nur Eingänge
        $formdata["15_quitzeichen"]  = $_SESSION["vStab_kuerzel"];
        $formdata["16_empf"]         = "";
        $form = new nachrichten4fach($formdata, "FM-Eingang_Sichter", "");
      } else {  // Sichter nur Eingänge
        $form = new nachrichten4fach($formdata, "FM-Eingang", "");
      }
    }
  }

  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b>  ### Weiterleitung <br>";
  }

  if ((isset($formdata["weiterleiten_x"])) and (($returnValue["task"] == "FM-Ausgang") or ($returnValue["task"] == "FM-Ausgang_Sichter"))) {

    // W E I T E R L E I T U N G  --  "FM-Ausgang"  or "FM-Ausgang_Sichter"

    if (debug) {
      echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><br> WEITERLEITUNG - FM-Ausgang(_Sichter)";
      echo "<br>\n";
    }

    $aushilfe = $formdata["10_anschrift"];
    $formdata["10_anschrift"] =  $formdata["13_abseinheit"] . "  " . $formdata["14_funktion"];
    $formdata["13_abseinheit"] = $aushilfe;
    $formdata["12_abfzeit"] = "";
    $formdata["12_inhalt"] = "Zitat: von " . $formdata["04_richtung"] . " " . $formdata["04_nummer"] . " \n\n\"" . $formdata["12_inhalt"] . "\"\n\n";
    $formdata["04_richtung"] = "";
    $formdata["04_nummer"] = "";

    unset($_SESSION["sw_data"]);

    if (sichter_online()) {
      $form = new nachrichten4fach($formdata, "FM-Eingang", "");
    } else {
      $formdata["15_quitzeichen"]  = $_SESSION["vStab_kuerzel"];
      $formdata["16_empf"]         = "";
      $form = new nachrichten4fach($formdata, "FM-Eingang_Sichter", "");
    }
  }
} //  if (isset ($_SESSION ["sw_data"] ))


/**********************************************************************
 Voreinstellung fuer das Menue
 ***********************************************************************/
if ((isset($_SESSION["vStab_benutzer"])) and
  (!(isset($returnValue["m2auswahl"])))
) {
  $mode = 2;
}

$formdata = array(""); // setze die Formulardaten zurck

/**********************************************************************\
  --- S T A B  s c h r e i b e n ---
  Hier werden die Angaben:
    Abfasszeit, Absendeeinheit, Zeichen des Verfassers, Funktion
  der Stabsfunktion im Formular voreingestellt.
\**********************************************************************/

if ((isset($returnValue["stab_schreiben_x"])) and !$gesprnotizsichter) {

  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b>   _GET[stab_schreiben_x] )) and !gesprnotizsichter ";
    echo "<br>\n";
  }
  $formdata["13_abseinheit"]  = $conf_4f["anschrift"];
  $formdata["14_zeichen"]     = $_SESSION["vStab_kuerzel"];
  $formdata["14_funktion"]    = $_SESSION["vStab_funktion"];
  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "  formdata:   ";
    var_dump($formdata);
    echo "</b><br>";
  }
  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "  conf_4f :   ";
    var_dump($conf_4f);
    echo "</b><br>";
  }
  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "  SESSION :   ";
    var_dump($_SESSION);
    echo "</b><br>";
  }

  $form = new nachrichten4fach($formdata, "Stab_schreiben", "");
}




/**********************************************************************\
  --- S T A B   l e s e n  ---

  Menue und Liste
\**********************************************************************/
/*isset($a) ? $a : null*/
$sessupl = (isset($_SESSION["UPLOAD"]) ? $_SESSION["UPLOAD"] : null);

$rolle = $_SESSION["vStab_rolle"] ?? ($_SESSION["ROLLE"] ?? null);
$funktion = $_SESSION["vStab_funktion"] ?? null;

$retval_stab = (isset($returnValue["stab"])  ? $returnValue["stab"] : null);

if (debug) {
  echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><br>  Stab_lesen ";
  echo "<br>\n";

  echo "Block I : ";
  var_dump((
    ((isset($_SESSION["vStab_rolle"]) ? $_SESSION["vStab_rolle"]  : null) && ($_SESSION["vStab_rolle"] == "Stab")) ||
    ((isset($_SESSION["ROLLE"])      ? $_SESSION["ROLLE"]        : null) && ($_SESSION["ROLLE"]       == "Stab")) ||
    ((isset($_SESSION["vStab_rolle"]) ? $_SESSION["vStab_rolle"]  : null) && ($_SESSION["vStab_rolle"] == "FB")) ||
    ((isset($_SESSION["ROLLE"])      ? $_SESSION["ROLLE"]        : null) && ($_SESSION["ROLLE"] == "FB"))));
  echo "<br>\n";


  echo "Block II : ";
  var_dump((
    ((isset($_SESSION["vStab_funktion"]) ? $_SESSION["vStab_funktion"] : null) && ($_SESSION["vStab_funktion"] != "Si")) &&
    ((($retval_stab === null) && ($retval_stab !== "meldung")) &&
      (!(isset($returnValue["m2_benutzer_x"]))))));
  echo "Block II a : ";
  var_dump((isset($_SESSION["vStab_funktion"]) ? $_SESSION["vStab_funktion"] : null) && ($_SESSION["vStab_funktion"] != "Si"));
  echo "<br>\n";

  echo "Block II b : ";
  var_dump(($retval_stab === null) && ($retval_stab !== "meldung"));
  echo "<br>\n";
  echo "Block II c : ";
  var_dump(!(isset($returnValue["m2_benutzer_x"])));
  echo "<br>\n";

  echo "Block III : ";
  $sessupl = (isset($_SESSION["UPLOAD"]) ? $_SESSION["UPLOAD"] : null);
  var_dump((
    (!(isset($returnValue["stab_schreiben_x"]))) &&
    (!$gesprnotizsichter) &&
    (!(isset($returnValue["stab_anhang_x"]))) &&
    (!(isset($returnValue["fm_anhang_x"]))) &&
    (!(isset($returnValue["ah_auswahl_x"]))) &&
    (!(isset($returnValue["m2_abmelden_x"]))) &&
    (!(isset($returnValue["antwort_x"]))) &&
    (!(isset($returnValue["weiterleiten_x"]))) &&
    (!(isset($_SESSION["sw_data"]))) &&
    (($sessupl != "fileupload")) &&
    (($sessupl != "upload"))
  ));
  echo "<br>\n";
  echo "Block III a : ";
  var_dump(!(isset($returnValue["stab_schreiben_x"])));
  echo "<br>\n";
  echo "Block III b : ";
  var_dump(!$gesprnotizsichter);
  echo "<br>\n";
  echo "Block III c : ";
  var_dump(!(isset($returnValue["stab_anhang_x"])));
  echo "<br>\n";
  echo "Block III d : ";
  var_dump(!(isset($returnValue["ah_auswahl_x"])));
  echo "<br>\n";
  echo "Block III e : ";
  var_dump(!(isset($returnValue["m2_abmelden_x"])));
  echo "<br>\n";
  echo "Block III f : ";
  var_dump(!(isset($returnValue["antwort_x"])));
  echo "<br>\n";
  echo "Block III g : ";
  var_dump(!(isset($returnValue["weiterleiten_x"])));
  echo "<br>\n";
  echo "Block III h : ";
  var_dump(!(isset($_SESSION["sw_data"])));
  echo "<br>\n";
  echo "Block III i : ";
  var_dump(($sessupl != "fileupload"));
  echo "<br>\n";
  echo "Block III j : ";
  var_dump(($sessupl != "upload"));
  echo "<br>\n";
  
  echo "<br><br>\n------------------------Info Block Start------------------------------<br>\n";
  echo "<big><big><b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b></big></big><br>";
  echo "returnValue=";
  var_dump($returnValue);
  echo "<br>\n";
  echo "GET=";
  var_dump($_GET);
  echo "<br>\n";
  echo "POST=";
  var_dump($_POST);
  echo "<br>\n";
  echo "COOKIE=";
  var_dump($_COOKIE);
  echo "<br>\n";
  echo "sessiondata=";
  var_dump($sessiondata);
  echo "<br>\n";
  echo "SESSION=";
  var_dump($_SESSION);
  echo "<br>\n------------------------Info Block Ende------------------------------<br>\n";
}

if (
  (
    ((isset($_SESSION["vStab_rolle"]) ? $_SESSION["vStab_rolle"]  : null) && ($_SESSION["vStab_rolle"] == "Stab")) ||
    ((isset($_SESSION["ROLLE"])      ? $_SESSION["ROLLE"]        : null) && ($_SESSION["ROLLE"]       == "Stab")) ||
    ((isset($_SESSION["vStab_rolle"]) ? $_SESSION["vStab_rolle"]  : null) && ($_SESSION["vStab_rolle"] == "FB")) ||
    ((isset($_SESSION["ROLLE"])      ? $_SESSION["ROLLE"]        : null) && ($_SESSION["ROLLE"] == "FB")))
  &&
  (
    ((isset($_SESSION["vStab_funktion"]) ? $_SESSION["vStab_funktion"] : null) && ($_SESSION["vStab_funktion"] != "Si")) &&
    (((($retval_stab === null) && ($retval_stab !== "meldung"))) &&
      (!(isset($returnValue["m2_benutzer_x"])))))
  &&
  (
    (!(isset($returnValue["stab_schreiben_x"]))) &&
    (!$gesprnotizsichter) &&
    (!(isset($returnValue["stab_anhang_x"]))) &&
    (!(isset($returnValue["fm_anhang_x"]))) &&
    (!(isset($returnValue["ah_auswahl_x"]))) &&
    (!(isset($returnValue["m2_abmelden_x"]))) &&
    (!(isset($returnValue["antwort_x"]))) &&
    (!(isset($returnValue["weiterleiten_x"]))) &&
    (!(isset($_SESSION["sw_data"]))) &&
    (($sessupl != "fileupload")) &&
    (($sessupl != "upload"))
  )
) {

  /*
if ( ( 
          ( isset ($_SESSION ["vStab_rolle"] ) && ($_SESSION ["vStab_rolle"] == "Stab" )) or
          ( isset ($_SESSION ["ROLLE"] ) && ($_SESSION ["ROLLE"] == "Stab" )) or
          ( isset ($_SESSION ["vStab_rolle"] ) && ($_SESSION ["vStab_rolle"] == "FB" )) or
          ( isset ($_SESSION ["ROLLE"] ) && ($_SESSION ["ROLLE"] == "FB" ))  ) and
         (
          ( (isset ($_SESSION ["vStab_funktion"])) && ( $_SESSION ["vStab_funktion"] != "Si"   )) and
          ( (isset ($returnValue ["stab"])) && ( $returnValue ["stab"] != "meldung")) and
          ( !(isset ($returnValue ["m2_benutzer_x"] ) )) )  and
        (
          ( !(isset ($returnValue ["stab_schreiben_x"] ) ) ) and
          ( !$gesprnotizsichter ) and
          ( !(isset ($returnValue ["stab_anhang_x"] ) ) ) and
          ( !(isset ($returnValue ["fm_anhang_x"] ) ) ) and
          ( !(isset ($returnValue ["ah_auswahl_x"] ) ) ) and
          ( !(isset ($returnValue["m2_abmelden_x"] ) ) ) and
          ( !(isset ($returnValue["antwort_x"] ) ) ) and
          ( !(isset ($returnValue["weiterleiten_x"] ) ) ) and
          ( !(isset ($_SESSION["sw_data"] ) ) ) and
          ( $_SESSION ["UPLOAD"] != "fileupload" ) and
          ( $_SESSION ["UPLOAD"] != "upload" )
         )
         ) {
          
*/

  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><br>  Stab_lesen - Menue und Liste ";
    echo "<br>\n";
  }
  $css = '';
  $css . $csskatego = "html { font-size: 100%; }
                a:link, a:visited, a:active {    text-decoration:    none;    color:              #333399; }
                a:hover { text-decoration:    underline; color:              #cc0000; }
                a:img {   border:             0; }
        /******************************************************************************/
        /* specific elements */
        /* topmenu */
        ul#topmenu { font-weight:bold; list-style-type:none; margin:0; padding:0; }
        ul#topmenu li { float:left; margin:0; padding:0; vertical-align: middle; }
          #topmenu img {vertical-align:middle; margin-right:0.1em; }

        /* default tab styles */
        .tab,
        .tabcaution,
        .tabactive {display: block; margin: 0.2em 0.2em 0 0.2em; padding: 0.2em 0.2em 0 0.2em; white-space: nowrap; }

        /* disabled tabs */
        span.tab {color: #666666; }

        /* disabled drop/empty tabs */
        span.tabcaution { color: #ff6666; }

        /* enabled drop/empty tabs */
        a.tabcaution {color:  #FF0000;  }
        a.tabcaution:hover { color: #FFFFFF; background-color:   #FF0000; }

        #topmenu { margin-top: 0.5em; padding: 0.1em 0.3em 0.1em 0.3em; }

ul#topmenu li {
    border-bottom:      1pt solid black;
}

/* default tab styles */
.tab, .tabcaution, .tabactive {
    background-color:   #E5E5E5;
    border:             1pt solid #D5D5D5;
    border-bottom:      0;
    border-top-left-radius: 0.4em;
    border-top-right-radius: 0.4em;
}

/* enabled hover/active tabs */
a.tab:hover,
a.tabcaution:hover,
.tabactive,
.tabactive:hover {
    margin:             0;
    padding:            0.2em 0.4em 0.2em 0.4em;
    text-decoration:    none;
}

a.tab:hover,
.tabactive {
    background-color:   #ffffff;
}

/* to be able to cancel the bottom border, use <li class=\"active\"> */
ul#topmenu li.active {
     border-bottom:      1pt solid #ffffff;
}";

  $nameversion = implode("", $conf_4f["NameVersion"]);
  pre_html("stabliste", "Stab lesen " . $nameversion, $css . $csskatego . $kats_table_css); // Normaler Seitenaufbau mit Auffrischung
  echo "<body style=\"font-family: 'Liberation Sans';\" bgcolor=\"#DCDCFF\">";

  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><br>  prehtml - Stab_lesen - Menue und Liste ";
    echo "<br>\n";
  }

  if (($_SESSION["vStab_funktion"] ?? "") === "S2") {
    generate_pdfs_for_read_s2_messages();
  }
  $list = new Listen("Stab_lesen", "");

  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><br>  prehtml - Stab_lesen - vor create list ";
    echo "<br>\n";
  }
  // echo "<b><big><big><big>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b></big></big></big><br>  prehtml - createlist ";
  $list->createlist();

  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><br>  prehtml - Stab_lesen nach create list ";
    echo "<br>\n";
  }
}

/**********************************************************************\
  --- S T A B   M e l d u n g   l e s e n ---

  Darstellung der Meldung ber die laufende Nummer
\**********************************************************************/
if (isset($returnValue["stab"]) && ($returnValue["stab"] == "meldung")) {

  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b> Stab Meldung lesen - Darstellung der Meldung über die laufende Nummer ";
    echo "<br>\n";
  }

  set_msg_read($returnValue["00_lfd"]);

  $formdata = get_msg_by_lfd($returnValue["00_lfd"]);

  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "  formdata:   ";
    var_dump($formdata);
    echo "</b><br>";
  }

  $form = new nachrichten4fach($formdata, "Stab_lesen", "");
}


/**********************************************************************\
  --- S i c h t e r   M e l d u n g   s i c h t e n ---

  Darstellung der Meldung ueber die laufende Nummer
\**********************************************************************/
if ((isset($returnValue["sichter"])) && ($returnValue["sichter"] == "meldung")) {

  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><br>";
  }
  $formdata = get_msg_by_lfd($returnValue["00_lfd"]);
  $formdata["15_quitzeichen"]  = $_SESSION["vStab_kuerzel"];
  $form = new nachrichten4fach($formdata, "Stab_sichten", "");
}

/***************************************************************************************************/
// 2. Sichtungsliste zurücksetzen, wenn sichten, FM_Ausgang,  angeklickt wurde
if ((isset($returnValue['stab_sichten_x'])) &&
  (isset($_SESSION['si_zweite_sichtung']) && ($_SESSION['si_zweite_sichtung'] == 1))
) {

  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><br> reset SI 2. sichtung </b><br>";
  }

  $_SESSION["si_zweite_sichtung"] = 0;
}

if (debug) {
  echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><br>";
}

if (((isset($returnValue['fm_ausgang_x'])) &&
  ((isset($_SESSION['fm_zweite_sichtung'])) && ($_SESSION['fm_zweite_sichtung'] == 1)))) {

  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . " reset FM 2. sichtung </b><br>";
  }

  $_SESSION["fm_zweite_sichtung"] = 0;
}

/***************************************************************************************************/


/**********************************************************************\
  --- S i c h t e r   l e s e n  ---

  Menue und Liste

 \**********************************************************************/
/*      	
if ( ( (  (isset($_SESSION ["vStab_rolle"])) && ($_SESSION ["vStab_rolle"] == "Stab") ) or
        ( (isset($_SESSION ["ROLLE"])) && ($_SESSION ["ROLLE"] == "Stab") )
        ) and
        ( $_SESSION ["vStab_funktion"] == "Si" ) and
        ( (isset($returnValue["sichter"])) && !($returnValue["sichter"] == "meldung") ) and
        ( !(isset($returnValue["si_admin_x"])) ) and
        ( !(isset ($returnValue ["m2_abmelden_x"]) ) and
        ( isset($returnValue["fm"]) &&
        ( ($returnValue["fm"] != "SI-Adminmeldung") AND ($returnValue["fm"] != "SI-Adminmeldung") ) ))
		and ($_SESSION ['si_zweite_sichtung'] == 0 )
      )
      {*/

      /*
$rolle = (
  (isset($_SESSION["vStab_rolle"]) && $_SESSION["vStab_rolle"] == "Stab")
  ||
  (isset($_SESSION["ROLLE"]) && $_SESSION["ROLLE"] == "Stab"));

$fm = $returnValue["fm"] ?? null;
/*
( ($returnValue["fm"] != "SI-Adminmeldung") AND ($returnValue["fm"] != "SI-Adminmeldung") ) ))
		and ($_SESSION ['si_zweite_sichtung'] == 0 )
*/
/*
$logikblock = $rolle === "Stab" &&
  ($_SESSION["vStab_funktion"] ?? null) === "Si" &&
  (isset($returnValue["sichter"]) && $returnValue["sichter"] !== "meldung") &&

  !isset($returnValue["si_admin_x"]) &&
  !isset($returnValue["m2_abmelden_x"]) &&
  isset($fm) &&
  $fm !== "SI-Adminmeldung" &&
  ($_SESSION["si_zweite_sichtung"] ?? null) === 0;

if (debug) {
  echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . " logikblock = </b><br>";
  var_dump($logikblock);
  echo "rolle = <br>\n";
  var_dump($rolle);
  echo "fm    = <br>\n";
  var_dump($fm);
}

if ($logikblock) { *******************************************/

if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "VOR Sichter lesen - Menue und liste </b><br>\n";
    
    
    echo "LOGIK :"; var_dump((
    (
        (isset($_SESSION["vStab_rolle"]) && $_SESSION["vStab_rolle"] === "Stab") ||
        (isset($_SESSION["ROLLE"]) && $_SESSION["ROLLE"] === "Stab")
    ) &&
    (isset($_SESSION["vStab_funktion"]) && $_SESSION["vStab_funktion"] === "Si") &&

    (( !isset($returnValue["sichter"] ) ) || (isset($returnValue["sichter"]) && $returnValue["sichter"] !== "meldung")) &&
    
    !isset($returnValue["si_admin_x"]) &&
    !isset($returnValue["m2_abmelden_x"]) &&
    ((( !isset($returnValue["fm"] ) )) || (isset($returnValue["fm"]) && ($returnValue["fm"] !== "SI-Adminmeldung")))
    &&
    ((( !isset($_SESSION['si_zweite_sichtung'] ) )) || (isset($_SESSION['si_zweite_sichtung']) && $_SESSION['si_zweite_sichtung'] === 0))
    ));


    echo "Rolle ="; 
    var_dump((
        (isset($_SESSION["vStab_rolle"]) && $_SESSION["vStab_rolle"] === "Stab") ||
        (isset($_SESSION["ROLLE"]) && $_SESSION["ROLLE"] === "Stab")
    ) );

    echo "Si ="; 
    var_dump((isset($_SESSION["vStab_funktion"]) && $_SESSION["vStab_funktion"] === "Si"));

    echo "meldung ="; 
    var_dump(( !isset($returnValue["sichter"] ) ) || (isset($returnValue["sichter"]) && $returnValue["sichter"] !== "meldung"));

    echo "si_zweite_sichtung===0 ="; 
    var_dump( ((( !isset($_SESSION['si_zweite_sichtung'] ) )) || (isset($_SESSION['si_zweite_sichtung']) && $_SESSION['si_zweite_sichtung'] === 0)));
}

if (
    (
        (isset($_SESSION["vStab_rolle"]) && $_SESSION["vStab_rolle"] === "Stab") ||
        (isset($_SESSION["ROLLE"]) && $_SESSION["ROLLE"] === "Stab")
    ) &&
    (isset($_SESSION["vStab_funktion"]) && $_SESSION["vStab_funktion"] === "Si") &&

    (( !isset($returnValue["sichter"] ) ) || (isset($returnValue["sichter"]) && $returnValue["sichter"] !== "meldung")) &&
    
    !isset($returnValue["si_admin_x"]) &&
    !isset($returnValue["m2_abmelden_x"]) &&
    ((( !isset($returnValue["fm"] ) )) || (isset($returnValue["fm"]) && ($returnValue["fm"] !== "SI-Adminmeldung")))
    &&
    ((( !isset($_SESSION['si_zweite_sichtung'] ) )) || (isset($_SESSION['si_zweite_sichtung']) && $_SESSION['si_zweite_sichtung'] === 0))
    &&
    !isset($returnValue["m2_benutzer_x"])
    ) {
    
  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . " Sichter lesen - Menue und liste </b><br>\n";
  }

  $css = "a:link { color:#000000; text-decoration:none; font-weight:normal; }\n" .
    "a:visited { color:#EE0000; text-decoration:none; font-weight:normal; }\n" .
    "a:hover { color:#EE0000; text-decoration:none; background-color:#FFFF99; font-weight:normal; }\n" .
    "a:active { color:#0000EE; background-color:#FFFF99; }\n" .
    "a:focus { color:#0000EE; background-color:#FFFF99;  }";
  $nameversion = implode("", $conf_4f["NameVersion"]);
  pre_html("siliste", "Sichterliste " . $nameversion, $css . $kats_table_css); // Normaler Seitenaufbau mit Auffrischung
  echo "<body style=\"font-family: 'Liberation Sans';\" bgcolor=\"#DCDCFF\">";
  $list = new listen("Stab_sichten", "STSI");
  $list->createlist();
}



/**********************************************************************\
  --- F e r n m e l d e r   E i n g a n g  ---
      Es wurde der "Eingang"-Button beim Fernmelder betätigt.
\**********************************************************************/
if (isset($returnValue["fm_eingang_x"])) {
  if (!empty($_SESSION["fm_zweite_sichtung"]) && $_SESSION["fm_zweite_sichtung"] == 1) {
    $_SESSION["fm_zweite_sichtung"] = 0;
  }

  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "  Fernmelder Eingang  </b><br>\n";
  }

  $formdata = array(
    '01_zeichen' => isset($_SESSION['vStab_kuerzel']) ? $_SESSION['vStab_kuerzel'] : null
  );

  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "  Zuweisung 01_zeichen  </b><br>\n";
    echo "formdata: ";
    var_dump($formdata);
    echo "<br>";
  }

  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "  Zuweisung 01_zeichen  </b><br>\n";
    echo "SESSION: ";
    var_dump($_SESSION);
    echo "<br>";
  }

  $formdata += array('10_anschrift' => $conf_4f['anschrift']);
  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "  Zuweisung10_anschrift  </b><br>\n";
    echo "formdata: ";
    var_dump($formdata);
    echo "<br>";
  }
  if (sichter_online()) {
    if (debug) {
      echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "  Sichter online?  </b><br>\n";
    }
    $form = new nachrichten4fach($formdata, "FM-Eingang", "");
  } else {
    $formdata["15_quitzeichen"]  = $_SESSION["vStab_kuerzel"];
    $formdata["16_empf"]         = get_autosichter_targets("");
    if (debug) {
      echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . " Vor Aufruf nachrichten4fach </b><br>\n";
      echo "formdata: ";
      var_dump($formdata);
      echo "<br>";
    }

    $form = new nachrichten4fach($formdata, "FM-Eingang_Sichter", "");
    if (debug) {
      echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "  Sichter nicht online? danach  </b><br>\n";
      var_dump($formdata);
      echo "<br>";
    }
  }
  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "  NACH Fernmelder Eingang  </b><br>\n";
  }
}



/**********************************************************************\
  --- F M   A u s g a n g  ---

  Menue und Liste
\**********************************************************************/

$rolleFernmelder = (
  (isset($_SESSION["vStab_rolle"]) && $_SESSION["vStab_rolle"] == "Fernmelder") ||
  (isset($_SESSION["ROLLE"]) && $_SESSION["ROLLE"] == "Fernmelder")
);

$funktionAW = (
  isset($_SESSION["vStab_funktion"]) && ($_SESSION["vStab_funktion"] == "A/W")
);

$keineUploads = !(
  isset($returnValue["fm_anhang_x"]) ||
  isset($returnValue["ah_upload_x"]) ||
  isset($returnValue["ah_auswahl_x"]) ||
  (isset($_SESSION["fm_zweite_sichtung"]) && $_SESSION["fm_zweite_sichtung"] == 1)
);

$keineAnderenAktionen = !(
  isset($returnValue["m2_abmelden_x"]) ||
  isset($returnValue["fm_eingang_x"]) ||
  isset($returnValue["fm_admin_x"]) ||
  isset($returnValue["etb_eintrag_x"]) ||
  isset($returnValue["antwort_x"])
);

$keinSWData = !isset($_SESSION["sw_data"]);

$keineAdminmeldung = (!isset($returnValue["fm"]) ||
  ((isset($returnValue["fm"]) &&  $returnValue["fm"] != "FM-Adminmeldung") &&
    (isset($returnValue["fm"]) &&  $returnValue["fm"] != "SI-Adminmeldung") &&
    (isset($returnValue["fm"]) &&  $returnValue["fm"] != "meldung")
  ));

if (debug) {
  echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b>  ### Logikwerte ### <br>\n";
  echo "rolleFernmelder = ";
  var_dump($rolleFernmelder);
  echo "funktionAW = ";
  var_dump($funktionAW);
  echo "keineUploads = ";
  var_dump($keineUploads);
  echo "keineAnderenAktionen = ";
  var_dump($keineAnderenAktionen);
  echo "keineAdminmeldung = ";
  var_dump($keineAdminmeldung);
  echo "keinSWData = ";
  var_dump($keinSWData);
}

if (
  $rolleFernmelder &&
  $funktionAW &&
  $keineUploads &&
  $keineAnderenAktionen &&
  $keinSWData &&
  $keineAdminmeldung &&
  !isset($returnValue["m2_benutzer_x"])
) {


  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b>  ### FM Ausgang - Menue und Liste <br>\n";
  }

  $css = "a:link { color:#000000; text-decoration:none; font-weight:normal; }\n" .
    "a:visited { color:#EE0000; text-decoration:none; font-weight:normal; }\n" .
    "a:hover { color:#EE0000; text-decoration:none; background-color:#FFFF99; font-weight:normal; }\n" .
    "a:active { color:#0000EE; background-color:#FFFF99; }\n" .
    "a:focus { color:#0000EE; background-color:#FFFF99;  }";
  pre_html("fmdliste", "FMD Ausgang " . $conf_4f["Titelkurz"] . "" . $conf_4f["Version"], $css . $kats_table_css); // Normaler Seitenaufbau mit Auffrischung
  echo "<body style=\"font-family: 'Liberation Sans';\" bgcolor=\"#DCDCFF\">";
  $list = new listen("FMA", "");
  $list->createlist();
}


/**********************************************************************\
  FM-Ausgang-Sichter
\**********************************************************************/

if (debug) {
  echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b>  ### FM Ausgang - Sichter<br>\n";
}

if ((isset($returnValue["fm"])) && ($returnValue["fm"] == "meldung")) {

  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b>   ### FM - Ausgang  ";
    echo "<br>\n";
  }

  $dbaccess = new db_access($conf_4f_db["server"], $conf_4f_db["datenbank"], $conf_4f_tbl["benutzer"], $conf_4f_db["user"],  $conf_4f_db["password"]);
  $query = "SELECT `x02_sperre`,`x03_sperruser` FROM `" . $conf_4f_tbl["nachrichten"] . "` where 00_lfd = " . $returnValue["00_lfd"];
  $result = $dbaccess->query_table($query);

  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><br>";
    echo "SQL-Result : result = ";
    var_dump($result);
    echo "<br>\n";
  }

  if ((($result[0]['x02_sperre'] == "f") or
      ($result[0]['x02_sperre'] == "0")) or // Ist der Satz schon in Bearbeitung (sperre == FALSE)?
    (($result[0]['x02_sperre'] == "t") and // Satz gesperrt
      ($_SESSION["vStab_kuerzel"] == $result[0]['x03_sperruser']))
  ) { // Du bist der Bearbeiter

    // Setze den Eintrag auf " G E S P E R R T "
    $query = "UPDATE " . $conf_4f_tbl["nachrichten"] . " SET x02_sperre = \"t\", x03_sperruser = \"" . $_SESSION["vStab_kuerzel"] . "\" where 00_lfd = " . $returnValue["00_lfd"];
    $result = $dbaccess->query_table_iu($query);

    if (debug) {
      echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><br>";
      echo "SQL-Result : result = ";
      var_dump($result);
      echo "<br>\n";
    }

    // Jetzt holen wir uns den kompletten, gesperrten Eintrag
    $formdata = get_msg_by_lfd($returnValue["00_lfd"]);
    // Voreinstellungen fuer den Befoerderungsvermerk
    $formdata["03_zeichen"]  = $_SESSION["vStab_kuerzel"];

    if (debug) {
      echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b>  ### <BR>";
      var_dump($conf_4f);
      echo " <br>\n";
    }

    if (sichter_online()) {
      $form = new nachrichten4fach($formdata, "FM-Ausgang", "");
    } else {  // Sichter nicht online
      if ($conf_4f["si_in_out"] == true) { // Sichter alles oder nur Eingänge
        $formdata["15_quitzeichen"]  = $_SESSION["vStab_kuerzel"];
        $formdata["16_empf"]  .= "," . get_autosichter_targets($formdata["14_funktion"]);
        if (debug) {
          echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><br>";
        }
        $form = new nachrichten4fach($formdata, "FM-Ausgang_Sichter", "");
      } else {  // Sichter nur Eingänge
        $form = new nachrichten4fach($formdata, "FM-Ausgang", "");
      }
    }
  } else {
    $sperruser = $result[0]['x03_sperruser'] ?? "";
    if ($_SESSION["vStab_kuerzel"] != $sperruser) {
      // Kruezel sind gleich
      echo "<big><big><big>Datensatz ist im Zugriff von <b>" . $sperruser . "!</b><br></big></big></big>";
      echo "<br><br><br><br><br><br>";
      echo "!!!Achtung!!!<br>";
      echo "Datensatzfreischaltung nur auf Anordnung des Betriebsstellenleiters.<br>";
      echo "<a href=\"./mainindex.php?reset_record=" . $returnValue["00_lfd"] . "\">
             <img src=\"./createbutton.php?icontext=Datensatz freigeben&color=red\" alt=\"Datensatz freigeben\"></a>";
    }
  }
}  //  if ( $returnValue["fm"] == "meldung"


/**********************************************************************\
   2. SICHTUNG

\**********************************************************************/
/*
  if ( (isset ( $returnValue["fm_admin_x"] ) || 
       ( (isset($_SESSION ["fm_zweite_sichtung"])) && ($_SESSION ["fm_zweite_sichtung"] == 1 ) ) 
       ) && (
         (isset($returnValue ["fm"])) && ($returnValue ["fm"] != "SI-Adminmeldung" )
       ) 
) {}*/

if (debug) {
  echo "<br><br>\n------------------------Info Block Start------------------------------<br>\n";
  echo "<big><big><b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b></big></big><br>";
  echo "returnValue=";
  var_dump($returnValue);
  echo "<br>\n";
  echo "GET=";
  var_dump($_GET);
  echo "<br>\n";
  echo "POST=";
  var_dump($_POST);
  echo "<br>\n";
  echo "COOKIE=";
  var_dump($_COOKIE);
  echo "<br>\n";
  echo "sessiondata=";
  var_dump($sessiondata);
  echo "<br>\n";
  echo "SESSION=";
  var_dump($_SESSION);
  echo "<br>\n------------------------Info Block Ende------------------------------<br>\n";
}
/************************************ für den Fernmelder ************************************************************/
$hasAdminX = isset($returnValue["fm_admin_x"]);
$secondReview = ($_SESSION["fm_zweite_sichtung"] ?? null) == 1;
//$isFmSet = isset($returnValue["fm"]);
$isNotSiAdminmeldung = ($returnValue["fm"] ?? null) != "SI-Adminmeldung";

if (debug) {
  echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b>  ### Logikwerte ### <br>\n";
  echo "hasAdminX = ";
  var_dump($hasAdminX);
  echo "secondReview = ";
  var_dump($secondReview);
  //echo "isFmSet = " ; var_dump ($isFmSet) ;
  echo "isNotSiAdminmeldung = ";
  var_dump($isNotSiAdminmeldung);
}

if (($hasAdminX || $secondReview) && $isNotSiAdminmeldung && !isset($returnValue["m2_benutzer_x"])) {
  //    if (($hasAdminX || $secondReview) && $isFmSet && $isNotSiAdminmeldung) {


  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b>   ###  FM Admin <br>";
  }

  $_SESSION["fm_zweite_sichtung"] = 1;
  $css = "a:link { color:#000000; text-decoration:none; font-weight:normal; }\n" .
    "a:visited { color:#000000; text-decoration:none; font-weight:normal; }\n" .
    "a:hover { color:#EE0000; text-decoration:none; background-color:#FFFF99; font-weight:normal; }\n" .
    "a:active { color:#0000EE; background-color:#FFFF99; font-weight:normal; }\n" .
    "a:focus { color:#0000EE; background-color:#FFFF99; font-weight:normal; }";
  $nameversion = implode("", $conf_4f["NameVersion"]);
  pre_html("si2liste", "Stab lesen " . $nameversion, $css . $kats_table_css); // Normaler Seitenaufbau mit Auffrischung
  echo "<body style=\"font-family: 'Liberation Sans';\" bgcolor=\"#DCDCFF\"\n><!-- 2. Sichtung -->\n";
  $list = new listen("FMADMIN", "");
  $list->createlist();
}


/************************************ für den Stab Sichter ************************************************************/
  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b>   ###  Sichter Admin ";
    echo ""; var_dump((
    (isset($returnValue['si_admin_x'])) ||
(
    (
       (  /*(( !isset($_SESSION["si_zweite_sichtung"] ) )) ||*/ (isset($_SESSION['si_zweite_sichtung'])) && ($_SESSION['si_zweite_sichtung'] == 1))
  
    ) && (

    ( (( !isset($returnValue["fm"] ) )) || (isset($returnValue["fm"])) && ($returnValue["fm"] != "SI-Adminmeldung"))
    )
  )
));
    echo "<br>\n";
  }

//$zweitesichtung = (  /* (( !empty($returnValue["si_zweite_sichtung"] ) )) ||*/ (isset($_SESSION['si_zweite_sichtung'])) && ($_SESSION['si_zweite_sichtung'] == 1))


if ((
    (isset($returnValue['si_admin_x'])) ||
(
    (
       (  /*(( !isset($_SESSION["si_zweite_sichtung"] ) )) ||*/ (isset($_SESSION['si_zweite_sichtung'])) && ($_SESSION['si_zweite_sichtung'] == 1))
  
    ) && (

    ( /*(( !isset($returnValue["fm"] ) )) ||*/ (isset($returnValue["fm"])) && ($returnValue["fm"] != "SI-Adminmeldung"))
    )
  )
) && !isset($returnValue["m2_benutzer_x"])) {

  $_SESSION["si_zweite_sichtung"] = 1;
  $css = "a:link { color:#000000; text-decoration:none; font-weight:normal; }\n" .
    "a:visited { color:#000000; text-decoration:none; font-weight:normal; }\n" .
    "a:hover { color:#EE0000; text-decoration:none; background-color:#FFFF99; font-weight:normal; }\n" .
    "a:active { color:#0000EE; background-color:#FFFF99; font-weight:normal; }\n" .
    "a:focus { color:#0000EE; background-color:#FFFF99; font-weight:normal; }";
  $nameversion = implode("", $conf_4f["NameVersion"]);
  pre_html("si2liste", "Stab lesen " . $nameversion, $css . $kats_table_css); // Normaler Seitenaufbau mit Auffrischung
  echo "<body style=\"font-family: 'Liberation Sans';\" bgcolor=\"#DCDCFF\">";
  $list = new listen("SIADMIN", "");
  $list->createlist();
}

/**********************************************************************\
Nachricht als Sichtung anzeigen

\**********************************************************************/
if ((
    (isset($returnValue["fm"])) && ($returnValue["fm"] == "FM-Adminmeldung")
  ) or (
    (isset($returnValue["fm"])) && ($returnValue["fm"] == "SI-Adminmeldung")
  )
) {

  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b>   ###  FM & Si Adminmeldung <br>\n";
  }

  $formdata = get_msg_by_lfd($returnValue["00_lfd"]);
  if (isset($returnValue["fm"])) {
    $fm = $returnValue["fm"];
  }
  if (isset($returnValue["fm"])) {
    $fm = $returnValue["fm"];
  }
  switch ($fm) {
    case "FM-Adminmeldung":
      $form = new nachrichten4fach($formdata, "FM-Admin", "");
      break;

    case "SI-Adminmeldung":
      $form = new nachrichten4fach($formdata, "SI-Admin", "");
      break;
  }
}


/**********************************************************************\
   A B M E L D E N
\**********************************************************************/
if (isset($returnValue["m2_abmelden_x"])) {
  if (debug == true) {
    echo "### 907 m2_abmelden_x ";
    echo "<br>\n";
  }
  include_once("./logoff.php");
  if (debug) {
    echo "<br><br>\n------------------------Info Block Start------------------------------<br>\n";
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><br>";
    echo "returnValue=";
    var_dump($returnValue);
    echo "<br>\n";
    echo "GET=";
    var_dump($_GET);
    echo "<br>\n";
    echo "POST=";
    var_dump($_POST);
    echo "<br>\n";
    echo "COOKIE=";
    var_dump($_COOKIE);
    echo "<br>\n";
    echo "sessiondata=";
    var_dump($sessiondata);
    echo "<br>\n";
    echo "SESSION=";
    var_dump($_SESSION);
    echo "<br>\n------------------------Info Block Ende------------------------------<br>\n";
  }

  $dbaccess = new db_access($conf_4f_db["server"], $conf_4f_db["datenbank"], $conf_4f_tbl["benutzer"], $conf_4f_db["user"],  $conf_4f_db["password"]);
  $query = "UPDATE " . $conf_4f_tbl["benutzer"] . " SET
                   `aktiv` = \"0\",
                   `sid`   = \"\",
                   `ip`    = \"\"
               WHERE `kuerzel` = \"" . $_SESSION["vStab_kuerzel"] . "\";";
  $result = $dbaccess->query_table_iu($query);

  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><br>";
    echo "SQL-Result : result = ";
    var_dump($result);
    echo "<br>\n";
  }

  protokolleintrag("Abmelden", $_SESSION["vStab_benutzer"] . ";" . $_SESSION["vStab_kuerzel"] . ";" . $_SESSION["vStab_funktion"] . ";" . $_SESSION["vStab_rolle"] . ";" . session_id() . ";" . $_SERVER["REMOTE_ADDR"]);

  // Session beenden - SESSION zurcksetzen -
  $_SESSION = array();
  if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/');
  }
  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b>abmelden -- session_destroy<br>";
  }
  session_destroy();

  $_SESSION["menue"] = "WELCOME";
  resetframeset($conf_urlroot . $conf_web["pre_path"]);
} // isset ($returnValue["m2_abmelden_x"]


/**********************************************************************\
  Angemeldete Sitzung: Nachrichtenliste zeigen, nicht zusätzlich
  das Anmeldeformular darunter.
  \**********************************************************************/
if (
  !empty($_SESSION["vStab_kuerzel"]) &&
  !empty($_SESSION["vStab_rolle"]) &&
  in_array(($_SESSION["menue"] ?? ""), ["LOGIN", "WELCOME"], true)
) {
  $_SESSION["menue"] = "ROLLE";
}

/**********************************************************************\

  \**********************************************************************/
if (in_array($_SESSION["menue"], ["LOGIN", "WELCOME"])) {

  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b>LOGIN od. WELCOME<br>";
  }

  echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n";
  echo "<html>\n";
  echo "<head>";
  echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\" />\n";
  echo "<style type=\"text/css\">" . $kats_table_css . "</style>\n";
  echo "</head>\n";

  echo "<body style=\"font-family: 'Liberation Sans';\" bgcolor=\"#DCDCFF\">";
  echo "<form action=\"" . $conf_4f["MainURL"] . "\" method=\"GET\" target=\"mainframe\">\n";
  echo "<!-- Formularelemente und andere Elemente innerhalb des Formulars -->\n";
  echo "<table border=\"1\" cellspacing=\"1\" cellpeding=\"1\">\n";
  echo "<tbody>";
  echo "<tr>\n";
  echo "<td>\n";

  echo "<table border=\"1\" cellspacing=\"1\" cellpeding=\"1\">\n";
  echo "<tbody>";
  echo "<tr>\n";
  switch ($_SESSION["menue"]) {
    case "WELCOME": // nicht angemeldet ==> nur login Button
      if (debug) {
        echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b>nicht angemeldet ==> nur login Button<br>";
      }
      echo "<td>\n";
      foreach ($conf_4f['NameVersion'] as $titel) {
        echo $titel;
      }
      echo "</td>\n";
      echo "</tr>\n<tr>\n";
      break;
    case "LOGIN": // Anmeldeformular
      if (debug) {
        echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b>Anmeldeformular<br>";
      }

      $password_wrong = isset($login_hinweis) && str_contains($login_hinweis, "Passwort falsch");
      if (!empty($login_hinweis) && !$password_wrong) {
        echo "<td colspan=\"2\"><big><b>" . $login_hinweis . "</b></big></td>\n";
        echo "</tr>\n<tr>\n";
      }
      echo "<td>\nName, Vorname:</td>\n<td>\n<input style=\"font-size:20px; font-weight:900;\" type=\"text\" size=\"32\" value=\"" . $menuename . "\" maxlength=\"32\" name=\"benutzer\"></td>\n";
      echo "</tr>\n<tr>\n";
      echo "<td>\nKürzel:</td>\n<td>\n<input style=\"font-size:20px; font-weight:900;\" type=\"text\" size=\"3\" maxlength=\"3\" value=\"" . $menuekuerzel . "\" name=\"kuerzel\"></td>\n";
      echo "</tr>\n<tr>\n";
      echo "<td>\nFunktion:</td>\n<td>\n<select style=\"font-size:20px; font-weight:900;\" name=\"funktion\">\n";
      for ($i = 1; $i <= count($conf_empf); $i++) {
        if ($menuefunktion == $conf_empf[$i]["fkt"]) {
          $sel = "selected";
        } else {
          $sel = "";
        }
        echo "<option " . $sel . ">" . $conf_empf[$i]["fkt"] . "</option>\n";
      }
      echo "</select>\n";
      echo "</td>\n";
      echo "</tr>\n<tr>\n";

      echo "<td>Kennwort:</td>";
      echo "<td><input name=\"kennwort1\" type=\"password\" size=\"32\" maxlength=\"32\"></td>\n";
      echo "</tr>\n";
      if ($doppelkennwort) {
        echo "<input type=\"hidden\" name=\"2teskennwort\" value=\"Yes\">\n";
        echo "<tr>\n";
        echo "<td>Kennwort:</td>";
        echo "<td><input name=\"kennwort2\" type=\"password\" size=\"32\" maxlength=\"32\"></td>\n";
        echo "</tr>\n";
      }
      echo "<tr>";
      echo "<td align=\"center\" bgcolor=$color_button_ok><input type=\"image\" name=\"absenden\" src=\"" . $conf_design_path . "/ok.gif\"></td>";
      echo "<td style=\"vertical-align:middle;\">\n";
      if (!empty($password_wrong)) {
        echo "<span style=\"color:red;\">" . $login_hinweis . "</span>\n";
      } else {
        echo "<a><img src=\"null.gif\" alt=\"leer\"></a>\n";
      }
      echo "</td>\n";
      echo "</tr>\n";

      break;
  }
  echo "</tr>\n";
  echo "</tbody>";
  echo "</table>";
  echo "</td>\n";
  echo "</tr>\n";
  echo "</tbody>";
  echo "</table>";
  echo "</form>";
}


/**********************************************************************\

\**********************************************************************/
if ((isset($returnValue["m2_benutzer_x"])) or
  ($_SESSION["menue"] == "WELCOME") or
  ($_SESSION["menue"] == "LOGIN")
) {
  if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b>m2_benutzer_x od. WELCOME od. LOGIN<br>";
    echo "<br><br>\n  session id ===" . session_id() . "<br><br>\n";
    echo "session===";
    var_dump($_SESSION);
  }
  if (isset($returnValue["m2_benutzer_x"]) && !in_array(($_SESSION["menue"] ?? ""), ["LOGIN", "WELCOME"], true)) {
    echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n";
    echo "<html>\n<head><meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\" />\n";
    echo "<title>Benutzerliste</title>\n";
    echo "<style type=\"text/css\">" . $kats_table_css . "</style>\n</head>\n";
    echo "<body style=\"font-family: 'Liberation Sans';\" bgcolor=\"#DCDCFF\">\n";
  }
  benutzerstatus("verlinkt");
}

if (debug) {
  echo "<br><br>\n";
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

?>
</body>

</html>