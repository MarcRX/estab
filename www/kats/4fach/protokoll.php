<?php
/*****************************************************************************\
   Datei: protokoll.php

   benötigte Dateien:

   Beschreibung:

   (C) Hajo Landmesser
   mailto://hajo.landmesser@oberbruch.de
\*****************************************************************************/
if ( debug ){ echo "<b>!File:". __FILE__ ."  Line:". __LINE__ ."</b><big><big>Protokoll</big></big><br>";  }
/*****************************************************************************\
  function protokolleintrag ();
       p_zeit,         - Zeitstempel
       p_was,        -  Art des Ereignis
       p_ereignis  -  Daten des Ereignis

Aufruf:

\*****************************************************************************/

  function protokolleintrag ($was, $daten){
     include ("../4fcfg/dbcfg.inc.php");
     include ("../4fcfg/e_cfg.inc.php");
     require_once ("../4fach/tools.php");
     include ("../4fcfg/config.inc.php");
     if ( debug ){ echo "<b>!File:". __FILE__ ."  Line:". __LINE__ ."</b><big><big>Protokoll</big></big><br>";  }

      if ( debug ){ echo "<b>!File:". __FILE__ ."  Line:". __LINE__ ."</b>Datenbank Verbindungsaufbau<br>";  }
      $db = mysqli_connect($conf_4f_db["server"],$conf_4f_db["user"], $conf_4f_db["password"])
            or die ("<b>!File:". __FILE__ ."  Line:". __LINE__." - Konnte keine Verbindung zur Protokolldatenbank herstellen");
            
      mysqli_query($db, 'SET NAMES utf8mb4');



     $query = "INSERT INTO ".$conf_4f_tbl["protokoll"]." SET
                   p_zeit          = \"".convtodatetime (date("dm"), date ("Hi"))."\",
                   p_was        = \"".$was."\",
                   p_ereignis = \"".mysqli_real_escape_string ($db, $daten)."\"";
      $result = "";
      
      if ( debug ){ echo "<b>!File:". __FILE__ ."  Line:". __LINE__ ."</b>Datenbank Verbindungsaufbau<br>";  }
      $db = mysqli_connect($conf_4f_db["server"],$conf_4f_db["user"], $conf_4f_db["password"])
            or die ("<b>!File:". __FILE__ ."  Line:". __LINE__." - Konnte keine Verbindung zur Protokolldatenbank herstellen");
            
      mysqli_query($db, 'SET NAMES utf8mb4');

      $db_check = mysqli_select_db ($db, $conf_4f_db ["datenbank"] )
           or die ("<b>!File:". __FILE__ ."  Line:". __LINE__ ."</b> - Auswahl der Protokolldatenbank fehlgeschlagen");
      if ( debug ){ echo "<b>!File:". __FILE__ ."  Line:". __LINE__ ."</b>Datenbank Verbindungsaufbau<br>";  }
      
      $query_result = mysqli_query ($db, $query)
           or die("<b>!File:". __FILE__ ."  Line:". __LINE__ ."</b> - ".mysqli_error()." ".mysqli_errno());

      mysqli_close ($db);
  } // function protokolleintrag
?>
