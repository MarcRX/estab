<?php
/*****************************************************************************\
   Datei: status.php

   benötigte Dateien:    tools.php, db_operation.php

   Beschreibung:

          Hier wird die Statusspalte links dargestellt.

   (C) Hajo Landmesser
   mailto://hajo.landmesser@oberbruch.de
\******************************************************************************/
if (!defined('debug')) {   // true = gibt debuginformationen aus
    define('debug', false);
}

if (debug) {
  ini_set('display_errors', '1');
  error_reporting(E_ALL);

  echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><big><big>Dateianfang</big></big><br>";
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start(); // Start session only if not already started
}
/*!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!*/
include ("../4fcfg/dbcfg.inc.php");    // Datenbankparameter

  $db = mysqli_connect($conf_4f_db   ["server"],$conf_4f_db   ["user"], $conf_4f_db   ["password"] );
  try {
    $result = $db && $db->query("SELECT 1");
  } catch (mysqli_sql_exception $e) {
    $result = false;
  }
  mysqli_query($db, 'SET NAMES utf8mb4');
  if ($result == false){
    echo "<big>DB?</big>";
    exit;
  }
  if (isset($db)){
    mysqli_close($db);
  }

/*!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!*/

if (isset($debug) && $debug) {echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><big><big>Vor include tools.php</big></big><br>";}
include ("tools.php");
if (isset($debug) && $debug) {echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><big><big>Vor include db_operation.php</big></big><br>";}
include ("db_operation.php");
if (isset($debug) && $debug) {echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><big><big>Vor include conf.inc.php</big></big><br>";}
include ("../4fcfg/config.inc.php");

if (isset($debug) && $debug) {echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><big><big>Vor pre_html</big></big><br>";}
pre_html ("status", "Status","");

echo "<body style=\"font-family: 'Liberation Sans';\" bgcolor=\"#ECECFF\">";

systemstatus ("vertikal");

echo "<table align=\"center\" style=\"text-align:center; width: 50px; background-color: rgb(150, 150, 150); height: 10px;\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
echo "<tbody>\n";
echo "<tr><td>";
echo "<img src=\"".$conf_design_path."/timer.gif\">";
echo "</td></tr>";
echo "</table>";
?>

</body>

</html>
