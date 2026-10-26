<?php

if (!defined('debug')) {   // true = gibt debuginformationen aus
    define('debug', false);
}

include ("../4fcfg/dbcfg.inc.php");
include ("../4fcfg/e_cfg.inc.php");
include ("../4fcfg/config.inc.php");

echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n";
echo "<html>\n";
echo "<head>\n";
echo "<meta content=\"text/html; charset=UTF-8\" http-equiv=\"content-type\">\n";
echo "<title>Datenbankverbindung prüfen</title>";
echo "</head>";
echo "<body style=\"font-family: 'Liberation Sans';\">";

echo "<FORM action=\"../4fadm/admin.php\" method=\"get\" target=\"_self\">\n";
echo "<fieldset>";
echo "<legend>Aktion:</legend>\n";
echo "<table border=\"1\" cellpadding=\"2\" cellspacing=\"0\" bgcolor=\"#E0E0E0\">\n";
echo "<tr>\n";
echo "<td bgcolor=$color_button_ok><input type=\"image\" name=\"absenden\" src=\"".$conf_design_path."/ok.gif\"></td>\n";
echo "</td></tr>\n";
echo "</table>\n";
echo "</fieldset>\n";
echo "<br>";

echo "<P align=center><FONT FACE=\"'Liberation Sans'\"><FONT SIZE=4>Datenbank Verbindungspr&uuml;fung:</FONT></FONT></P>";
echo "<br>\n";

// umdefinieren der Konstanten - nur in PHP 4
//define("FATAL", E_USER_ERROR);
//define("ERROR", E_USER_WARNING);
//define("WARNING", E_USER_NOTICE);


if ( debug ){ echo "<b>!File:". __FILE__ ."  Line:". __LINE__ ."</b><big></big><br>";  }

// die Stufe für dieses Skript einstellen
// error_reporting(E_ALL); //FATAL | ERROR | WARNING);

// Fehlerbehandlungsfunktion
/*
function myErrorHandler($errno, $errstr, $errfile, $errline)
{
if ( debug ){ echo "<b>!File:". __FILE__ ."  Line:". __LINE__ ."</b><big></big><br>";  }  
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
*/

// Funktion zum Test der Fehlerbehandlung
function scale_by_log($vect, $scale)
{
	if ( debug ){ echo "<b>!File:". __FILE__ ."  Line:". __LINE__ ."</b><big></big><br>";  }
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
if ( debug ){ echo "<b>!File:". __FILE__ ."  Line:". __LINE__ ."</b><big></big><br>";  }
   switch ($errno){
      case 0 :
         echo "OK";
      break;
      case 1045 :
         echo "Benutzer oder Passwort zur Anmeldung am SQL-Server falsch.";
      break;
       case 1046 :
         echo "Es ist keine Datenbank ausgewählt.";
      break;
      case 1049 :
         echo "<p>Datenbank nicht vorhanden <a href=\"./create_db.php\">Datenbank anlegen</a></p>";
      break;
      case 2005 :
         echo "SQL-Server nicht bekannt.";
      break;
      default:
         echo "Fehlernummer = ".$errno."<br>Fehlertext = ".$errtxt;
      break;
    }
}

