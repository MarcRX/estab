<?php

if ( debug ){ echo "<b>!File:". __FILE__ ."  Line:". __LINE__ ."</b><big>DB-Operationen</big><br>";  }
/*****************************************************************************\
   Datei: db_operation.php

   benötigte Dateien:

   Beschreibung:

   (C) Hajo Landmesser
   mailto://hajo.landmesser@oberbruch.de
\*****************************************************************************/

  /******************************************************************************\
    Funktion: custom_db_ping  (mysqli)
  \******************************************************************************/
function custom_db_ping($mysqli) {
    if (!$mysqli) {
        return false;
    }
    try {
        $ok = $mysqli->query("SELECT 1");
    } catch (mysqli_sql_exception $e) {
        return false;
    }
    return (bool) $ok;
}


class db_access {

  public $db_server ;
  public $db_name ;
  public $db_table ;
  public $db_user ;
  public $db_pw ;

  public $sqlquery;
  public $db_sqlquery;
  public $db_result;
  public $result;
  public $resultcount ;

 public function __construct ($newdb_server, $newdb_name, $newdb_table, $newdb_user, $newdb_pw){
  	
  	if ( debug ){ echo "<b>!File:". __FILE__ ."  Line:". __LINE__ ."constructor </b>"."<br>";  }

    $this->db_server = $newdb_server ;
    $this->db_name   = $newdb_name ;
    $this->db_table  = $newdb_table ;
    $this->db_user   = $newdb_user ;
    $this->db_pw     = $newdb_pw ;
  }

  /******************************************************************************\
  Funktion: db_connection_check()
  \******************************************************************************/
  public function db_connection_check() {
    // Verbindung zur Datenbank herstellen inklusive Datenbankname
    $db = mysqli_connect($this->db_server, $this->db_user, $this->db_pw, $this->db_name);
    if (!$db) {
        die("[db_connection_check] Konnte keine Verbindung zur Datenbank herstellen: " . mysqli_connect_error());
    }
    mysqli_set_charset($db, 'utf8mb4');

    // Prüfen, ob Verbindung noch aktiv ist
    $result = custom_db_ping($db);

    mysqli_close($db);

    return $result;
}

/*
public  function db_connection_check (){
    $db = mysqli_connect($this->db_server,$this->db_user, $this->db_pw)
       or die ("[table_exist] Konnte keine Verbindung zur Datenbank herstellen");
    mysqli_query($db, 'SET NAMES utf8mb4');
    $result = mysqli_ping  ($db);

    return ($result);
  }
*/



