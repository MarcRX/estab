<?php declare(strict_types=1);
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start(); // Start session only if not already started
}
/*****************************************************************************\
   Datei: backup.php

   benoetigte Dateien:

   Beschreibung:

   Funktionen:

   (C) Hajo Landmesser
   mailto://hajo.landmesser@oberbruch.de
\*****************************************************************************/

if (!defined('debug')) {   // true = gibt debuginformationen aus
    define('debug', false);
}

/*****************************************************************************

******************************************************************************/
// if ((!isset ($returnValue)) and (isset ($_GET))){   $returnValue = $_GET;}
$returnValue = !empty($_POST) ? $_POST : (!empty($_GET) ? $_GET : []);

include  ("../4fcfg/config.inc.php");
include  ("../4fcfg/dbcfg.inc.php");     // Datenbankparameter
include  ("../4fcfg/e_cfg.inc.php");     // Datenbankparameter
require_once  ("../4fach/db_operation.php");  // Datenbank operationen
require_once  ("../4fach/tools.php") ;
// require_once  ("../4fbak/backup_img.php") ;
require_once("../4fbak/backup_pdf.php");

// define ("outputtyp","png"); // png, jpg
define('FPDF_FONTPATH',$_SERVER ["DOCUMENT_ROOT"]."/".$conf_web ["pre_path"].'4fbak/fpdf/font/');


 if (debug) {
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><br>";
    echo "FPDF_FONTPATH = "; var_dump (FPDF_FONTPATH); echo "<br>";
  }

@ini_set('memory_limit', '64M');

  // schalte Ausführungslimit aus = unbegrenzte Laufzeit
set_time_limit ( 0 );

  if (isset($returnValue["anz"])){
    echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">";
    echo "<HTML>";
    echo "<HEAD>";
    echo "<META HTTP-EQUIV=\"CONTENT-TYPE\" CONTENT=\"text/html; charset=UTF-8\">";
    echo "<TITLE>Einsatz abschliessen.</TITLE>";
    echo "<META NAME=\"GENERATOR\" CONTENT=\"OpenOffice.org 2.0  (Linux)\">";
    echo "<META NAME=\"AUTHOR\" CONTENT=\"Hajo Landmesser\">";
    echo "<META NAME=\"CREATED\" CONTENT=\"20070327;15421200\">";
    echo "<META NAME=\"CHANGEDBY\" CONTENT=\"hajo\">";
    echo "<META NAME=\"CHANGED\" CONTENT=\"20080612;18052200\">";
    echo "<meta http-equiv=\"cache-control\" content=\"no-cache\">";
    echo "<meta http-equiv=\"pragma\" content=\"no-cache\">";
    echo "</HEAD>";
    echo "<body style=\"font-family: 'Liberation Sans';\">";
  }


  if (isset ( $returnValue["anz"] )){ $anzahl = $returnValue["anz"]; } else { $anzahl = 1 ; }

  $dbaccess = new db_access ($conf_4f_db ["server"], $conf_4f_db ["datenbank"],$conf_4f_tbl ["benutzer"], $conf_4f_db ["user"],  $conf_4f_db ["password"]);
  $query = "SELECT * FROM `".$conf_4f_tbl ["nachrichten"]."` where ((`x04_druck` = 'f' OR `x04_druck` = '0') and (`x01_abschluss` = '1' OR `x01_abschluss` = 't')) LIMIT $anzahl";
    
  $result = $dbaccess->query_table ($query);
    
  $dbdata = $result ; //[1];

  if (debug) {
    echo "<br><br>\n------------------------Info Block Start------------------------------<br>\n";
    echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><br>";
    echo "query === ".$query."<br>";
    echo "dbdata === "; var_dump ($dbdata); echo "<br>";
  }

  if (is_array($dbdata) && $dbdata !== []) {
    foreach ($dbdata as $formdata){

      if (debug) { echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b>MARKE<br>";}
      $vordruckpdf = new vordruckaspdf ($formdata);
      
      if (debug) { echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b>MARKE<br>";}
      $vordruckpdf->SetFont ('helvetica'); // ('helvetica');

      if (debug) { echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b>MARKE<br>";}
      $vordruckpdf->SetAutoPageBreak(true, $vordruckpdf->bottom - $vordruckpdf->point[38][1]) ;

      if (debug) { echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b>MARKE<br>";}
      $vordruckpdf->main();

      $dbaccess = new db_access ($conf_4f_db ["server"], $conf_4f_db ["datenbank"],$conf_4f_tbl ["benutzer"], $conf_4f_db ["user"],  $conf_4f_db ["password"]);
      $query = "UPDATE `".$conf_4f_tbl ["nachrichten"]."` SET `x04_druck` = 't' where  `00_lfd` = ".$formdata ["00_lfd"]."; ";
      if (debug) { echo "query===".$query."<br>"; }

      $res = $dbaccess->query_table_iu ($query);
    }
  }
  
  if (isset($returnValue["anz"])){
  echo "<FORM action=\"../4fadm/admin.php\" method=\"get\" target=\"_self\">\n";
  echo "<fieldset>";
  echo "<legend>Aktion:</legend>\n";
  echo "<table border=\"1\" cellpadding=\"5\" cellspacing=\"0\" bgcolor=$color_data_table>\n";
  echo "<tr>\n";
  echo "<td bgcolor=$color_button_ok><input type=\"image\" name=\"absenden\" src=\"".$conf_design_path."/ok.gif\"></td>\n";
  echo "</td></tr>\n";
  echo "</table>\n";
  echo "</fieldset>";
  echo "<br>";
  }


  if (isset($returnValue["anz"])){
    $vordruck_uri = $conf_urlroot.$conf_web["pre_path"].$conf_4f["data"]."/".$conf_4f_db["datenbank"].$conf_4f["vordruck"]."/";
    echo "<big><big>Habe bis zu ".$anzahl." Vordrucke als Grafik erzeugt</big></big>";
    echo "<p><a href=\"".htmlspecialchars($vordruck_uri, ENT_QUOTES, "UTF-8")."\">Ordner mit den erzeugten PDF-Dateien</a></p>";
    echo "</BODY></HTML>";
  }

?>
