<?php
/*****************************************************************************\
   Datei: config.inc.php

   benoetigte Dateien:

   Beschreibung:
     Zentrale Konfigurationsdatei

   (C) 2006-2019 Hajo Landmesser
   mailto://hajo.landmesser@oberbruch.de
\*****************************************************************************/

/******************************************************************************\
   Allgemeine Einstellunge

\******************************************************************************/

    if (!defined("debug")) define("debug", false);

    if (file_exists ( "d_cfg.inc.php" )) REQUIRE "d_cfg.inc.php" ; // Verzeichnis
    if (file_exists ( "m_cfg.inc.php" )) REQUIRE "m_cfg.inc.php" ;
    if (file_exists ( "./4fcfg/d_cfg.inc.php" )) REQUIRE "./4fcfg/d_cfg.inc.php" ;
    if (file_exists ( "../4fcfg/d_cfg.inc.php" )) REQUIRE "../4fcfg/d_cfg.inc.php" ;

    if (debug) {
       echo "<br><b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><big><big>nach session_start</big></big><br><br>";
       echo "SERVER = " ; var_dump ($_SERVER);echo "<br><br>";
    }
    /*
      Sollen Klänge übermittelt und abgespielt werden können ?
        true  : Klänge werden als objekt übertragen und können abgespielt werden.
        false : es werden keine Klänge übermittelt;
    */
    $conf_4f["sounds"] = true ;
    /*  Stabsfunktionen als Array */
    $stabsfunktionen = ["S1","S2","S3","S4","S5","S6"] ;

	/* $conf_4f["si_in_out"]
			true  == Ein- und Ausgänge laufen über den Sichter
		  false == nur Eingänge gehen über den Sichter
	*/
	 $conf_4f["si_in_out"] = false ;

   /* 
      getrennt  : Eingang und Ausgang zählen für sich
      gemeinsam : Eingang und Ausgang zählen zusammen
    */

      if (defined("Nachweisung") == false ) define ("Nachweisung", "gemeinsam");

    /*
    */
    if (defined("posttakzeit") == false ) define ("posttakzeit","true");

    // URL des Servers
    // $conf_urlroot  = "https://".$_SERVER ["SERVER_NAME"].":".$_SERVER ["SERVER_PORT"]."/";
    // $protocol = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on") ? "https" : "http";
    $protocol = $_SERVER["HTTP_X_FORWARDED_PROTO"];

    if (isset($_SERVER['SERVER_PORT'])) {
      $port = $_SERVER["SERVER_PORT"] ;
    } else {
      $port = "";
    }

      // Standardports nicht anhängen
    if (($protocol === "http" && $port == 80) || ($protocol === "https" && $port == 443)) {
      $port = "";
    } else {
      $port = ":" . $port;
    }
    
    if (debug) {
       echo "<br><b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><big><big>Protokoll</big></big><br><br>";
       echo "Protokoll = " ; var_dump ($protocol);echo "<br><br>";
    }
    
    $conf_urlroot = $protocol."://". $_SERVER["SERVER_NAME"]/*.$port*/."/";


      // Wurzelverzeichnis für den Webserver
      // unter Linux /srv/www/htdocs
      // unter Windows C:\Progra~1\Apache\htdocs
      // u.U. muss mit dir /X der Kurzname 8.3 ermittelt und eingetragen werden
    $conf_web ["srvroot"]  = $_SERVER ["DOCUMENT_ROOT"];

      // URL bis zum Kats-Verzeichnis
      // z.B. http://localhost/Einsatzleitung
      // http://192.168.100.1/Einsatzlt
      // http://www.leitstelle.de/ELStab
      // u.s.w. */
    $pre_url = $conf_urlroot.$conf_web["pre_path"] ;

      // Design fuer Buttons
    $conf_design           = "HS";


    $conf_menue ["symbole"] = $conf_urlroot.$conf_web["pre_path"]."4fsym/";

/*******************   nicht aendern ; do not change !!! ***********************/

    $conf_design_path      = $conf_urlroot. // $conf_web ["srvroot"]. // $conf_urlroot.
                             $conf_web ["pre_path"].
                               "4fach/design/".$conf_design ;

//echo "<big>".$conf_design_path."</big>";

    $conf_design_URI       = $pre_url.
                               $conf_web ["pre_path"].
                               "4fach/design/".$conf_design;