  /******************************************************************************\
  Funktion: table_exist  ($tablename)
  \******************************************************************************/
  public function table_exist($tablename) {
    // Verbindung zur Datenbank herstellen
    $db = mysqli_connect($this->db_server, $this->db_user, $this->db_pw, $this->db_name);
    if (!$db) {
        die("[table_exist] Konnte keine Verbindung zur Datenbank herstellen: " . mysqli_connect_error());
    }
    mysqli_query($db, 'SET NAMES utf8mb4');

    // SQL-Abfrage um zu prüfen, ob Tabelle existiert
    $sql = "SHOW TABLES LIKE '" . mysqli_real_escape_string($db, $tablename) . "'";
    $result = mysqli_query($db, $sql);

    if (!$result) {
        echo "DB Fehler, Tabellen können nicht angezeigt werden\n";
        echo 'MySQL Fehler: ' . mysqli_error($db);
        exit;
    }

    // Wenn Ergebnis mindestens eine Zeile enthält, existiert die Tabelle
    $table_exist = mysqli_num_rows($result) > 0;

    mysqli_free_result($result);
    mysqli_close($db);

    return $table_exist;
}

/*
public  function table_exist ($tablename) {
    $db = mysqli_connect($this->db_server,$this->db_user, $this->db_pw)
       or die ("[table_exist] Konnte keine Verbindung zur Datenbank herstellen");
    mysqli_query($db, 'SET NAMES utf8mb4');
    $db_check = mysqli_select_db ($this->db_name)
       or die ("[read_table] Auswahl der Datenbank fehlgeschlagen");

    $result = mysqli_list_tables($this->db_name);

    if (!$result) {
      echo "DB Fehler, Tabellen können nicht angezeigt werden\n";
      echo 'MySQL Fehler: ' . mysqli_error($db);
      exit;
    }
    $table_exist = FALSE;
    while ($row = mysqli_fetch_row($result)) {
      if ( $tablename == $row [0] ) { $table_exist = TRUE; }
    }
    mysqli_free_result($result);
    return ( $table_exist );
  } // function table_exist
*/

/******************************************************************************\
  Funktion create_user_table ($tablename)
\******************************************************************************/

public  function create_user_table ($tablename, $fkttblname) {
  	  if ( debug ){ echo "<b>!File:". __FILE__ ."  Line:". __LINE__ ."</b>create_user_table<br>";}
    $db = mysqli_connect($this->db_server,$this->db_user, $this->db_pw)
       or die ("<b>!DB:". __FILE__ ."  Line:". __LINE__."create_user_table [connect] Konnte keine Verbindung zur Datenbank herstellen");
    mysqli_query($db, 'SET NAMES utf8mb4');

    $db_check = mysqli_select_db ($db, $this->db_name)
       or die ("<b>!DB:". __FILE__ ."  Line:". __LINE__."[read_table] Auswahl der Datenbank fehlgeschlagen");
     // gelesen

    $query = "CREATE TABLE IF NOT EXISTS ".$tablename."_read (
        `lfd` bigint(20) unsigned NOT NULL auto_increment COMMENT 'Laufende Nummer',
        `zeit` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzte Änderung',
        `nachnum` bigint(20) NOT NULL COMMENT 'Fremdschlüssel der Erledigten Nachricht',
        `gelesen` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'Zeitpunkt wann die Nachricht gelesen wurde',
        PRIMARY KEY  (`lfd`)
      ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;";
    $result = mysqli_query($db, $query);
    if (!$result) {
       die("<b>!DB:". __FILE__ ."  Line:". __LINE__."89 db_oper Ungueltige Abfrage: ". mysqli_error($db));
    }


     $query = "ALTER TABLE `".$tablename."_read` ADD INDEX IF NOT EXISTS `nachnum` ( `nachnum` ) ";
     $result = mysqli_query($db, $query);
    if (!$result) {
       die("<b>!DB:". __FILE__ ."  Line:". __LINE__."95 db_oper Ungueltige Abfrage: " . mysqli_error($db));
    }

     // erledigte bezogen auf die Funktion

    $query = "CREATE TABLE IF NOT EXISTS ".$fkttblname."_erl (
        `lfd` bigint(20) unsigned NOT NULL auto_increment COMMENT 'Laufende Nummer',
        `zeit` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzte Änderung',
        `nachnum` bigint(20) NOT NULL COMMENT 'Fremdschlüssel der Erledigten Nachricht',
        `erledigt` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'Zeitpunkt wann die Nachricht gelesen wurde',
        PRIMARY KEY  (`lfd`)
      ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;";

    $result = mysqli_query($db, $query);
    if (!$result) {
       die("<b>!DB:". __FILE__ ."  Line:". __LINE__." db_Oper Ungueltige Abfrage: " . mysqli_error($db));
    }


     $query = "ALTER TABLE `".$fkttblname."_erl` ADD INDEX IF NOT EXISTS `nachnum` ( `nachnum` ) ";
     $result = mysqli_query($db, $query);
    if (!$result) {
       die("<b>!DB:". __FILE__ ."  Line:". __LINE__." db_oper Ungueltige Abfrage: " . mysqli_error($db));
    }

     // Kategorien der Funktion
    $query = "CREATE TABLE IF NOT EXISTS ".$fkttblname."_katego (
        `lfd` bigint(20) unsigned NOT NULL auto_increment COMMENT 'Laufende Nummer',
        `kategorie` varchar(10) NOT NULL COMMENT 'Benutzer definierte Kategorien',
        `beschreibung` varchar (254) NULL COMMENT 'Beschreibung zur Kategorie',
        PRIMARY KEY  (`lfd`)
      ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;";

    $result = mysqli_query($db, $query);
    if (!$result) {
       die("<b>!DB:". __FILE__ ."  Line:". __LINE__." db_oper Ungueltige Abfrage: " . mysqli_error($db));
    }
     // Zuordnung Kategorien <--> Meldung
    $query = "CREATE TABLE IF NOT EXISTS ".$fkttblname."_kategolink (
             `msg` bigint(20) NOT NULL,
          `katego` bigint(20) NOT NULL
           ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ;";

    $result = mysqli_query($db, $query);
    if (!$result) {
       die("<b>!DB:". __FILE__ ."  Line:". __LINE__."Ungueltige Abfrage: " . mysqli_error($db));
    }



     // Kategorien
    $query = "CREATE TABLE IF NOT EXISTS ".$tablename."_katego (
        `lfd` bigint(20) unsigned NOT NULL auto_increment COMMENT 'Laufende Nummer',
        `kategorie` varchar(10) NOT NULL COMMENT 'Benutzer definierte Kategorien',
        `beschreibung` varchar (254) NULL COMMENT 'Beschreibung zur Kategorie',
        PRIMARY KEY  (`lfd`)
      ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;";

    $result = mysqli_query($db, $query);
    if (!$result) {
       die("<b>!DB:". __FILE__ ."  Line:". __LINE__." db_oper Ungueltige Abfrage: " . mysqli_error($db));
    }
     // Zuordnung Kategorien <--> Meldung
    $query = "CREATE TABLE IF NOT EXISTS ".$tablename."_kategolink (
             `msg` bigint(20) NOT NULL,
          `katego` bigint(20) NOT NULL
           ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ;";

    $result = mysqli_query($db, $query);
    if (!$result) {
       die("<b>!DB:". __FILE__ ."  Line:". __LINE__."  Ungueltige Abfrage: " . mysqli_error($db));
    }
  }

