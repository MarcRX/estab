<?php 
session_start ();
/*****************************************************************************\
   Datei: index.php

   Beschreibung:

          In dieser Datei wird der Frameset eingerichtet.
          links einStreifen mit der status.php
          rest die Datei mainindex.php

   (C) Hajo Landmesser
   mailto://hajo.landmesser@oberbruch.de
   
   +------------------+-----------------------------+
|                  |                             |
|                  |                             |
|   counter.php    |                             |
|   (150px hoch)   |                             |
|------------------|        mainindex.php        |
|   vorgaben.php   |                             |
|   (150px hoch)   |                             |
|------------------|                             |
|   status.php     |                             |
|                  |                             |
+------------------+-----------------------------+
   100px breit          Restbreite

*****************************************************************************/
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<link REL="SHORTCUT ICON" HREF="../favicon.ico" />
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
<meta name="author" content="hajo" >
<meta name="generator" content="Bluefish 2.2.12" >
<TITLE>Nachrichtenvordruck</TITLE>
<FRAMESET COLS="100,*" frameborder="1" framespacing="0" border="1">
   <FRAMESET ROWS="150,*" frameborder="1" framespacing="0" border="1">
      <FRAME NAME="counter" TITLE="counter" SRC="./counter.php" SCROLLING=NO MARGINWIDTH="0" MARGINHEIGHT="0" FRAMEBORDER="1" NORESIZE>
         <FRAMESET ROWS="150,*" frameborder="1" framespacing="0" border="1">
           <FRAME NAME="vorgaben" TITLE="vorgaben" SRC="./vorgaben.php" SCROLLING=NO MARGINWIDTH="0" MARGINHEIGHT="0" FRAMEBORDER="1" NORESIZE>
           <FRAME NAME="status" TITLE="status" SRC="./status.php" SCROLLING=NO MARGINWIDTH="0" MARGINHEIGHT="0" FRAMEBORDER="1" NORESIZE>
         </FRAMESET>
   </FRAMESET>
   <FRAME NAME="mainframe" TITLE="mainframe" SRC="./mainindex.php" SCROLLING=AUTO MARGINWIDTH="3" MARGINHEIGHT="3" FRAMEBORDER="1">
</FRAMESET>
</HEAD>
</HTML>