function cmp_tablename ($tables, $cmp_table) {
  if ( debug ){ echo "<b>!File:". __FILE__ ."  Line:". __LINE__ ."</b><big></big><br>";  }
  $result = false;
  foreach ($tables as $table) {
    if ($cmp_table == $table){ $result = true; }
  }
  return $result;
}

  if ( debug ){ echo "<b>!File:". __FILE__ ."  Line:". __LINE__ ."</b><big></big><br>";  }
  echo "<table>";
  echo "<tbody>";
  echo "<tr>";
  echo "<th>Test</th><th>Ergebnis</th><th>Massnahme</th>";
  echo "</tr>";
    /**************************************************************************/
  echo "<tr>";
  echo "<td>";
  echo "<big><big>Verbindungstest: Datenbankserver</big></big>";
  echo "</td><td>";
    try {
      $db_hndl = mysqli_connect ( $conf_4f_db ["server"] ,
                                 $conf_4f_db ["user"] ,
                                 $conf_4f_db ["password"] );
    } catch (mysqli_sql_exception $e) {
      $db_hndl = false;
    }
    if ($db_hndl) {
      mysqli_set_charset($db_hndl, "utf8mb4");
      $db_errno  = mysqli_errno ($db_hndl);
      $db_errtxt = mysqli_error ($db_hndl);
    } else {
      $db_errno  = mysqli_connect_errno ();
      $db_errtxt = mysqli_connect_error ();
    }
    if ( debug ){ echo "<b>!File:". __FILE__ ."  Line:". __LINE__ ."</b><big></big><br>";  }
    if ($db_errno != 0) {
      echo "<p><img src=\"".$conf_menue ["symbole"]."/redlight.gif\" alt=\"schwerer Fehler\"></p>";
    } else {
      echo "<p><img src=\"".$conf_menue ["symbole"]."/greenlight.gif\" alt=\"keine Fehler\"></p>";
    }
  echo "</td>";
  echo "<td>";
  echo "<big><b>";
    outerrormsg ($db_errno, $db_errtxt);
  echo "</b></big>";
  echo "</td>";
  echo "</tr>";

    /**************************************************************************/
  echo "<tr>";
  echo "<td>";
  echo "<big><big>Verbindungstest: Datenbank <b>".$conf_4f_db ["datenbank"]."</b></big></big>";
  if ( debug ){ echo "<b>!File:". __FILE__ ."  Line:". __LINE__ ."</b><big></big><br>";  }
  echo "</td><td>";
    $result = $db_hndl ? mysqli_select_db ($db_hndl, $conf_4f_db ["datenbank"]) : false;

    $db_errno  = $db_hndl ? mysqli_errno ($db_hndl) : mysqli_connect_errno ();
    $db_errtxt = $db_hndl ? mysqli_error ($db_hndl) : mysqli_connect_error ();

    if ($db_errno != 0) {
      echo "<p><img src=\"".$conf_menue ["symbole"]."/redlight.gif\" alt=\"schwerer Fehler\"></p>";
    } else {
      echo "<p><img src=\"".$conf_menue ["symbole"]."/greenlight.gif\" alt=\"keine Fehler\"></p>";
    }
  echo "</td>";
  echo "<td>";
  echo "<big><b>";
    outerrormsg ($db_errno, $db_errtxt);
  echo "</b></big>";
  echo "</td>";
  echo "</tr>";

    /**************************************************************************/
  echo "<tr>";
  echo "<td>";
  echo "<big><big>Verbindungstest: Tabellen in der Datenbank <b>".$conf_4f_db ["datenbank"]."</b></big></big>";
  echo "</td><td>";
  if ( debug ){ echo "<b>!File:". __FILE__ ."  Line:". __LINE__ ."</b><big></big><br>";  }

  $sql = "SHOW TABLES FROM ".$conf_4f_db ["datenbank"];

  if ( debug ){ echo "<b>!File:". __FILE__ ."  Line:". __LINE__ ."</b><big>sql=".$sql."</big><br>";  }  
  
  $result = $db_hndl ? mysqli_query($db_hndl, $sql) : false;
  
  if ( debug ){ echo "<b>!File:". __FILE__ ."  Line:". __LINE__ ."</b><big></big><br>";  }
  
  //echo "result="; var_dump($result); echo "<br><br>";  
  
    $db_errno  = $db_hndl ? mysqli_errno ($db_hndl) : mysqli_connect_errno ();
    $db_errtxt = $db_hndl ? mysqli_error ($db_hndl) : mysqli_connect_error ();

    if ($db_errno != 0) {
      echo "<p><img src=\"".$conf_menue ["symbole"]."/redlight.gif\" alt=\"schwerer Fehler\"></p>";
    } else {
      echo "<p><img src=\"".$conf_menue ["symbole"]."/greenlight.gif\" alt=\"keine Fehler\"></p>";
    }
    if ( debug ){ echo "<b>!File:". __FILE__ ."  Line:". __LINE__ ."</b><big></big><br>";  }
    $i = 0;
    if ($result) {
      while ($row = mysqli_fetch_row($result)) {
         $tables[$i++] = $row[0] ;
      }
    }
    if ($result) {
      mysqli_free_result($result);
    }
    if ( debug ){ echo "<b>!File:". __FILE__ ."  Line:". __LINE__ ."</b><big></big><br>";  }
  echo "</td>";
  echo "<td>";
  echo "<big><b>";
    outerrormsg ($db_errno, $db_errtxt);
  echo "</b></big>";
  echo "</td>";
  echo "</tr>";

  /******  Benutzer *************************************************************/
  echo "<tr>";
  echo "<td>";
  echo "<big><big>Tabelle: <b>".$conf_4f_tbl ["benutzer"]."</b></big></big>";
  echo "</td><td>";
  if (cmp_tablename ($tables, $conf_4f_tbl ["benutzer"])) {
    echo "<p><img src=\"".$conf_menue ["symbole"]."/greenlight.gif\" alt=\"keine Fehler\"></p>";
  } else {
    echo "<p><img src=\"".$conf_menue ["symbole"]."/redlight.gif\" alt=\"schwerer Fehler\"></p>";
  }


  echo "</td>";
  echo "<td>";
  echo "<big><b>";
  outerrormsg ($db_errno, $db_errtxt);
  echo "</b></big>";
  echo "</td>";
  echo "</tr>";

  /******  Masterkategorie*******************************************************/
  echo "<tr>";
  echo "<td>";
  echo "<big><big>Tabelle: <b>".$conf_4f_tbl ["masterkatego"]."</b></big></big>";
  echo "</td><td>";
  if (cmp_tablename ($tables, $conf_4f_tbl ["masterkatego"])) {
    echo "<p><img src=\"".$conf_menue ["symbole"]."/greenlight.gif\" alt=\"keine Fehler\"></p>";
  } else {
    echo "<p><img src=\"".$conf_menue ["symbole"]."/redlight.gif\" alt=\"schwerer Fehler\"></p>";
  }


  echo "</td>";
  echo "<td>";
  echo "<big><b>";
  outerrormsg ($db_errno, $db_errtxt);
  echo "</b></big>";
  echo "</td>";
  echo "</tr>";

  /******  Masterkategorielink*******************************************************/
  echo "<tr>";
  echo "<td>";
  echo "<big><big>Tabelle: <b>".$conf_4f_tbl ["masterkategolk"]."</b></big></big>";
  echo "</td><td>";
  if (cmp_tablename ($tables, $conf_4f_tbl ["masterkategolk"])) {
    echo "<p><img src=\"".$conf_menue ["symbole"]."/greenlight.gif\" alt=\"keine Fehler\"></p>";
  } else {
    echo "<p><img src=\"".$conf_menue ["symbole"]."/redlight.gif\" alt=\"schwerer Fehler\"></p>";
  }


  echo "</td>";
  echo "<td>";
  echo "<big><b>";
  outerrormsg ($db_errno, $db_errtxt);
  echo "</b></big>";
  echo "</td>";
  echo "</tr>";



  /******  Nachrichten **********************************************************/
  echo "<tr>";
  echo "<td>";
  echo "<big><big>Tabelle: <b>".$conf_4f_tbl ["nachrichten"]."</b></big></big>";
  echo "</td><td>";
  if (cmp_tablename ($tables, $conf_4f_tbl ["nachrichten"])) {
    echo "<p><img src=\"".$conf_menue ["symbole"]."/greenlight.gif\" alt=\"keine Fehler\"></p>";
  } else {
    echo "<p><img src=\"".$conf_menue ["symbole"]."/redlight.gif\" alt=\"schwerer Fehler\"></p>";
  }


  echo "</td>";
  echo "<td>";
  echo "<big><b>";
  outerrormsg ($db_errno, $db_errtxt);
  echo "</b></big>";
  echo "</td>";
  echo "</tr>";

  /******  Protokoll ***********************************************************/
  echo "<tr>";
  echo "<td>";
  echo "<big><big>Tabelle: <b>".$conf_4f_tbl ["protokoll"]."</b></big></big>";
  echo "</td><td>";
  if (cmp_tablename ($tables, $conf_4f_tbl ["protokoll"])) {
    echo "<p><img src=\"".$conf_menue ["symbole"]."/greenlight.gif\" alt=\"keine Fehler\"></p>";
  } else {
    echo "<p><img src=\"".$conf_menue ["symbole"]."/redlight.gif\" alt=\"schwerer Fehler\"></p>";
  }
  echo "</td>";
  echo "<td>";
  echo "<big><b>";
  outerrormsg ($db_errno, $db_errtxt);
  echo "</b></big>";
  echo "</td>";
  echo "</tr>";


  /******   Anhang   ***********************************************************/
  echo "<tr>";
  echo "<td>";
  echo "<big><big>Tabelle: <b>".$conf_4f_tbl ["anhang"]."</b></big></big>";
  echo "</td><td>";
  if (cmp_tablename ($tables, $conf_4f_tbl ["anhang"])) {
    echo "<p><img src=\"".$conf_menue ["symbole"]."/greenlight.gif\" alt=\"keine Fehler\"></p>";
  } else {
    echo "<p><img src=\"".$conf_menue ["symbole"]."/redlight.gif\" alt=\"schwerer Fehler\"></p>";
  }
  echo "</td>";
  echo "<td>";
  echo "<big><b>";
  outerrormsg ($db_errno, $db_errtxt);
  echo "</b></big>";
  echo "</td>";
  echo "</tr>";


  /******  Einsatztagebuch ******************************************************/
  echo "<tr>";
  echo "<td>";
  echo "<big><big>Tabelle: <b>".$conf_tbl ["etb"]."</b></big></big>";
  echo "</td><td>";
  if (cmp_tablename ($tables, $conf_tbl ["etb"])) {
    echo "<p><img src=\"".$conf_menue ["symbole"]."/greenlight.gif\" alt=\"keine Fehler\"></p>";
  } else {
    echo "<p><img src=\"".$conf_menue ["symbole"]."/redlight.gif\" alt=\"schwerer Fehler\"></p>";
  }


  echo "</td>";
  echo "<td>";
  echo "<big><b>";
  outerrormsg ($db_errno, $db_errtxt);
  echo "</b></big>";
  echo "</td>";
  echo "</tr>";
  /******  Betriebbuch ***********************************************************/
  echo "<tr>";
  echo "<td>";
  echo "<big><big>Tabelle: <b>".$conf_tbl ["ubb"]."</b></big></big>";
  echo "</td><td>";
  if (cmp_tablename ($tables, $conf_tbl ["ubb"])) {
    echo "<p><img src=\"".$conf_menue ["symbole"]."/greenlight.gif\" alt=\"keine Fehler\"></p>";
  } else {
    echo "<p><img src=\"".$conf_menue ["symbole"]."/redlight.gif\" alt=\"schwerer Fehler\"></p>";
  }
  echo "</td>";
  echo "<td>";
  echo "<big><b>";
  outerrormsg ($db_errno, $db_errtxt);
  echo "</b></big>";
  echo "</td>";
  echo "</tr>";

  /******  Komunikationsplan******************************************************/
  echo "<tr>";
  echo "<td>";
  echo "<big><big>Tabelle: <b>".$conf_tbl ["komplan"]."</b></big></big>";
  echo "</td><td>";
  if (cmp_tablename ($tables, $conf_tbl ["komplan"])) {
    echo "<p><img src=\"".$conf_menue ["symbole"]."/greenlight.gif\" alt=\"keine Fehler\"></p>";
  } else {
    echo "<p><img src=\"".$conf_menue ["symbole"]."/redlight.gif\" alt=\"schwerer Fehler\"></p>";
  }
  echo "</td>";
  echo "<td>";
  echo "<big><b>";
  outerrormsg ($db_errno, $db_errtxt);
  echo "</b></big>";
  echo "</td>";
  echo "</tr>";


    /******  BHP 50   ***********************************************************/
  echo "<tr>";
  echo "<td>";
  echo "<big><big>Tabelle: <b>".$conf_tbl ["bhp50"]."</b></big></big>";
  echo "</td><td>";
  if (cmp_tablename ($tables, $conf_tbl ["bhp50"])) {
    echo "<p><img src=\"".$conf_menue ["symbole"]."/greenlight.gif\" alt=\"keine Fehler\"></p>";
  } else {
    echo "<p><img src=\"".$conf_menue ["symbole"]."/redlight.gif\" alt=\"schwerer Fehler\"></p>";
  }
  echo "</td>";
  echo "<td>";
  echo "<big><b>";
  outerrormsg ($db_errno, $db_errtxt);
  echo "</b></big>";
  echo "</td>";
  echo "</tr>";


  echo "<tbody>";
  echo "</table>";

  if ($db_hndl) {
    mysqli_close ($db_hndl);
  }
  
  if (isset($_GET["abbrechen_x"])) {
     header("Location: ".$conf_urlroot.$conf_web ["pre_path"]."/4fadm/admin.php");
  }
  
  echo "</FORM>";
  
?>
</BODY>
</HTML>