/********** Die nachfolgenden Zeilen duerfen nicht geaendert werden !!!  *********/
/**********              Do not change the following lines               **********/
    $conf_4f ["Titelkurz"]        =  "eStab";
    $conf_4f ["SubTitel"]["env"]  =  " - elektronischer Nachrichtenvordruck";
    $conf_4f ["SubTitel"]["etb"]  =  "Einsatztagebuch";
    $conf_4f ["Version"]          =  "v1.0  .07.2025";
      // Hier kann die eigene Dienststelle eingetragen werden Zeilenumbruch mit <br>
    $conf_4f ["Stelle"]           =  "Führungshilfsmittel" ;
      // Programm information und Versionsnummer
    $conf_4f ["NameVersion"][0]   = "<big><big><b>".$conf_4f ["Titelkurz"]." ".
    $conf_4f ["SubTitel"]["env"]."</b><br>".
    $conf_4f ["Version"]."</big></big><br><br><b><big><big><big>".
    $conf_4f ["Stelle"]."</big></big></big></b><br><br>\n";
    $conf_4f ["NameVersion"][1]   = "<b></b><br>\n";
    $conf_4f ["NameVersion"][2]  = "(C) 2005-2025 eStab-Team (<a href=mailto:info@eStab.de>info@eStab.de</a>)<br>\n";
    $conf_4f ["NameVersion"][3]  = "Für php8 angepasst. <br>\n";
    $conf_4f ["NameVersion"][4]  = "Infos unter  <a href=https://www.eStab.de target=\"_blank\">https://www.eStab.de</a><br>\n";
    $conf_4f ["NameVersion"][5]  = "<a href=https://sourceforge.net/projects/estab/forums target=\"_blank\">Forum</a>, <a href=https://eStab.sourceforge.net target=\"_blank\">Entwicklung</a> & <a href=https://sourceforge.net/projects/estab/files/ target=\"_blank\">Sourcen</a> unter: <a href=https://eStab.sourceforge.net target=\"_blank\">https://eStab.sourceforge.net</a><br>\n";

/*******************************************************************************/
      // Datenverzeichnis
    $conf_4f ["data"]     = "4fdata";

    $conf_4f ["anhang"]   = "/anhang";
    $conf_4f ["vordruck"] = "/vordruck";

/*******************************************************************************/

    $conf_4f ["MainURL"]    = $conf_urlroot.$conf_web ["pre_path"]."4fach/mainindex.php";

//  include "dbcfg.inc.php"; // wegen des Datenbanknamens  $conf_4f_db ["datenbank"]

    if (file_exists ( "e_cfg.inc.php" )) REQUIRE "e_cfg.inc.php" ;
    if (file_exists ( "./4fcfg/e_cfg.inc.php" )) REQUIRE "./4fcfg/e_cfg.inc.php" ;
    if (file_exists ( "../4fcfg/e_cfg.inc.php" )) REQUIRE "../4fcfg/e_cfg.inc.php" ;

    $conf_4f ["ablage_dir"] = $conf_web ["srvroot"]."/".
                              $conf_web ["pre_path"].
                              $conf_4f ["data"]."/".
                              $conf_4f_db ["datenbank"].
                              $conf_4f ["anhang"];

    $conf_4f ["ablage_uri"] = $conf_urlroot.
                              $conf_web ["pre_path"].
                              $conf_4f ["data"]."/".
                              $conf_4f_db ["datenbank"].
                              $conf_4f ["anhang"];

/*******************************************************************************/

    $conf_4f ["vordruck_dir"] = $conf_web ["srvroot"]."/".
                              $conf_web ["pre_path"].
                              $conf_4f ["data"]."/".
                              $conf_4f_db ["datenbank"].
                              $conf_4f ["vordruck"];

    $conf_4f ["einsatzende_dir"] = $conf_web ["srvroot"]."/".
                              $conf_web ["pre_path"].
                              $conf_4f ["data"]."/".
                              $conf_4f_db ["datenbank"] ;


    // Listendarstellungen Darstellung des Inhaltes
    $conf_4f_liste ["inhalt"] = 50 ; // Zeichen des Inhaltes


/*******************************************************************************/



    $tak_monate = array (
         "01" => 'jan',
         "02" => 'feb',
         "03" => 'mar',
         "04" => 'apr',
         "05" => 'mai',
         "06" => 'jun',
         "07" => 'jul',
         "08" => 'aug',
         "09" => 'sep',
         "10" => 'oct',
         "11" => 'nov',
         "12" => 'dec'
    );



    $rew_tak_monate = array (
         "jan" => '01',
         "feb" => '02',
         "mar" => '03',
         "apr" => '04',
         "mai" => '05',
         "may" => '05',
         "jun" => '06',
         "jul" => '07',
         "aug" => '08',
         "sep" => '09',
         "okt" => '10',
         "oct" => '10',
         "nov" => '11',
         "dez" => '12',
         "dec" => '12'
    );
/*****************************************************************/
//    Tabellenfarben
/*****************************************************************/

$color_data_table = "#E0E0E0";
$color_button     = "#E0E0E0";
$color_button_ok  = "#A0FFA0"; // auch für "Absenden"
$color_button_nok = "#FFA0A0";
?>
