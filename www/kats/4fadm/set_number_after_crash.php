<?php
/*****************************************************************************\
   Datei: set_number_after_crash.php

   benötigte Dateien: config.inc.php, dbcfg.inc.php, protokoll.php


   Beschreibung:

          Fällt estab aus wird mit papier weitergearbeitet. Ist estab dann wider
          einsatzbereit muss es möglich sein den Nachrichtenzähler auf die nächste
          bis dahin aufgelaufene Nachrichtennummer zu inkrementieren.
          Dekrementieren ist nicht gestattet. Diese Aktion muss geloggt werden.
          Das Ändern des Zählerstandes soll als weitere Option im Adminbereich
          aufgelistet werden.

          First Page: Formular anzeigen und Counter inkrementiernen

          Second Page: Neue Counter Position setzten

   (C) David Toboll FGr F/K Münster
   mailto://d.toboll@thw-muenster.de
\******************************************************************************/

include("../4fcfg/dbcfg.inc.php");
include("../4fcfg/config.inc.php");
include("../4fcfg/fkt_rolle.inc.php");
include("../4fach/protokoll.php");

//Parameter
$page = $_GET['page'] ?? '';
if($page=='') $page = 1;
$protokoll = false;

//Datenbank Verbindung herstellen und überprüfen
 $db = mysqli_connect($conf_4f_db ["server"],$conf_4f_db ["user"], $conf_4f_db ["password"]);
 try {
   $db_con_is_ok = $db && $db->query("SELECT 1");
 } catch (mysqli_sql_exception $e) {
   $db_con_is_ok = false;
 }
 mysqli_query($db, 'SET NAMES utf8mb4');
 mysqli_select_db($db, $conf_4f_db ["datenbank"]) ;


//Datenbank abfrage und in Array ablegen
 $tabelle = $conf_4f_tbl['nachrichten'];
//DB Einlesen
$result = mysqli_query ($db, "SELECT `04_richtung`, `04_nummer` FROM `$tabelle` ORDER BY `00_lfd` DESC");
$nummer = [];
$richtung = [];
if ($result) {
            while($inhalt = mysqli_fetch_array($result, MYSQLI_ASSOC)){
            $nummer[] = $inhalt['04_nummer'];
            $richtung[] = $inhalt['04_richtung'];
            }
}

//Finde letzte Eingangsnummer
                 for($i=0; $i<count($richtung); $i++){
                 if($richtung[$i] == "E")
                         {
                         $e_nummer = $i;
                         break;
                         }
                 }
//Finde letzte Ausgangsnummer
                 for($i=0; $i<count($richtung); $i++){
                 if($richtung[$i] == "A")
                         {
                         $a_nummer = $i;
                         break;
                         }
                 }


