<?php

define("debug", false);
// umdefinieren der Konstanten - nur in PHP 4
/*
define("FATAL", E_USER_ERROR);
define("ERROR", E_USER_WARNING);
define("WARNING", E_USER_NOTICE);

// die Stufe für dieses Skript einstellen
error_reporting(E_ALL);

	if (debug){ echo "<b>Start in </b>".__file__.":".__LINE__."):<br>" ;}

// Fehlerbehandlungsfunktion

function myErrorHandler($errno, $errstr, $errfile, $errline)
{
  switch ($errno) {
  case FATAL:
    echo "<b>FATAL</b> [$errno] $errstr<br />\n";
    echo "  Fatal error in line $errline of file $errfile";
    echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
    echo "Aborting...<br />\n";
    exit(1);
    break;
  case ERROR:
    echo "<b>ERROR</b> [$errno] $errstr<br />\n";
    break;
  case WARNING:
    echo "<b>WARNING</b> [$errno] $errstr<br />\n";
    break;
  default:
//    echo "Unkown error type: [$errno] $errstr<br />\n";
    break;
  }
}

// Funktion zum Test der Fehlerbehandlung
function scale_by_log($vect, $scale)
{
  if (!is_numeric($scale) || $scale <= 0) {
    trigger_error("log(x) for x <= 0 is undefined, you used: scale = $scale",
      FATAL);
  }

  if (!is_array($vect)) {
    trigger_error("Incorrect input vector, array of values expected", ERROR);
    return null;
  }

  for ($i=0; $i<count($vect); $i++) {
    if (!is_numeric($vect[$i]))
      trigger_error("Value at position $i is not a number, using 0 (zero)",
        WARNING);
    $temp[$i] = log($scale) * $vect[$i];
  }
  return $temp;
}


function outerrormsg ($errno, $errtxt) {
   switch ($errno){
      case 0 :
         echo "OK";
      break;
      case 1045 :
         echo "Benutzer oder Passwort zur Anmeldung am SQL-Server falsch.";
      break;
      case 1049 :
         echo "<p>Datenbank nicht vorhanden <a href=\"./4fadm/create_db.php\">Datenbank anlegen</a></p>";
      break;
      case 2005 :
         echo "SQL-Server nicht bekannt.";
      break;
      default:
         echo "Fehlernummer = ".$errno."<br>Fehlertext = ".$errtxt;
      break;
    }
}

*/

/*-----------------------------------------------------------------------------\


\-----------------------------------------------------------------------------*/

