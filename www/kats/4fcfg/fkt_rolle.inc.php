<?php
if (!defined('debug')) {   // true = gibt debuginformationen aus
    define('debug', false);
}


if ( debug ){
  echo "<br><big><big>##########################################  fkt_rolle.inc.php  #####################################################</big></big><br>\n";
  echo "<b>fkt_rolle</b>".__file__.":".__LINE__."):<br>" ;
  echo "<br><br>\n";
  echo "GET="; var_dump ($_GET);    echo "#<br><br>\n";
  echo "POST="; var_dump ($_POST);   echo "#<br><br>\n";
  echo "COOKIE="; var_dump ($_COOKIE); echo "#<br><br>\n";
  /*if (isset($_SESSION))*/ 
  echo "SESSION="; var_dump ($_SESSION); echo "<br>\n";
  echo "<br>----------------------------------- Info Block Ende-------------------------------------------<br>\n";
}

/******************************************************************************\
       Definition der Mitspieler
|-----|-----|-----|-----|
| 1.1 | 2.1 | 3.1 | 4.1 |
| 1.2 | 2.2 | 3.2 | 4.2 |
| 1.3 | 2.3 | 3.3 | 4.3 |
| 1.4 | 2.4 | 3.4 | 4.4 |
| 1.5 | 2.5 | 3.5 | 4.5 |
|-----|-----|-----|-----|
\******************************************************************************/
//              lfd-Nr      PosForm  Fkt
//                       Spalte,Zeile          Roll
    include ("../4fcfg/config.inc.php");
    include ("../4fcfg/dbcfg.inc.php");
    include ("../4fcfg/e_cfg.inc.php");


    $conf_empf = [] ;



    //public $db_lnk ;    
//    include_once ("../4fach/db_operation.php");

// echo "<h1>fkt_rolle.inc.php</h1>";
/*
    $dbaccess = new db_access ($conf_4f_db  ["server"],
                               $conf_4f_db  ["datenbank"],
                               $conf_4f_tbl ["benutzer"],
                               $conf_4f_db  ["user"],
                               $conf_4f_db  ["password"] );

echo "DBDATA--".$conf_4f_db  ["server"]." - ".$conf_4f_db  ["datenbank"]." - ".
                               $conf_4f_tbl ["benutzer"]." - ".
                               $conf_4f_db  ["user"]." - ".
                               $conf_4f_db  ["password"]."<br>" ;

*/
    $db_lnk = mysqli_connect($conf_4f_db  ["server"],$conf_4f_db ["user"], $conf_4f_db  ["password"])
       or die ("[query_table]34 Konnte keine Verbindung zur Datenbank herstellen".mysqli_error($db_lmk)." ".mysqli_errno($db_lnk));
       
    if ( debug ){  echo "<b>db_check</b>".__file__.":".__LINE__."):".$conf_4f_db  ["datenbank"]."<br>" ;}

    $db_check = mysqli_select_db ($db_lnk, $conf_4f_db  ["datenbank"]);
