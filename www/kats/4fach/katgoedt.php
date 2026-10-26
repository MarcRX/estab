<?php
/*****************************************************************************\
   Datei: katgoedt.php

   benoetigte Dateien:

   Beschreibung:

   (C) Hajo Landmesser 
   mailto://hajo.landmesser@oberbruch.de

   /katgoedt.php?dbtyp=user&fkt=edit&msgno=6
    /katgoedt.php?dbtyp=fkt&fkt=edit&msgno=6

\*****************************************************************************/

if (!defined('debug')) {   // true = gibt debuginformationen aus
    define('debug', false);
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start(); // Start session only if not already started
}

if ( debug ){
  echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><big><big>KATEGOEDT</big></big><br>\n";
}
include_once ("../4fach/katego.php");
include ("../4fcfg/config.inc.php");
include ("../4fcfg/fkt_rolle.inc.php");

$resval_katoedt = !empty($_POST) ? $_POST : (!empty($_GET) ? $_GET : []);

if ( debug ){
  echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><big><big>KATEGOEDT</big></big><br>\n";
  echo "resval_katoedt="; var_dump ($resval_katoedt);    echo "#<br><br>\n";
  echo "GET     ="; var_dump ($_GET);    echo "#<br><br>\n";
  echo "POST    ="; var_dump ($_POST);   echo "#<br><br>\n";
  echo "COOKIE  ="; var_dump ($_COOKIE); echo "#<br><br>\n";
  echo "SESSION ="; var_dump ($_SESSION); echo "#<br>\n";
  echo "SERVER  ="; var_dump ($_SERVER); echo "#<br>\n";
}

$berechtigt = ($_SESSION ["vStab_funktion"] == $redcopy2) ||
              ($_SESSION ["vStab_funktion"] == "Si");


if ( debug ){
  echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "<br></b><big><big>berechtigt = ";
  var_dump($berechtigt); 
  echo "</big></big><br>\n";
} 


if ( debug ){
  echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "<br><b><big><big>*** MARKE ***</b></big></big><br>\n";
}

