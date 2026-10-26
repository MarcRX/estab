<?php 
/******************************************************************************\ 
              Definitionen fuer den Datenbankzugriff                              
\******************************************************************************/ 
$conf_4f_db   ["server"]        = getenv("ESTAB_DB_HOST") ?: "localhost"; 
$conf_4f_db   ["user"]          = getenv("MARIADB_USER") ?: "estabuser"; 
$conf_4f_db   ["password"]      = getenv("MARIADB_PASSWORD") ?: ""; 
$conf_4f_tbl  ["prefix"]        = "es" ; 
$conf_4f_tbl  ["benutzer"]      = "esbenutzer"; 
$conf_4f_tbl  ["masterkatego"]  = "esmasterkatego"; 
$conf_4f_tbl  ["masterkategolk"]= "esmasterkategolink"; 
$conf_4f_tbl  ["nachrichten"]   = "esnachrichten"; 
$conf_4f_tbl  ["empfmtx"]       = "esempfmtx"; 
$conf_4f_tbl  ["protokoll"]     = "esprotokoll"; 
$conf_4f_tbl  ["anhang"]        = "esanhang"; 
$conf_4f_tbl  ["usrtblprefix"]  = "usr_"; 
$conf_tbl     ["bhp50"]         = "esbhp50"; 
$conf_tbl     ["komplan"]       = "eskomplan"; 
$conf_tbl     ["etb"]           = "esetb"; 
$conf_tbl     ["tbb"]           = "estbb"; 
$conf_tbl     ["ubb"]           = "esubb"; 




?>