//       or die ("[query_table]37 Auswahl der Datenbank fehlgeschlagen".mysqli_error($db)." ".mysqli_errno($db));
       
    if ( debug ){  echo "<b>db_check</b>".__file__.":".__LINE__."): <br>" ;}       
       
    if (mysqli_connect_errno()) {
      printf("Connect failed: %s\n", mysqli_connect_error());
      exit();
    }
      
      if ( debug ){  echo "<b>db_check</b>".__file__.":".__LINE__."): <br>" ;}

      $query = "SELECT
                 mtx_x as `m`,
                 mtx_y as `n`,
                 mtx_typ as `typ`,
                 mtx_fkt as `fkt`,
                 mtx_rolle as `rolle`,
                 mtx_mode as `mode`,
                 mtx_rc2 as `redcopy`,
                 mtx_auto as `auto` FROM ".$conf_4f_tbl   ["empfmtx"]." WHERE 1 ;" ;

    $query_result = mysqli_query ($db_lnk, $query) or
       die("<b>db_check</b>".__file__.":".__LINE__."): ".mysqli_error($db_lnk)." ".mysqli_errno($db_lnk))."<br>";
       
    if ( debug){
        	  echo "<b>!File:". __FILE__ ."  Line:". __LINE__ ."</b><br>";
            echo "<br><br>\n------------------------Info Block Start------------------------------<br>\n";
            echo "query = $query <br>\n";            
            echo "result = "; var_dump ($query_result);    echo "<br>\n";
            echo "<br>\n------------------------Info Block Ende------------------------------<br>\n";
          } 
    if ( debug ){  echo "<b>db_check</b>".__file__.":".__LINE__."): <br>" ; echo "QUERY = ".$query."<br><br>";}

    $resultcount = mysqli_num_rows($query_result);
    
    $result = NULL;
    for ($i=1;$i<=$resultcount;$i++){
      $result[$i] = mysqli_fetch_array($query_result);
    }

    if ( debug ){  echo "<b>db_check</b>".__file__.":".__LINE__."): <br>" ;	
       echo "<big>resultcount = ";var_dump ($resultcount); echo "</big><br>"; 
       //echo "fetch_array = " ; var_dump ($result) ; echo "<br>";
    }

    mysqli_free_result($query_result);

      // Voreinstellung für die leere Tabelle
    for ($i=1; $i<=5; $i++){
      for ($j=1; $j<=4; $j++){
         $empf_matrix [$i][$j] = array(
                           "typ" => "t",
                           "fkt" => "",
                           "rolle" => "leer",
                           "mode" => "ro",
                           "auto" =>  "f");
      }
    }

    if ( debug ){  echo "<b>db_check</b>".__file__.":".__LINE__."): <br>" ;	
       echo "<big>resultcount = $resultcount</big><br>"; 
       //       echo "fetch_array = " ; var_dump ($result) ; echo "<br>";
    }

    if ($result != ""){
      foreach ($result as $fktdata){

        if ( debug ){  
           echo "<b>db_check</b>".__file__.":".__LINE__."): <br>" ;	
           //  echo "fktdata = " ; var_dump ($fktdata) ; echo "<br>";
        }

        $empf_matrix [$fktdata["m"]] [$fktdata["n"]] = array(
                       "typ"   => $fktdata["typ"],
                       "fkt"   => $fktdata["fkt"],
                       "rolle" => $fktdata["rolle"],
                       "mode"  => $fktdata["mode"],
                       "auto"  => $fktdata["auto"] ) ;
        if ( ($fktdata["redcopy"] == "t") or
             ($fktdata["redcopy"] == "1")){ $redcopy2 = $fktdata ["fkt"] ;}
      }

      $db_lnk = mysqli_connect($conf_4f_db  ["server"],$conf_4f_db ["user"], $conf_4f_db  ["password"])
         or die ("[query_table] Konnte keine Verbindung zur Datenbank herstellen");

      $db_check = mysqli_select_db ($db_lnk, $conf_4f_db  ["datenbank"])
         or die ("[query_table] Auswahl der Datenbank fehlgeschlagen");

      $query = "SELECT
                 mtx_x as `m`,
                 mtx_y as `n`,
                 mtx_typ as `typ`,
                 mtx_fkt as `fkt`,
                 mtx_rolle as `rolle`,
                 mtx_mode as `mode`,
                 mtx_rc2 as `redcopy`,
                 mtx_auto as `auto` FROM ".$conf_4f_tbl   ["empfmtx"]." WHERE 1 ;" ;

      $query_result = mysqli_query ($db_lnk, $query) or
         die("[query_table] <br>$query<br>103-".mysqli_error($db_lnk)." ".mysqli_errno($db_lnk));

      if ( debug){
        	  echo "<b>!File:". __FILE__ ."  Line:". __LINE__ ."</b><br>";
            echo "<br><br>\n------------------------Info Block Start------------------------------<br>\n";
            echo "query = $query <br>\n";            
            echo "result = "; var_dump ($query_result);    echo "<br>\n";
            echo "<br>\n------------------------Info Block Ende------------------------------<br>\n";
      } 

      $resultcount = mysqli_num_rows($query_result);

      for ($i=1;$i<=$resultcount;$i++){
        $result[$i] = mysqli_fetch_assoc($query_result);
      }
      
      if ( debug ){  echo "<b>db_check</b>".__file__.":".__LINE__."): <br>" ;	
         echo "<big>resultcount = $resultcount</big><br>"; 
         // echo "fetch_assoc = " ; var_dump ($result) ; echo "<br>";
      }

      mysqli_free_result($query_result);

       // Voreinstellung für die leere Tabelle
     $stab = NULL;
     $fb   = Null;
     $i    = 0 ;
     $j    = 0 ;
     foreach ($result as $fktdata){
       if ($fktdata ["fkt"] != "" ) {
         switch ($fktdata["rolle"]){
           case "Stab":
             $stab [$i++] = $fktdata ["fkt"] ;
           break;
           case "FB":
             $fb   [$j++] = $fktdata ["fkt"] ;
           break;
         }
       }
     }

     if ( debug ){  
       echo "<b>db_check</b>".__file__.":".__LINE__."): <br>" ;	
       echo "conf_empf = " ; var_dump ($conf_empf) ; echo "<br>";
       echo "STAB="; var_dump ($stab); echo "<br>";
       echo "FB  ="; var_dump ($fb); echo "<br>";
     }

     if ($stab != NULL) sort ($stab);
     if ($fb   != NULL) sort ($fb);

     if ( debug ){  
       echo "<b>db_check</b>".__file__.":".__LINE__."): <br>" ;	
       echo "conf_empf = " ; var_dump ($conf_empf) ; echo "<br>";
       echo "STAB="; var_dump ($stab); echo "<br>";
       echo "FB  ="; var_dump ($fb); echo "<br>";
     }
     $conf_empf = [] ;
     $i = 1;
     if ($stab != NULL){
       foreach ($stab as $fktdata){
         $conf_empf [$i++] = array(
                             "fkt"   => $fktdata,
                             "rolle" => "Stab" );
       }
     }
       // Sichter und Fernmelder dazu
     $conf_empf [$i++] = array ("fkt" => "Si",  "rolle" => "Stab" );
     $conf_empf [$i++] = array ("fkt" => "A/W", "rolle" => "Fernmelder" );
     if ($fb != NULL){
       foreach ($fb as $fktdata){
         $conf_empf [$i++] = array(
                             "fkt"   => $fktdata,
                             "rolle" => "FB" );
       }
     }
     if ( debug ){  
       echo "<b>db_check</b>".__file__.":".__LINE__."): <br>" ;	
       echo "conf_empf = " ; var_dump ($conf_empf) ; echo "<br>";
     }

   }


   if ( debug ){  
     echo "<b>Ende fkt_rolle_inc-php </b>".__file__.":".__LINE__."): fkt_rolle.inc.php ===   "; 
     var_dump ($empf_matrix); 
     echo "Das Letzte<br><br>";
     echo "<br><big><big>##########################################  fkt_rolle.inc.php  #####################################################</big></big><br>\n";
   }



?>