  public function ensure_user_table ($tablename, $fkttblname) {
    foreach (array($fkttblname."_erl", $tablename."_read") as $table) {
      $table_sql = str_replace(array("\\", "'"), array("\\\\", "\\'"), $table);
      $vorhanden = $this->query_table_wert(
        "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = '".$table_sql."'"
      );
      if (empty($vorhanden[0])) {
        $this->create_user_table($tablename, $fkttblname);
        return;
      }
    }
  }


public function read_table (){
    $db = mysqli_connect($this->db_server,$this->db_user, $this->db_pw)
       or die ("[read_table] Konnte keine Verbindung zur Datenbank herstellen");
    mysqli_query($db, 'SET NAMES utf8mb4');
    $db_check = mysqli_select_db ($this->db_name)
       or die ("[read_table] Auswahl der Datenbank fehlgeschlagen");

    $this->sqlquery = "SELECT * FROM $this->db_table WHERE 1" ;

    $query_result = mysqli_query ($db, $this->sqlquery) or
       die("[read_table]  103-".mysqli_error($db)." ".mysqli_errno($db));

    $this->resultcount = mysqli_num_rows($query_result);

    for ($i=1;$i<=$this->resultcount;$i++){
      $this->result[$i] = mysqli_fetch_assoc($query_result);
    }

    mysqli_free_result($query_result);
    mysqli_close ($db);
    return ($this->result);
  } // function read_table


public  function query_table($query) {
      // Debug-Konstante prüfen
      if (defined('DEBUG') && DEBUG) {
          echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><big>getoutqueuecount</big><br>";
      }
      $this->result = array(); // direkt als Array initialisieren
      $this->sqlquery = $query;

      $db = mysqli_connect($this->db_server, $this->db_user, $this->db_pw)
          or die("[query_table196] Konnte keine Verbindung zur Datenbank herstellen" . mysqli_error($db));
      mysqli_query($db, 'SET NAMES utf8mb4');

      $db_check = mysqli_select_db($db, $this->db_name)
          or die("[query_table199] Auswahl der Datenbank fehlgeschlagen" . mysqli_error($db));

      if (defined('DEBUG') && DEBUG) {
          echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b>";
          echo "sqlquery = "; var_dump($this->sqlquery); echo "<br>";
      }

      $query_result = mysqli_query($db, $this->sqlquery)
          or die("[query_table202] <br>$query<br>103-" . mysqli_error($db) . " " . mysqli_errno($db));

      $i = 0;
      while ($row = mysqli_fetch_assoc($query_result)) {
          $this->result[$i] = $row;
          if (defined('DEBUG') && DEBUG) {
              echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b>";
              echo "result [i]= "; var_dump($this->result[$i]); echo "<br>";
          }
          $i++;
      }

      /* free result set */
      mysqli_free_result($query_result);

      if (defined('DEBUG') && DEBUG) {
          echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b> count=" . count($this->result) . "<br>\n";
          echo "result = "; var_dump($this->result); echo "<br>\n";
      }

      mysqli_close($db);
      return $this->result;
  } // function query_table