if ( ( isset ( $resval_katoedt ["4fachkatego_absenden_x"] )) ) {
        // So als erstes muessen wir mal rauskriegen was Überhaupt geändert worden ist.
        $om    = $resval_katoedt ['kategorien_master'] ?? null;
        $of    = $resval_katoedt ['kategorien_fkt'] ?? null;
        $ou    = $resval_katoedt ['kategorien_user'] ?? null; //$ou  = $resval_katoedt ['kategorien_user'];

        $kmo   = $resval_katoedt ['kategorien_master_oben'] ?? null;
        $kfo   = $resval_katoedt ['kategorien_fkt_oben'] ?? null;
        $kuo   = $resval_katoedt ['kategorien_user_oben'] ?? null ;

        $kmu   = $resval_katoedt ['kategorien_master_unten'] ?? null;
        $kfu   = $resval_katoedt ['kategorien_fkt_unten'] ?? null;
        $kuu   = $resval_katoedt ['kategorien_user_unten'] ?? null;
        $neu_m = "";
        $neu_f = "";
        $neu_u = "";

          if (( $kmo != $om ) OR ( $kmu != $om )) { // Master wurde geändert
          if ( $kmo != $om ) { $neu_m = $kmo; }
          elseif ( $kmu != $om ) { $neu_m = $kmu; }
        } else { $neu_m = $om; }

        if (( $kfo != $of ) OR ( $kfu != $of )) { // Funktion wurde geändert
          if ( $kfo != $of ) { $neu_f = $kfo; }
          elseif ( $kfu != $of ) { $neu_f = $kfu; }
        } else { $neu_f = $of; }

        if (( $kuo != $ou ) OR ( $kuu != $ou )) { // User wurde geändert
          if ( $kuo != $ou ) { $neu_u = $kuo; }
          elseif ( $kuu != $ou ) { $neu_u = $kuu; }
        } else { $neu_u = $ou; }


        if (debug){
          echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><big><big>Master, Funktion, User</big></big><br>\n";
          echo "om    =".$om."<br>" ;
          echo "of    =".$of."<br>" ;
          echo "ou    =".$ou."<br>" ;
          echo "kmo   =".$kmo."<br>" ;
          echo "kfo   =".$kfo."<br>" ;
          echo "kuo   =".$kuo."<br>" ;
          echo "kmu   =".$kmu."<br>" ;
          echo "kfu   =".$kfu."<br>" ;
          echo "kuu   =".$kuu."<br>" ;
          echo "neu_m =".$neu_m."<br>" ;
          echo "neu_f =".$neu_f."<br>" ;
          echo "neu_u =".$neu_u."<br>" ;
        }
        // ------------------------- Katego Master --------------------------
      if ($berechtigt) {
        if (debug) { echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ ."</b><big><big>berechtigt == true--</big></big><br>"; }

        $katego = new kategorien ("master");
          // kategorien_master in Tabelle suchen
        
        $result = $katego->db_get_kategobymsg ( $resval_katoedt["msglfd"] );

        if (debug) { 
          echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ ."</b><big><big>berechtigt == true--</big></big><br>"; 
          echo "RESULT="; var_dump ($result); echo"<br>";
        }

        if ($result == false) {
          // für die Nachrichtennummer gibt es keinen Kategorieeintrag
          if (debug) {
            echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ ."</b> 33-- db_get_kategobymsg ( _GET[msglfd] ) == false<br>";
          }
          // insert da es noch keinen Eintrag gibt
          if ($neu_m != "") {
            if (debug) {
              echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ ."</b>36-- _GET [kategorie_master] !=<br>";
            }
            $katego->dblk_neu ( $resval_katoedt["msglfd"], $neu_m );
          }
         
        } ELSE {
            // Kategorie muss geändert werden
          if (debug) {
            echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ ."</b>Kategorie muss geändert werden<br>";
          }
          // insert da es noch keinen Eintrag gibt
          if ($neu_m != "") {
            $katego->dblk_aendern ( $resval_katoedt["msglfd"], $neu_m );
          } ELSE {
            $katego->dblk_loeschen ( $resval_katoedt["msglfd"] );
          }
        }
      }

        // Kategorie FUNKTION
      if (debug) { 
        echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b><big><big>kategorie funktion--</big></big><br>"; 
      }

      $katego = new kategorien ("fkt");
        // kategorien_user in Tabelle suchen
      $result=$katego->db_get_kategobymsg ( $resval_katoedt["msglfd"] );

      if (debug) {
        echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b>"; echo"<br>"; var_dump ($result); echo"<br>";
      }


      if ($result == false) {
        // für die Nachrichtennummer gibt es keinen Kategorieeintrag
        if (debug) {echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b> -- db_get_kategobymsg ( _GET[msglfd] ) == false<br>";}
        // insert da es noch keinen Eintrag gibt
        if ($neu_f != "") {
          $katego->dblk_neu ( $resval_katoedt["msglfd"], $neu_f );
        }
      } ELSE {
        // Kategorie muss geändert werden
        // insert da es noch keinen Eintrag gibt
        if ($neu_f != "") {
          if (debug) { 
            echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b>-- db_get_kategobymsg ( resval_katoedt[msglfd] ) == TRUE<br>";
          }

          $katego->dblk_aendern ( $resval_katoedt["msglfd"], $neu_f );

        } ELSE {

          $katego->dblk_loeschen ( $resval_katoedt["msglfd"] );
        
        }
      }

        // KATEGORIE USER
      if (debug) echo "katgoedt.php 129 kategorie user --<br>";
      $katego = new kategorien ("user");
        // kategorien_user in Tabelle suchen
      $result=$katego->db_get_kategobymsg ( $resval_katoedt["msglfd"]);
      if (debug) {echo "katgoedt 133 RESULT="; var_dump ($result); echo"<br>";}
      if ($result == false) {
        // für die Nachrichtennummer gibt es keinen Kategorieeintrag
        if (debug) {
          echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "</b> db_get_kategobymsg ( $resval_katoedt[msglfd] ) == false<br>";
        
        }
        // insert da es noch keinen Eintrag gibt
        if ($neu_u != "") {
          $katego->dblk_neu ( $resval_katoedt["msglfd"], $neu_u );
        }
      } ELSE {
        // Kategorie muss geändert werden
        // insert da es noch keinen Eintrag gibt
        if ($neu_u != "") {
          if (debug) echo "katgoedt 145 -- db_get_kategobymsg ( _GET[msglfd] ) == TRUE<br>";
          $katego->dblk_aendern ( $resval_katoedt["msglfd"], $neu_u );
        } ELSE {
            $katego->dblk_loeschen ( $resval_katoedt["msglfd"] );
        }
      }

      unset ($_SESSION ["kat_msgno"]);
      unset ($_SESSION ["kat_tbl"]);
      unset ($_SESSION ["kat_no"]);
    }

    if ( ( isset ( $resval_katoedt ["dbtyp"] )) AND
         ( isset ( $resval_katoedt ["msgno"] )) AND
         ( $resval_katoedt ["fkt"] == "edit" )) {
      // Liste der Kategorien bzw Eingabemöglichkeit einer neuen Kategorie
      if ( debug ){
         echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ ;
         echo "<br><b><big><big>*** MARKE ***</b></big></big><br>\n";
      }
      $_SESSION ["kat_msgno"] = $resval_katoedt ["msgno"];
      $_SESSION ["kat_tbl"]   = $resval_katoedt ["dbtyp"];
      $katego = new kategorien ($_SESSION ["kat_tbl"]);
      $katego->liste_kategorien ();
      $katego->eingabezeile ("neu","","","");
    }

    if ( ( isset ( $resval_katoedt ["dbtyp"] )) AND
         ( isset ( $resval_katoedt ["lfd"] )) AND
         ( $resval_katoedt ["kate_todo"] == "editrecord" )) {
      if ( debug ){
         echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "<br><b><big><big>*** MARKE ***</b></big></big><br>\n";
       }
      $_SESSION ["kat_no"] = $resval_katoedt ["lfd"];
      $katego = new kategorien ($_SESSION ["kat_tbl"]);
      $katego->db_get ( $_SESSION ["kat_no"] );
      $katego->eingabezeile ("update",
                             $katego->result ["lfd"],
                             $katego->result ["kategorie"],
                             $katego->result ["beschreibung"]);
    }

    if ( ( isset ( $resval_katoedt ["kate_dbtbl"] )) AND
         ( isset ( $resval_katoedt ["lfd"] )) AND
         ( $resval_katoedt ["kate_todo"] == "update" ) AND
         ( isset ( $resval_katoedt ["lfd"] )) AND
         ( isset ( $resval_katoedt ["kategorie"] )) AND
         ( isset ( $resval_katoedt ["beschreibung"] )) AND
         ( isset ( $resval_katoedt ["katego_absenden_x"] )) ) {
      if ( debug ){
         echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "<br><b><big><big>*** MARKE ***</b></big></big><br>\n";
       }
      $katego = new kategorien ($_SESSION ["kat_tbl"]);
      $katego->db_aendern ($resval_katoedt ["lfd"], $resval_katoedt["kategorie"], $resval_katoedt["beschreibung"] );

       // Liste mit eingabemöglichkeit
      $katego->liste_kategorien ();
      $katego->eingabezeile ("neu","","","");
//          if (!debug)
//      header("Location: ".$_SERVER['PHP_SELF']);
    }

    if ( ( isset ( $resval_katoedt ["katego_abbrechen_x"]  )) OR
         ( ( isset ( $resval_katoedt ["katego_absenden_x"] )) AND
           ( $resval_katoedt ["lfd"] == "" ) AND
           ( $resval_katoedt ["kategorie"] == "" ) AND
           ( $resval_katoedt ["beschreibung"] == "" ) ) ) {
      if (debug) echo "110--<br>";
      // 4fachVordruck
      include_once ("4fachform.php"); // Formular Behandlung 4fach Vordruck
      include_once ("db_operation.php"); // Datenbank operationen
      include_once ("data_hndl.php"); // Schnittstelle zur Datenbank

      $formdata = get_msg_by_lfd ($_SESSION ["kat_msgno"]);

      $form = new nachrichten4fach ($formdata, "Stab_lesen", "");
       // $_SESSION löschen
      unset ($_SESSION ["kat_msgno"]);
      unset ($_SESSION ["kat_tbl"]);
      unset ($_SESSION ["kat_no"]);


    }
    if ( ( isset ( $resval_katoedt ["kate_dbtbl"] )) AND
	       ( isset ( $resval_katoedt ["kate_todo"] )) AND
         (         $resval_katoedt ["kate_todo"] == "neu" ) AND
         ( isset ( $resval_katoedt ["lfd"] )) AND
         (         $resval_katoedt ["kategorie"] != "" ) AND
         ( isset ( $resval_katoedt ["beschreibung"] )) AND
         ( isset ( $resval_katoedt ["katego_absenden_x"] )) ) {
       if ( debug ){
         echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "<br><b><big><big>*** MARKE ***</b></big></big><br>\n";
       }
            // INSERT
       if ( $resval_katoedt ["kategorie"] != "") {
         $katego = new kategorien ($_SESSION ["kat_tbl"]);
         $katego->db_neu($resval_katoedt ["kategorie"], $resval_katoedt ["beschreibung"] );
       }
      header("Location: ".$_SERVER['PHP_SELF']."?dbtyp=".$_SESSION ["kat_tbl"]."&fkt=edit&msgno=".$_SESSION ["kat_msgno"]);
    }
    if ( ( isset ( $resval_katoedt ["dbtyp"] )) AND 
	     ( isset ( $resval_katoedt ["kate_todo"] )) AND
         ( $resval_katoedt ["kate_todo"] == "deleterecord" ) AND
         ( isset ( $resval_katoedt ["lfd"] )) ) {
       if ( debug ){
         echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "<br><b><big><big>*** MARKE ***</b></big></big><br>\n";
       }
            // DELETE
       $katego = new kategorien ($resval_katoedt ["dbtyp"]);
       $katego->db_delete ( $resval_katoedt ["lfd"] );
       switch ($resval_katoedt ["dbtyp"]) {
         case "master":
           if (isset ($_SESSION["ma_katego"])) {
             unset ($_SESSION["ma_katego"]);
             unset ($_SESSION["ma_kategotyp"]);
           }
         break;
         case "fkt":
           if (isset ($_SESSION["fk_katego"])) {
             unset ($_SESSION["fk_katego"]);
             unset ($_SESSION["fk_kategotyp"]);
           }
         break;
         case "user":
           if (isset ($_SESSION["us_katego"])) {
             unset ($_SESSION["us_katego"]);
             unset ($_SESSION["us_kategotyp"]);
           }
         break;
       }
       //if (!debug)
       if ( debug ){
         echo "<b>!File:" . __FILE__ . "  Line:" . __LINE__ . "<br><b><big><big>*** MARKE ***</b></big></big><br>\n";
       }
       header("Location: ".$_SERVER['PHP_SELF']."?dbtyp=".$resval_katoedt ["dbtyp"]."&fkt=edit&msgno=".$_SESSION ["kat_msgno"]);
    }

?>