// $old_error_handler = set_error_handler("myErrorHandler");
   if ( debug ){ echo "<b>!File:". __FILE__ ."  Line:". __LINE__ ."<br>";}

  include "../4fcfg/dbcfg.inc.php";
  include "../4fcfg/e_cfg.inc.php";
  include "../4fcfg/config.inc.php";

  echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">";
  echo "<HTML>";
  echo "<HEAD>";
  echo "<META HTTP-EQUIV=\"CONTENT-TYPE\" CONTENT=\"text/html; charset=UTF-8\">";
  echo "<TITLE>Datenbanktabellen anlegen</TITLE>";
  echo "<META NAME=\"GENERATOR\" CONTENT=\"OpenOffice.org 2.0  (Linux)\">";
  echo "<META NAME=\"AUTHOR\" CONTENT=\"Hajo Landmesser\">";
  echo "<META NAME=\"CREATED\" CONTENT=\"20070327;15421200\">";
  echo "<META NAME=\"CHANGEDBY\" CONTENT=\"hajo\">";
  echo "<META NAME=\"CHANGED\" CONTENT=\"20080620;18052200\">";
  echo "</HEAD>";
  echo "<body style=\"font-family: 'Liberation Sans';\">\n";

  echo "<FORM action=\"../4fadm/admin.php\" method=\"get\" target=\"_self\">\n";

  echo "<fieldset>";
  echo "<legend>Aktion:</legend>\n";
  echo "<table border=\"1\" cellpadding=\"5\" cellspacing=\"0\" bgcolor=$color_data_table>\n";
  echo "<tr>\n";
  echo "<td bgcolor=$color_button_ok><input type=\"image\" name=\"absenden\" src=\"".$conf_design_path."/ok.gif\"></td>\n";
  echo "</td></tr>\n";
  echo "</table>\n";

  $link = mysqli_connect(  $conf_4f_db ["server"], $conf_4f_db ["user"],  $conf_4f_db ["password"] )
     or die("<b>!DB:". __FILE__ ."  Line:". __LINE__ ."keine Verbindung m&ouml;glich: </b>");
      
  mysqli_query($link, 'SET NAMES utf8mb4');

  if ( debug ){ echo "<b>!File:". __FILE__ ."  Line:". __LINE__ ."  Verbindung erfolgreich  </b><br>\n";}
  
  $query = "CREATE DATABASE IF NOT EXISTS ".$conf_4f_db ["datenbank"].
	   " DEFAULT CHARACTER SET utf8
             DEFAULT COLLATE utf8_general_ci ;" ;

  $result = mysqli_query($link, $query);
    
  if ( debug ){ echo "<b>!File:". __FILE__ ."  Line:". __LINE__ ."  query : </b>\n"; var_dump ($query); echo "<br>";}
  
  if (!$result) {
//     echo "Query01=".$query."<br>";
     die('Ung&uuml;ltige Abfrage: ' . mysqli_error($link)." ".__file__.":".__LINE__."):<br>");
  } else {
    echo 'Datenbank wurde angelegt oder war schon vorhanden';
    echo "<br>\n";
  }


  include "../4fadm/create_dir.php";


  // benutze Datenbank vStab_db
  $db_selected = mysqli_select_db($link, $conf_4f_db ["datenbank"]);
  if (!$db_selected) {
     die ('Kann Datenbank nicht benutzen : ' . mysqli_error());
  } else {
    echo 'Datenbank wurde ausgew&auml;hlt';
    echo "<br>\n";
  }

  $query = "CREATE TABLE IF NOT EXISTS `".$conf_4f_tbl ["nachrichten"]."` (
    `00_lfd` bigint(20) NOT NULL auto_increment,
    `01_medium` set('Fe','Fu','Me','FAX','FS','@') NOT NULL default '',
    `01_datum` datetime NOT NULL default '0000-00-00 00:00:00',
    `01_zeichen` char(3) NOT NULL default '',
    `02_zeit` datetime NOT NULL default '0000-00-00 00:00:00',
    `02_zeichen` char(3) NOT NULL default '',
    `03_datum` datetime NOT NULL default '0000-00-00 00:00:00',
    `03_zeichen` char(3) NOT NULL default '',
    `04_richtung` set('E','A') NOT NULL default '',
    `04_nummer` bigint(20) NOT NULL default '0',
    `05_gegenstelle` varchar(128) NOT NULL default '',
    `06_befweg` varchar(128) NOT NULL default '',
    `06_befwegausw` set('Fe','Fu','Me','FAX','FS','@') NOT NULL default '',
    `07_durchspruch` set('D','S') NOT NULL default '',
    `08_befhinweis` varchar(128) NOT NULL default '',
    `08_befhinwausw` set('Fe','Fu','Me','FAX','FS','@') NOT NULL default '',
    `09_vorrangstufe` set('eee','sss','bbb','aaa') NOT NULL default '',
    `10_anschrift` varchar(255) NOT NULL default '',
    `11_gesprnotiz` binary(1) NOT NULL default 'f',
    `12_anhang` text NULL,
    `12_inhalt` longtext NOT NULL,
    `12_abfzeit` datetime NOT NULL default '0000-00-00 00:00:00',
    `13_abseinheit` varchar(128) NOT NULL default '',
    `14_zeichen` char(3) NOT NULL default '',
    `14_funktion` varchar(128) NOT NULL default '',
    `15_quitdatum` datetime NOT NULL default '0000-00-00 00:00:00',
    `15_quitzeichen` char(3) NOT NULL default '',
    `16_empf` tinytext NULL,
    `17_vermerke` longtext NULL,
    `20_master_katego` bigint(20),
    `x00_status` smallint(6) NOT NULL default '0',
    `x01_abschluss` binary(1) NOT NULL default '0',
    `x02_sperre` binary(1) NOT NULL default '0',
    `x03_sperruser` char(3) NOT NULL default '',
    `x04_druck` binary(1) NOT NULL default '0',
    `x05_druck_d` datetime NOT NULL default '0000-00-00 00:00:00',
    `99_lstacc` timestamp NOT NULL default '0000-00-00 00:00:00' on update CURRENT_TIMESTAMP,
    KEY `00_lfd` (`00_lfd`),
    FULLTEXT KEY `12_inhalt` (`12_inhalt`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

  $result = mysqli_query($link, $query);
  if (!$result) {
     die('Ung&uuml;ltige Abfrage: ' . mysqli_error());
  } else {
    echo 'Meldungstabelle wurde angelegt.';
    echo "<br>\n";
  }

    // Ist die Tabelle FKTMTX vorhanden

  $result = mysqli_query($link, 'Show Tables');

  if (!$result) {
    echo "DB Fehler, Tabellen können nicht angezeigt werden\n";
    echo 'MySQL Fehler: ' . mysqli_error();
    exit;
  }
  $fktmtxtable = false;
  while ($row = mysqli_fetch_row($result)) {
    if ( $conf_4f_tbl ["empfmtx"] == $row [0] ) { $fktmtxtable = true; }
  }
  mysqli_free_result($result);

/*
    echo "<b><big>fktmtxtable===";
    if ($fktmtxtable == true) {echo "TRUE";} else {echo "FALSE";};
    echo "</big></b><br>";
*/

    // Wenn die Tabelle nicht da ist
  if (!$fktmtxtable){
    /*
        erstelle Tab
    */
    $query = "CREATE TABLE IF NOT EXISTS `".$conf_4f_tbl ["empfmtx"]."` (
                `mtx_lfd` bigint(11) unsigned NOT NULL auto_increment,
                `mtx_x` int(11) NOT NULL,
                `mtx_y` int(11) NOT NULL,
                `mtx_typ` set('cb','t') character set utf8 collate utf8_general_ci NOT NULL,
                `mtx_fkt` varchar(6) NOT NULL,
                `mtx_rolle` set('Stab','FB') character set utf8 collate utf8_general_ci NOT NULL,
                `mtx_mode` set('ro','rw') NOT NULL,
                `mtx_rc2`  binary(1) NOT NULL default 'f',
                `mtx_auto` binary(1) NOT NULL default 'f',
                PRIMARY KEY  (`mtx_lfd`)
              ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";


    $result = mysqli_query($link, $query);
    if (!$result) {
       die('Ung&uuml;ltige Abfrage: ' . mysqli_error());
    } else {
      echo 'Funktionsmatrixtabelle wurde angelegt.';
      echo "<br>\n";
    }

    // Default-Stabsbesetzung aus 4fcfg/deault.fkt.php (Zeile 1-5, Spalte 1-4).
    include "../4fcfg/deault.fkt.php";
    if (!isset($redcopy2)) { $redcopy2 = ""; }

    $query2 = "";
    $query1 = "INSERT INTO `".$conf_4f_tbl ["empfmtx"]."` (`mtx_x`, `mtx_y`, `mtx_typ`, `mtx_fkt`, `mtx_rolle`, `mtx_mode`, `mtx_rc2`, `mtx_auto`) VALUES ";
    for ($zeile = 1; $zeile <= 5; $zeile++) {
      for ($spalte = 1; $spalte <= 4; $spalte++) {
         $fkt   = "";
         $rolle = "";
         if (isset($empf_matrix[$zeile][$spalte]["fkt"])) {
           $fkt = $empf_matrix[$zeile][$spalte]["fkt"];
         }
         if (isset($empf_matrix[$zeile][$spalte]["rolle"])) {
           $rolle = $empf_matrix[$zeile][$spalte]["rolle"];
         }
         if (($fkt == "") or ($rolle == "leer")) {
           $typ   = "t";
           $rolle = "";
         } else {
           $typ = "cb";
         }
         if (($fkt != "") and ($fkt == $redcopy2)) { $redcpy = "1"; } else { $redcpy = "0"; }
         $fkt_sql   = mysqli_real_escape_string($link, $fkt);
         $rolle_sql = mysqli_real_escape_string($link, $rolle);
         $query2 .= " (".$zeile.", ".$spalte.", \"".$typ."\", \"".$fkt_sql."\", \"".$rolle_sql."\", \"ro\", \"".$redcpy."\", \"0\")";
         if ( ($zeile == 5) and ( $spalte == 4 )) { $query2 .= "; "; } else { $query2 .= ", "; }
      }
    }

    $query = $query1.$query2 ;


    $result = mysqli_query($link, $query);
    if (!$result) {
       die('Ung&uuml;ltige Abfrage: ' . mysqli_error($link));
    } else {
      echo 'Funktionsmatrixtabelle wurde mit der Default-Stabsbesetzung gef&uuml;llt.';
      echo "<br>\n";
    }
  }




  $query = "CREATE TABLE IF NOT EXISTS `".$conf_4f_tbl ["benutzer"]."` (
    `benutzer` varchar(50) NOT NULL default '',
    `kuerzel` varchar(6) NOT NULL default '',
    `funktion` varchar(10) NOT NULL default '',
    `rolle` varchar(15) NOT NULL default '',
    `sid` varchar(50) NOT NULL default '',
    `ip` varchar(15) NOT NULL default '',
    `fwdip` varchar(15) NOT NULL default '',
    `aktiv` smallint(1) NOT NULL,
    `password` varchar(32) NOT NULL default '',
    PRIMARY KEY  (`kuerzel`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";



  $result = mysqli_query($link, $query);
  if (!$result) {
     die('Ungültige Abfrage: ' . mysqli_error());
  } else {
    echo 'Benutzertabelle wurde angelegt.';
    echo "<br>\n";
  }


  $query = "CREATE TABLE IF NOT EXISTS `".$conf_4f_tbl ["masterkatego"]."` (
        `lfd` bigint(20) unsigned NOT NULL auto_increment COMMENT 'Laufende Nummer',
        `kategorie` varchar(10) NOT NULL COMMENT 'Benutzer definierte Kategorien',
        `beschreibung` varchar (254) NULL COMMENT 'Beschreibung zur Kategorie',
        PRIMARY KEY  (`lfd`)
      ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

  $result = mysqli_query($link, $query);
  if (!$result) {
     die('Ung&uuml;ltige Abfrage: ' . mysqli_error());
  } else {
    echo 'Masterkategorietabelle wurde angelegt.';
    echo "<br>\n";
  }


  $query = "CREATE TABLE IF NOT EXISTS `".$conf_4f_tbl ["masterkategolk"]."` (
             `msg` bigint(20) NOT NULL,
          `katego` bigint(20) NOT NULL,
           PRIMARY KEY  (`msg`)
           ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;";

  $result = mysqli_query($link, $query);
  if (!$result) {
     die('Ung&uuml;ltige Abfrage: ' . mysqli_error());
  } else {
    echo 'Masterkategorielinktabelle wurde angelegt.';
    echo "<br>\n";
  }




  $query = "CREATE TABLE IF NOT EXISTS `".$conf_4f_tbl ["protokoll"]."` (
    `p_lfd` bigint(20) NOT NULL auto_increment,
    `p_zeit` timestamp NOT NULL default CURRENT_TIMESTAMP,
    `p_was` varchar(30) NOT NULL default '',
    `p_ereignis` text NOT NULL,
    PRIMARY KEY  (`p_lfd`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

  $result = mysqli_query($link, $query);
  if (!$result) {
     die('Ung&uuml;ltige Abfrage: ' . mysqli_error());
  } else {
    echo 'Protokolltabelle wurde angelegt.';
    echo "<br>\n";
  }


  $query = "CREATE TABLE IF NOT EXISTS `".$conf_4f_tbl ["anhang"]."` (
      `lfd-nr` bigint(20) NOT NULL AUTO_INCREMENT,
      `filename` varchar(255) NOT NULL,
      `fileext` varchar(4) NOT NULL,
      `org_filename` varchar(255) NOT NULL,
      `comment` varchar(255) NOT NULL,
      `md5hash` varchar(32) NOT NULL,
      `date` datetime NOT NULL,
      `kuerzel` varchar(3) DEFAULT NULL,
      `status` tinyint(4) NOT NULL DEFAULT '1',
      `id` varchar(32) NOT NULL,
    PRIMARY KEY (`lfd-nr`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1; ";


  $result = mysqli_query($link, $query);
  if (!$result) {
     die('Ung&uuml;ltige Abfrage: ' . mysqli_error());
  } else {
    echo 'Anhangtabelle wurde angelegt.';
    echo "<br>\n";
  }

  $query = "CREATE TABLE IF NOT EXISTS `".$conf_tbl ["etb"]."` (
  `etb_lfd-nr` INT NOT NULL auto_increment,
  `etb_time` DATETIME NOT NULL ,
  `etb_aktion` TEXT NOT NULL ,
  `etb_bemerk` TEXT NOT NULL,
  `etb_benutzer` varchar(50) NOT NULL default '',
  `etb_kuerzel` varchar(6) NOT NULL default '',
  `etb_funktion` varchar(10) NOT NULL default '',
   PRIMARY KEY  (`etb_lfd-nr`)
  ) ENGINE = MYISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	if (debug){ echo "<b>Start in </b>".__file__.":".__LINE__."):<b>".$query."</b><br>" ;}
  $result = mysqli_query($link, $query);
  if (!$result) {
     die('Ung&uuml;ltige Abfrage: ' . mysqli_error());
  } else {
    echo "Einsatztagebuch wurde angelegt.";
    echo "<br>\n";
  }
	if (debug){ echo "<b>Start in </b>".__file__.":".__LINE__."):<br>" ;}
  $query = "CREATE TABLE IF NOT EXISTS `".$conf_tbl ["tbb"]."` (
  `tbb_lfd-nr` INT NOT NULL auto_increment,
  `tbb_time` DATETIME NOT NULL ,
  `tbb_aktion` TEXT NOT NULL ,
  `tbb_bemerk` TEXT NOT NULL,
  `tbb_benutzer` varchar(50) NOT NULL default '',
  `tbb_kuerzel` varchar(6) NOT NULL default '',
  `tbb_funktion` varchar(10) NOT NULL default '',
   PRIMARY KEY  (`tbb_lfd-nr`)
  ) ENGINE = MYISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
if (debug){ echo "<b>Start in </b>".__file__.":".__LINE__."):<b>".$query."</b><br>" ;}
  $result = mysqli_query($link, $query);
  if (!$result) {
     die('Ung&uuml;ltige Abfrage: ' . mysqli_error());
  } else {
    echo "Technisches Betriebsbuch wurde angelegt.";
    echo "<br>\n";
  }

	if (debug){ echo "<b>Start in </b>".__file__.":".__LINE__."):<br>" ;}
  $query = "CREATE TABLE IF NOT EXISTS `".$conf_tbl ["ubb"]."` (
  `ubb_lfd-nr` INT NOT NULL auto_increment,
  `ubb_time`   DATETIME NOT NULL ,
  `ubb_wo`     TEXT NOT NULL ,
  `ubb_wervon` TEXT NOT NULL,
  `ubb_weran`  TEXT NOT NULL,
  `ubb_was`    TEXT NOT NULL,
  `ubb_sonst`  TEXT NOT NULL,
  `ubb_benutzer` varchar(50) NOT NULL default '',
  `ubb_kuerzel` varchar(6) NOT NULL default '',
  `ubb_funktion` varchar(10) NOT NULL default '',
   PRIMARY KEY  (`ubb_lfd-nr`)
  ) ENGINE = MYISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
if (debug){ echo "<b>Start in </b>".__file__.":".__LINE__."):<b>".$query."</b><br>" ;}
  $result = mysqli_query($link, $query);
  if (!$result) {
     die('Ung&uuml;ltige Abfrage: ' . mysqli_error().'query='.$query  );
  } else {
    echo "Übungstagebuch wurde angelegt.";
    echo "<br>\n";
  }



$query = "CREATE TABLE IF NOT EXISTS `".$conf_tbl ["komplan"]."` (
  `lfd` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `stelle` varchar(255) NOT NULL,
  `orga` varchar(15) NULL,
  `rufname` varchar(40) NULL,
  `kanal4` varchar(8) NULL,
  `kanal2` varchar(7) NULL,
  `tel1` varchar(20) NULL,
  `tel2` varchar(20) NULL,
  `mobil1` varchar(20) NULL,
  `mobil2` varchar(20) NULL,
  `fax1` varchar(20) NULL,
  `fax2` varchar(20) NULL,
  `e-mail` varchar(255) NULL,
  `ftphttp` varchar(255) NULL,
  PRIMARY KEY (`lfd`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

  $result = mysqli_query($link, $query);
  if (!$result) {
     die('Ung&uuml;ltige Abfrage: ' . mysqli_error());
  } else {
    echo "Komunikationsplan wurde angelegt.";
    echo "<br>\n";
  }

$query = "CREATE TABLE IF NOT EXISTS `".$conf_tbl ["bhp50"]."` (
  `lfd-nr` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `anhang` varchar(7) ,
  `name` varchar(20) ,
  `vorname` varchar(20) ,
  `geschlecht` set('m','w') ,
  `nation` varchar(20) ,
  `gebdat` varchar(10) ,
  `fundort` varchar(30) ,
  `datum` varchar(10) ,
  `sich1` set('1','2','3','4','5') ,
  `sich1_arzt` varchar(20) ,
  `sich1_zeit` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sich2` set('1','2','3','4','5') ,
  `sich2_arzt` varchar(20) ,
  `sich2_zeit` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sich3` set('1','2','3','4','5') ,
  `sich3_arzt` varchar(20) ,
  `sich3_zeit` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sich4` set('1','2','3','4','5') ,
  `sich4_arzt` varchar(20) ,
  `sich4_zeit` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `diagnose` text ,
  `trans_liegend` set('t','f') ,
  `trans_sitzend` set('t','f') ,
  `mit_arzt` set('t','f') ,
  `isoliert` set('t','f') ,
  `trans_mittel` varchar(15) ,
  `trans_ziel` varchar(15) ,
  `trans_start` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `trans_dauer` float NOT NULL,
  `sudi_wohnort` varchar(30) ,
  `sudi_strasse` varchar(30) ,
  `sudi_konfekt` varchar(3) ,
  `sudi_verbleib` varchar(30) ,
  `sudi_bemerk` text ,
  PRIMARY KEY (`lfd-nr`),
  KEY `anhang` (`anhang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; ";

  $result = mysqli_query($link, $query);
  if (!$result) {
     die('Ung&uuml;ltige Abfrage: ' . mysqli_error());
  } else {
    echo "BHP 50 wurde angelegt.";
    echo "<br>\n";
  }

  echo 'Ich habe fertig.';
  echo "</big></big>\n";
  echo "</fieldset>\n";

  echo "</FORM>\n";
  echo "</BODY></HTML>\n";

  mysqli_close($link);



?>