  /*
  function query_table ($query){
  	if ( debug ){ echo "<b>!File:". __FILE__ ."  Line:". __LINE__ ."</b><big>getoutqueuecount</big><br>";  }
    $this->result = array();
    $this->sqlquery = $query ;

    $db = mysqli_connect($this->db_server,$this->db_user, $this->db_pw)
       or die ("[query_table196] Konnte keine Verbindung zur Datenbank herstellen" . mysqli_error($db));
    mysqli_query($db, 'SET NAMES utf8mb4');

    $db_check = mysqli_select_db ($db, $this->db_name)
       or die ("[query_table199] Auswahl der Datenbank fehlgeschlagen" . mysqli_error($db));

    if ( debug ){ echo "<b>!File:". __FILE__ ."  Line:". __LINE__ ."</b>" ; echo "sqlquery = "; var_dump ($this->sqlquery); echo"<br>";  }

    $query_result = mysqli_query ($db, $this->sqlquery) or
       die("[query_table202] <br>$query<br>103-".mysqli_error($db)." ".mysqli_errno($db));
     /* fetch associative array */
  /*  $i=0;
    while ($row = mysqli_fetch_assoc($query_result)) {
    	$this->result[$i] = $row;
      if ( debug ){ echo "<b>!File:". __FILE__ ."  Line:". __LINE__ ."</b>" ; echo "result [i]= "; var_dump ($this->result[$i]); echo"<br>";  }
      $i++;
    }

    if ( debug ){ echo "<b>!File:". __FILE__ ."  Line:". __LINE__ ."</b> count=".count ($this->result); echo"<br>\n";
      echo "result = "; var_dump ($this->result); echo"<br>\n";  }

    /* free result set */
/*    mysqli_free_result($query_result);
    mysqli_close ($db);
    return ($this->result);
  } // function query_table
*/

/********************************************************************\
|	Function    : query_table_wert												|
|  Eingabswert :
\********************************************************************/

public function query_table_wert ($query){
    $this->result = "";
    $this->sqlquery = $query ;

    $db = mysqli_connect($this->db_server,$this->db_user, $this->db_pw)
       or die ("[query_table_wert] Konnte keine Verbindung zur Datenbank herstellen");
    mysqli_query($db, 'SET NAMES utf8mb4');
    $db_check = mysqli_select_db ($db, $this->db_name)
       or die ("[query_table_wert] Auswahl der Datenbank fehlgeschlagen");

    if ( debug ){ echo "<b>!File:". __FILE__ ."  Line:". __LINE__ ."  query_table_wert  </b><br>\n"; var_dump ($this->sqlquery); echo "~~~~~~~~~~<br>";}

    $query_result = mysqli_query ($db, $this->sqlquery) or
       die("[query_table_wert] 103-".mysqli_error($db)." ".mysqli_errno($db));

    $this->resultcount = mysqli_num_rows($query_result);

    $this->result = mysqli_fetch_row($query_result);

    mysqli_free_result($query_result);
    mysqli_close ($db);
    return ($this->result);
  } // function query_table_wert


public  function query_table_iu ($query){
    $this->sqlquery = $query ;
    $db = mysqli_connect($this->db_server,$this->db_user, $this->db_pw)
       or die ("[query_table_iu] Konnte keine Verbindung zur Datenbank herstellen");
    mysqli_query($db, 'SET NAMES utf8mb4');
    $db_check = mysqli_select_db ($db, $this->db_name)
       or die ("[query_table_iu] Auswahl der Datenbank fehlgeschlagen");
    $query_result = mysqli_query ($db, $this->sqlquery) or
       die("[query_table_iu] ".mysqli_error($db)." ".mysqli_errno($db)."<br> Query=".$query);
    mysqli_close ($db);
    return ($query_result);
  } // function query_table_iu



public  function query_usrtable ($query){
    $this->result = [];
    $this->sqlquery = $query ;
    if ( debug ){ echo "<b>!File:". __FILE__ ."  Line:". __LINE__ ."  query_usrtable  </b><br>\n"; var_dump ($this->sqlquery); echo "<br>";}

    $db = mysqli_connect($this->db_server,$this->db_user, $this->db_pw)
       or die ("<b>!File:". __FILE__ ."  Line:". __LINE__ ." Konnte keine Verbindung zur Datenbank herstellen".mysqli_error($db)." ".mysqli_errno($db));
    mysqli_query($db, 'SET NAMES utf8mb4');
    $db_check = mysqli_select_db ($db, $this->db_name)
       or die ("<b>!File:". __FILE__ ."  Line:". __LINE__ ." Auswahl der Datenbank fehlgeschlagen".mysqli_error($db)." ".mysqli_errno($db));

    $query_result = mysqli_query ($db, $this->sqlquery) or
       die("<b>!File:". __FILE__ ."  Line:". __LINE__ ."-".mysqli_error($db)." ".mysqli_errno($db));

    if ( debug ){ echo "<b>!File:". __FILE__ ."  Line:". __LINE__ ."</b> query_result = ";
          var_dump($query_result) ;
      }

    $this->resultcount = mysqli_num_rows($query_result);

    for ($i=1;$i<=$this->resultcount;$i++){
      $this->result[$i] = mysqli_fetch_assoc($query_result);

      $this->result[$i] = $this->result[$i]["00_lfd"] ;
      
      if ( debug ){ echo "<b>!File:". __FILE__ ."  Line:". __LINE__ ."</b> this->result = ";
          var_dump($this->result) ;
      }
    }

    mysqli_free_result($query_result);
    mysqli_close ($db);
    return ($this->result);
  } // function read_table

} // class
?>