//########################################## First Page ##########################################
if($page == 1) {
if (!isset($nummer[0])) { $nummer[0] = 0; }


//Nachweisung Überprüfen
if(Nachweisung == "gemeinsam")
         {
         ?>
<script type="text/javascript">
         function checkForm() {
         var strFehler='';
         if (document.forms[0].ea_nummer.value <= <?PHP echo $nummer[0]++; ?>)
         strFehler += "Feld Ein-/Ausgangsnummer fehlerhaft:Mindestwert <? echo $nummer[0]; ?> \n";

         if (strFehler.length>0) {
         alert("Festgestellte Probleme: \n\n"+strFehler);
         return(false);
         }
         }
</script>
<?php
         echo"<form action='' onsubmit='return checkForm()' method=get>";
         echo"<fieldset>";
         echo"<legend>Nachrichtennummer nach Systemausfall setzen</legend>";
         echo"Hinweis: Tragen Sie hier die letzte verwendete Nummer ein!";
         echo"<table border=2 cellpadding=5 cellspacing=0 bgcolor=#E0E0E0>";
         echo"<tr><td align=right><b>Ein-/Ausgangsnummer</b><br>Mindestwert: ".$nummer[0]."</td><td>";
         echo"<input name=ea_nummer_get type=text size=8>";
         echo"</td></tr></table>";
         echo"<input name=page type=hidden value=2 size=8>";
         echo"<input type='submit' value='Absenden'/>";
         echo"</fieldset></form>";
         }
elseif(Nachweisung == "getrennt")
         {

?>
<script type="text/javascript">
         function checkForm() {
         var strFehler='';
         if (document.forms[0].e_nummer.value < <? echo $nummer[$e_nummer]++; ?>)
         strFehler += "Feld Eingangsnummer fehlerhaft:Mindestwert <? echo $nummer[$e_nummer]; ?> \n";

          if (document.forms[0].a_nummer.value < <? echo $nummer[$a_nummer]++; ?>)
         strFehler += "Feld Ausgangsnummer fehlerhaft:Mindestwert <? echo $nummer[$a_nummer]; ?> \n";

         if (strFehler.length>0) {
         alert("Festgestellte Probleme: \n\n"+strFehler);
         return(false);
         }
         }
</script>
<?php
         //Formular schreiben
         echo"<form action='' onsubmit='return checkForm()' method=get>";
         echo"<fieldset>";
         echo"<legend>Nachrichtennummer nach Systemausfall setzen</legend>";
         echo"Hinweis: Tragen Sie hier die letzten verwendeten Nummeren ein!";
         echo"<table border=2 cellpadding=5 cellspacing=0 bgcolor=#E0E0E0>";
         echo"<tr><td align=right><b>Eingangsnummer</b><br>Mindestwert: ".$nummer[$e_nummer]."</td><td>";
         echo"<input name=e_nummer_get type=text size=8>";
         echo"</td></tr>";
         echo"<tr><td align=right><b>Ausgangsnummer</b><br>Mindestwert: ".$nummer[$a_nummer]."</td><td>";
         echo"<input name=a_nummer_get type=text size=8>";
         echo"</td></tr></table>";
         echo"<input name=page type=hidden value=2 size=8>";
         echo"<input type='submit' value='Absenden'/>";
         echo"</fieldset></form>";
         }
}
//########################################## Second Page ##########################################
if($page == 2){
//Neue Nummern im System setzen

//Datenbank Eintragungen


if(Nachweisung == "gemeinsam")
         {
         $ea_num = (int) ($_GET['ea_nummer_get'] ?? 0);
         $von = "eStab Systemmeldung";
         $inhalt1 = "Nachrichtenzähler wurde nach Systemausfall auf E/A".$ea_num." erhöht.";
         if($ea_num > $nummer[0]++)
                 {
                 $inhalt_sql = mysqli_real_escape_string($db, $inhalt1);
                 $von_sql = mysqli_real_escape_string($db, $von);
                 $jetzt = date("Y-m-d H:i:s");
                 $sichter = mysqli_real_escape_string($db, $redcopy2 ?? "S2");
                 $empf_sql = $sichter . "_rt," . $sichter . "_gn";
                 $eintrag = "INSERT INTO `$tabelle` (`04_richtung`, `04_nummer`, `12_inhalt`, `13_abseinheit`, `01_datum`, `12_abfzeit`, `14_funktion`, `16_empf`) VALUES ('E', $ea_num, '$inhalt_sql', '$von_sql', '$jetzt', '$jetzt', '$sichter', '$empf_sql')";
                 $eintragen = mysqli_query($db, $eintrag);
                 if($eintragen) echo "Nachweisnummer (Eingang/Ausgang) wurde gesetzt auf:".$ea_num;
                 $protokoll = true;
                 }
         else
                 {
                 echo "<b>Fehler:</b> Ihr eingegebner Wert (".$ea_num.") ist niedriger als der Mindeswert <u>".$nummer[0]++."</u>";
                 echo"<br><br> <a href=set_number_after_crash.php>Zurück</a> ";
                 }
         }
elseif(Nachweisung == "getrennt")
         {
         $e_num = (int) ($_GET['e_nummer_get'] ?? 0);
         $a_num = (int) ($_GET['a_nummer_get'] ?? 0);
         $enum = $nummer[$e_nummer]+1;
         $anum = $nummer[$a_nummer]+1;
         $von = "eStab Systemmeldung";
         $inhalt = "Nachrichtenzähler wurde nach Systemausfall auf E".$e_num."/A".$a_num." erhöht.";
         if($e_num > $enum  && $a_num > $anum )
                 {
                 $inhalt_sql = mysqli_real_escape_string($db, $inhalt);
                 $von_sql = mysqli_real_escape_string($db, $von);
                 $jetzt = date("Y-m-d H:i:s");
                 $sichter = mysqli_real_escape_string($db, $redcopy2 ?? "S2");
                 $empf_sql = $sichter . "_rt," . $sichter . "_gn";
                 $eintrag = "INSERT INTO `$tabelle` (`04_richtung`, `04_nummer`, `12_inhalt`, `13_abseinheit`, `01_datum`, `12_abfzeit`, `14_funktion`, `16_empf`, `15_quitdatum`, `15_quitzeichen`) VALUES ('E', $e_num, '$inhalt_sql', '$von_sql', '$jetzt', '$jetzt', '$sichter', '$empf_sql', '$jetzt', 'xxx')";
                 $eintragen = mysqli_query($db, $eintrag);
                 if($eintragen) echo "Nachweisnummer (Eingang) wurde gesetzt auf: ".$e_num."<br>";

                 $protokoll = true;

                 $eintrag = "INSERT INTO `$tabelle` (`02_zeit`, `03_zeichen`, `04_richtung`, `04_nummer`, `12_inhalt`, `10_anschrift`, `13_abseinheit`, `12_abfzeit`, `14_funktion`, `16_empf`) VALUES ('$jetzt', 'xxx', 'A', $a_num, '$inhalt_sql', '$von_sql', '$von_sql', '$jetzt', '$sichter', '$empf_sql')";
                 $eintragen = mysqli_query($db, $eintrag);
                 if($eintragen) echo "Nachweisnummer (Ausgang) wurde gesetzt auf: ".$a_num;
                 }
         else
                 {
                 if($e_num <= $enum)   { echo "<b>Fehler:</b> Ihr eingegebner Eingangs-Wert (".$e_num.") ist niedriger als der Mindeswert <u>".$enum."</u><br>"; }
                 if($a_num <= $anum) { echo "<b>Fehler:</b> Ihr eingegebner Ausgangs-Wert (".$a_num.") ist niedriger als der Mindeswert <u>".$anum."</u>"; }

                 echo"<br><br> <a href=set_number_after_crash.php>Zurück</a> ";
                 }
         }


//Protokoll eintrag
if($protokoll)
         {
         if(Nachweisung == "gemeinsam") $wert_neu = "E/A".$ea_num;
         elseif(Nachweisung == "getrennt") $wert_neu = "E".$e_num." / A".$a_num;
         $was = "Nachrichtennummer Sync";
         $daten = "Nachrichtenz&auml;hler nach Systemausfall auf ".$wert_neu." gesetzt.";
         protokolleintrag($was, $daten);
         }


}

echo "<br><br><br><hr><a href=admin.php>Zurück zur Administrations Übersicht</a>";

?>
