<?php
/******************************************************************************\
|  Mehrfach Schaltergenerator
|  Es sollen Schalter folgender Art hergestellt werden:
|    1. Kippschalter Aus/Ein
|    2. Druckschalter Rund ohne Füllung Aus / mit Füllung (ggf farbig) Ein
|    3. Radioschalter mehrere Schalter nur einer ist aktiv
|    4. Button aktiv und inaktiv.
|
|
\******************************************************************************/

class createbutton {

  var $returnvalue ; // Übergabewert aus GET und POST
  var $type ;
  var $text ;
  var $form  ;
  var $color ;


  var $white ;
  var $yellow ;
  var $blue ;
  var $red ;
  var $green ;
  var $lightyellow ;
  var $lightblue ;
  var $mlightblue ;
  var $lightred ;
  var $lightgreen ;
  var $black ;

  var $tumbler ; // array der Kippschalterparameter
  var $push ;    // array der Druckschalterparameter
  var $radioswt ;   // array der Radioschalterparameter

  var $status ;
  
  var $oncol  ;
  
  var $textpos ;
 
  var $textarr ;

  var $font_size ;
  var $fontheight ;
  var $height ;
  var $width ;

  var $bg ;
  var $textcol ;
  var $bordercol ;

  var $img ;
  var $m_tc ;

/*******************************************************************************\
   Funktion: createbutton ()
   Konstruktor der Klasse createbutton

   Argumente werden mit der $_GET oder $_POST Variable übergeben.

   Beim Radiobutton wird zwischen der Grafikerzeugung und der Übermittlung der
   map-Daten unterschieden.

     1. "tumbler" Kippschalter
       a. status  0 oder 1 für Kippschalter nach oben oder unten
       b. ontext  Beschriftungstext oben
       c. offtext Beschriftungstext unten

     http://SERVER-URL/kats/4fach/buttonxxx.php?type=push&status=EIN&text=erledigt
     2. "push"    Druckschalter
       a. status  0 oder 1 für Druckschalter eingedrückt oder nicht
       b. form    rund oder quadratisch
       c. oncol   on color farbe wenn eingeschaltet
       d. text    Schalterbeschriftung
       e. textpos Textposition rechts, links, drüber oder drunter

     3. "radio"   Radioschalter
       "switches" Schalter
       a. status  ein oder aus
       b. text    Schaltertext
       c. semikolon ; zum Trennen der Schalterabschnitte
         Beispiel: 1,Text1;0,Text2;0,Text3

\********************************************************************************/
/*
    createbutton ("menue", $text, $color, $form);
    1. Parameter: Typ des Knopfes
       tumbler : 
       push     :
       radio
       icon
       menue   :
    2. Parameter: Text
    3. Parameter: Farbe
    4. Parameter: Form (abgerundet, spitz, rechteckig)


*/
  function __construct (){  //"push",$text, $color, $form
    $this->returnvalue = null ;
    //https://y1vdufy7b4g6aez4.myfritz.net/kats/4fach/button.php?type=menue&m_text=abbbrechen&m_fs=15&m_form=rund&bg=blue
    $this->font_size = 5;
     
    // _GET und _POST zu einer Variable zum weitergearbeiten
    $this->returnvalue = !empty($_POST) ? $_POST : (!empty($_GET) ? $_GET : []);

    if ( debug ){
          echo "<br>----------------------------------- Info Block Begin-------------------------------------------<br>\n";
          echo "<b>MARKE </b>".__file__.":".__LINE__."):<br>" ;
          echo "<b>anfang constructor</b>".__file__.":".__LINE__."):<br>" ;
          echo "returnvalue  = "; var_dump ($this->returnvalue); echo "#<br><br>\n";
          echo "<br>----------------------------------- Info Block Ende-------------------------------------------<br>\n";
        }
       
    switch ($this->returnvalue["type"]){
      /************************************** tumbler **************************************************/
      case "tumbler ": //Kippschalter
        $this->tumbler = $this->returnvalue ;
      break;
      case "push":
        if (isset($this->returnvalue ['status'] )){ $this->status = $this->returnvalue ['status'];  }
        if (isset($this->returnvalue ['form']   )){ $this->form   = $this->returnvalue ['form'];    }
        if (isset($this->returnvalue ['oncol']  )){ $this->oncol  = $this->returnvalue ['oncol'];   }
        if (isset($this->returnvalue ['text']   )){ $this->text   = $this->returnvalue ['text'];    }
        if (isset($this->returnvalue ['textpos'])){ $this->textpos= $this->returnvalue ['textpos']; }

        $this->create_push_button ();
      break;
      /**************************************  radio  **************************************************/
      case "radio":
         // einzelne Schalterwerte in swt speichern
        $this->radioswt = explode (";", $this->returnvalue["switches"]);
      break;
      /**************************************  icon  **************************************************/
      case "icon":
        $this->status = $this->returnvalue ['status'];  // EIN oder AUS
           // Hintergrundfarbe
        if (isset($this->returnvalue ['bg']       )){$this->bg  = $this->returnvalue ['bg'] ;} else { $this->bg   = $this->lightblue;}
           // Text farbe
        if (isset($this->returnvalue ['textcol']  )){$this->textcol   = $this->returnvalue ['textcol']  ;}else{ $this->textcol   = $this->black;}
           // Linienfarbe
        if (isset($this->returnvalue ['bordercol'])){$this->bordercol = $this->returnvalue ['bordercol'];}else{ $this->bordercol = $this->black;}
        if (isset ($this->returnvalue ['color']))     {$this->color     = $this->returnvalue ['color']; }
        if (isset ($this->returnvalue ['font_size'])) {$this->font_size = $this->returnvalue ['font_size'] ;  }
        if (isset ($this->returnvalue ['text']))      {$this->text      = $this->returnvalue ['text'];        }
        if (isset ($this->returnvalue ['textpos']))   {$this->textpos   = $this->returnvalue ['textpos'];     } // in or under

        $this->create_icon_button ();

      break;
      /****************************************  menue  ************************************************/
      case "menue":
        
        if ( debug ){
          echo "<br>----------------------------------- Info Block Begin-------------------------------------------<br>\n";
          echo "<b>vor create_menue_button()</b>".__file__.":".__LINE__."):<br>" ;
          echo "returnvalue  = "; var_dump ($this->returnvalue); echo "#<br><br>\n";
          echo "<br>----------------------------------- Info Block Ende-------------------------------------------<br>\n";
        }

        if (isset($this->returnvalue ['bg']))    {$this->m_bg   = $this->returnvalue ['bg']       ;}else{ $this->m_bg      = $this->lightblue;}//Hintergrundfarbe
        if (isset($this->returnvalue ['m_tc']))  {$this->m_tc   = $this->returnvalue ['textcol']  ;}else{ $this->textcol   = $this->black;}// Text farbe
        if (isset($this->returnvalue ['m_bc']))  {$this->m_bc   = $this->returnvalue ['bordercol'];}else{ $this->bordercol = $this->black;}// Linienfarbe
        if (isset($this->returnvalue ['m_form'])){$this->m_form = $this->returnvalue ['m_form'];   }else{ $this->m_form    = "spitz";}
        if (isset($this->returnvalue ['font_size'])) {$this->font_size = $this->returnvalue ['font_size'] ;}
        if (isset($this->returnvalue ['m_text'])){$this->m_text = $this->returnvalue ['m_text'];   }else{ $this->m_text    = "!Textfehler!";}
        if (isset($this->returnvalue ['m_fs']))  {$this->m_fs = $this->returnvalue ['m_fs'];         }   // Fontsize
        if (isset($this->returnvalue ['width'])) {$this->m_width = $this->returnvalue ['width'];    }   // Wunschbreite
        if ( debug ){
          echo "<br>----------------------------------- Info Block Begin-------------------------------------------<br>\n";
          echo "<b>vor create_menue_button()</b>".__file__.":".__LINE__."):<br>" ;
          echo "returnvalue  = "; var_dump ($this->returnvalue); echo "#<br><br>\n";
          echo "<br>----------------------------------- Info Block Ende-------------------------------------------<br>\n";
        }
        $this->create_menue_button ();
      break;
      default: echo __file__.":".__LINE__."<b><big><big><big>!!! DEFAULT !!! </big></big></big></b>";
    }
  }

  function set_color(){
    $this->white       = imagecolorallocate($this->img,255,255,255);
    $this->yellow      = imagecolorallocate($this->img,225,225,150);
    $this->blue        = imagecolorallocate($this->img,150,150,255);
    $this->red         = imagecolorallocate($this->img,255,100,100);
    $this->green       = imagecolorallocate($this->img, 80,255, 80);
    $this->lightyellow = imagecolorallocate($this->img,225,225,200);
    $this->lightblue   = imagecolorallocate($this->img,220,220,255);
    $this->mlightblue  = imagecolorallocate($this->img,220,220,255);
    $this->lightred    = imagecolorallocate($this->img,255,200,200);
    $this->lightgreen  = imagecolorallocate($this->img,200,255,200);
    $this->black       = imagecolorallocate($this->img,  0,  0,  0);
  }

  function get_color ($colorwords){
    switch ($colorwords){
      case "white":       return ($this->white);       break;
      case "yellow":      return ($this->yellow);      break;
      case "blue":        return ($this->blue);        break;
      case "red":         return ($this->red);         break;
      case "green":       return ($this->green);       break;
      case "lightyellow": return ($this->lightyellow); break;
      case "lighterblue":   return ($this->lightblue);   break;
      case "lightblue":   return ($this->lightblue);   break;
      case "mlightblue":  return ($this->mlightblue);  break;
      case "lightred":    return ($this->lightred);    break;
      case "lightgreen":  return ($this->lightgreen);  break;
      case "black":       return ($this->black);       break;
      default:  return ($this->black);
    }
  }

  /*******************************************************************************
   Funktion: katego_icon ()
     $color =
      "blue"       "red"      "yellow"      "green"
      "lightblue"  "lightred" "lightyellow" "lightgreen"

  ********************************************************************************/
  function px($value) {
    return (int) round($value);
  }

  function create_icon_button (){

    $this->height = $this->px(imagefontheight($this->font_size)*1.5);
    $fontwidth    = imagefontwidth ($this->font_size)*strlen($this->text);
    $this->width  = $this->px($fontwidth + $this->height);
    $this->img = imagecreate($this->width, $this->height);
    $this->set_color ();
      // Hintergrundfrage
    imagefill  ( $this->img, 1, 1, $this->get_color ($this->bg) );
    $len=strlen($this->text);
      // Rahmen
    if ($this->status == "EIN"){
      imagerectangle  ( $this->img , 0, 0, $this->width-1 , $this->height-1, $this->get_color ($this->bordercol) ) ;
    } elseif ($this->status == "AUS") {
      imageline ( $this->img ,   0, $this->height-1, 0, 0 , $this->get_color ($this->bordercol) );
      imageline ( $this->img ,   0, 0, $this->width-1,  0, $this->get_color ($this->bordercol) );
      imageline ( $this->img , $this->width-1,  0, $this->width-1,  $this->height-1, $this->get_color ($this->bordercol) );
    }
    $xpos= $this->px(($this->width - $fontwidth) / 2) ;//$i*imagefontwidth($font_size);
    $ypos= $this->px(($this->height - imagefontheight($this->font_size) ) / 2) ;
    imagestring($this->img, $this->font_size, $xpos, $ypos, $this->text, $this->get_color ($this->textcol) );

    header("Content-Type: image/gif");
    imagegif($this->img);
    imagedestroy($this->img);
  }



/*******************************************************************************\
   Funktion: create_push_button ()
     $text  : Text Auf dem Button
     $color : Farbe des Button als
       Array[
         hf : (R,G,B), Hintergrundfarbe
         lf : (R,G,B), Randlinienfarbe
         tf : (R,G,B), Textfarbe
       ]
     $form (ecken, gerundet, elypse)

\********************************************************************************/
  function create_push_button (){
    $switchwidth  = 20 ; // Schalterbreite
    $switchheight = 30 ; // Schalterhöhe
    $xsymcent     = $this->px($switchwidth / 2) ;
    $ysymcent     = $this->px($switchheight *(1-1/3)) ;
    $elliheight   = $this->px($switchheight / 2);
      // wieviele Zeichen sind es
    $lentext     = strlen($this->text);
      // das macht x Pixel
    $fontwidth   = imagefontwidth ($this->font_size)*$lentext + 2 ;
    $fontheight  = $this->px(imagefontheight($this->font_size)*1.5);

    switch ( $this->textpos ){
        // drüber
        // schalterhöhe + Texthöhe
        // Breiteste Schalter oder Text
      case "top":
      break;

        // drunter
        // schalterhöhe + Texthöhe
        // Breiteste Schalter oder Text
      case "buttom":
        $this->height = $switchheight + $fontheight ;
        if ($switchwidth >= $fontwidth){ $this->width = $switchwidth; } else { $this->width = $fontwidth;}
        // x/y Position der oberen linken Ecke des Schalters
        $xpos_img = ($this->width - $switchwidth) / 2 ; // links
        $ypos_img = 0 ; // oben
        // x/y Position des Textes
        $xpos_txt = 1 ;
        $ypos_txt = $switchheight + 1 ;
      break;

        // links daneben
        // schalterbreite + textbreite
        // Größte von schalterhöte oder Texthöhe
      case "left":
        // x/y Position der oberen linken Ecke des Schalters
        $xpos_img = 0 ;
        $ypos_img = 0 ;
        // x/y Position des Textes
        $xpos_txt = 0 ;
        $ypos_txt = 0 ;
      break;

        // rechts daneben
        // schalterbreite + textbreite
        // Größte von schalterhöte oder Texthöhe
      case "right":
        $this->height = $this->fontheight ;
        if ($this->height < $switchheight){ $this->height = $switchheight ; }
        $this->width = $switchwidth + $fontwidth ;// + $this->height;
        // x/y Position der oberen linken Ecke des Schalters
        $xpos_img = 0 ; // links
        $ypos_img = 0 ; // oben
        // x/y Position des Textes
        $xpos_txt = $xpos_img + $switchwidth + 1 ;
        $ypos_txt = $this->px(($this->height - $this->font_size) /2 - 4) ;
      break;

      default:
        $this->height = $switchheight + $fontheight ;
        if ($switchwidth >= $fontwidth){ $this->width = $switchwidth; } else { $this->width = $fontwidth;}
        // x/y Position der oberen linken Ecke des Schalters
        $xpos_img = ($this->width - $switchwidth) / 2 ; // links
        $ypos_img = 0 ; // oben
        // x/y Position des Textes
        $xpos_txt = 1 ;
        $ypos_txt = $switchheight + 1 ;
    }


    $this->width = $this->px($this->width);
    $this->height = $this->px($this->height);
    $xpos_img = $this->px($xpos_img);
    $ypos_img = $this->px($ypos_img);
    $xpos_txt = $this->px($xpos_txt);
    $ypos_txt = $this->px($ypos_txt);
    $stem = $this->px(($switchwidth - 9) / 2);

    $this->img   = imagecreate($this->width,$this->height);
    $this->set_color ($this->oncol);

    if ($this->status == "EIN"){
      // Eingeschaltet ==> grüner Knopf nach unten
      $noerror = imagefilledarc ($this->img, $xpos_img + $xsymcent, $ypos_img + $ysymcent-5,  $switchwidth-9,  $elliheight-9,  0, 360, $this->green, IMG_ARC_PIE);
      $noerror = imagefilledarc ($this->img, $xpos_img + $xsymcent, $ypos_img + $ysymcent-5,  $switchwidth-8,  $elliheight-8,  0, 360, $this->blue, IMG_ARC_NOFILL);
      $noerror = imagefilledarc ($this->img, $xpos_img + $xsymcent, $ypos_img + $ysymcent-5,  $switchwidth-7,  $elliheight-7,  0, 360, $this->blue, IMG_ARC_NOFILL);
    } else {
      // Ausgeschaltet ==> roter Kopf ganz oben
      $noerror = imagefilledarc ($this->img, $xpos_img + $xsymcent, $ypos_img + $ysymcent-12,  $switchwidth-9,  $elliheight-9,  0, 360, $this->red, IMG_ARC_PIE);
      $noerror = imagefilledarc ($this->img, $xpos_img + $xsymcent, $ypos_img + $ysymcent-12,  $switchwidth-8,  $elliheight-8,  0, 360, $this->blue, IMG_ARC_NOFILL);
      $noerror = imagefilledarc ($this->img, $xpos_img + $xsymcent, $ypos_img + $ysymcent-12,  $switchwidth-7,  $elliheight-7,  0, 360, $this->blue, IMG_ARC_NOFILL);
      imageline  ( $this->img, $xpos_img + $xsymcent-$stem, $ypos_img + $ysymcent-12, $xpos_img + $xsymcent-$stem, $ypos_img + $ysymcent , $this->blue ) ;
      imageline  ( $this->img, $xpos_img + $xsymcent-$stem-1, $ypos_img + $ysymcent-12, $xpos_img + $xsymcent-$stem-1, $ypos_img + $ysymcent , $this->blue ) ;
      imageline  ( $this->img, $xpos_img + $xsymcent+$stem, $ypos_img + $ysymcent-12, $xpos_img + $xsymcent+$stem, $ypos_img + $ysymcent , $this->blue ) ;
      imageline  ( $this->img, $xpos_img + $xsymcent+$stem+1, $ypos_img + $ysymcent-12, $xpos_img + $xsymcent+$stem+1, $ypos_img + $ysymcent , $this->blue ) ;
    }
    // Untere Ebene innere Kreise
    $noerror = imagefilledarc ($this->img, $xpos_img + $xsymcent,  $ysymcent,  $switchwidth-8,  $elliheight-8,  0, 180, $this->blue, IMG_ARC_NOFILL);
    $noerror = imagefilledarc ($this->img, $xpos_img + $xsymcent,  $ysymcent,  $switchwidth-7,  $elliheight-7,  0, 180, $this->blue, IMG_ARC_NOFILL);
    $noerror = imagefilledarc ($this->img, $xpos_img + $xsymcent,  $ysymcent,  $switchwidth-2,  $elliheight-2,  320, 220, $this->blue, IMG_ARC_NOFILL);
    $noerror = imagefilledarc ($this->img, $xpos_img + $xsymcent,  $ysymcent,  $switchwidth-1,  $elliheight-1,  320, 220, $this->blue, IMG_ARC_NOFILL);
    $noerror = imagefilledarc ($this->img, $xpos_img + $xsymcent,  $ysymcent,  $switchwidth  ,  $elliheight  ,  320, 220, $this->blue, IMG_ARC_NOFILL);

    imagefill  ( $this->img, 1, 1, $this->lightblue );
    imagestring($this->img,$this->font_size,$xpos_txt,$ypos_txt,$this->text,$this->black) ; //textcol);


    header("Content-Type: image/gif");
    imagegif($this->img);
    imagedestroy($this->img);

  }




/*******************************************************************************\
   Funktion: create_radio_button ()
     $textarr : Text Auf den Button

\********************************************************************************/
  function create_radio_button (){  }

  var $m_form ; // Form des Tasters rund, spitz, eckig
  var $m_text ; // Text des Tasters
  var $m_bg ;   // Hintergrundfarbe im mittleren Bereich
  var $m_bc ;   // Randfarbe
  var $m_pc ;   // Farbe der Umgebung
  var $m_font ; // Zeichensatz
  var $m_fs ;   // Schriftgrösse
  var $m_width; // Breite


  function create_menue_button (){
    if ( debug ){ echo "<b>MARKE</b>".__file__.":".__LINE__."):<br>" ; }
    $this->m_font = dirname(__DIR__)."/4fbak/fonts/NimbusSans-Bold.otf"; // Helvetica (PDF-Standardschrift)
    if (!is_readable($this->m_font)) {
      $fallbackFont = "/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf";
      if (is_readable($fallbackFont)) {
        $this->m_font = $fallbackFont;
      }
    }
    $this->m_fs = (float) $this->m_fs;

    $b = imageTTFBbox($this->m_fs, 0, $this->m_font, $this->m_text);  //errechnet die Größe des Bereichs für eine True-Type-Textausgabe
/*  b[6],b[7] O---------------------O b[4],b[5]
              |                     |
    b[0],b[1] O---------------------O b[2],b[3]  */

    $txt_dx = abs ($b[4]) + abs ($b[6]) ;           // Breite des Textes
    $ober   = abs ($this->m_fs - abs ($b[7])+5) ; // Schriftgrösse - Y-Wert oben
    $mittel = $this->m_fs;                                // Schriftgrösse
    $unter  = abs ($b[1])-2;                    // Y-Wert unten - 2 Pixel

    $txt_dy = $ober + $mittel + $unter ;

    $this->height = (int) round($txt_dy + 6);
    $this->width  = (int) round($txt_dx + $ober + $mittel);
    if ( $this->m_width > $this->width ){ $this->width = (int) round($this->m_width); }
    $this->img   = imagecreate($this->width, $this->height);
    $this->set_color ($this->oncol);

    switch ($this->m_form){
      case "spitz":
          // spitz seiten
          // 1. Strich links oben nach rechts oben vom Rand 10 Punkte
            /*    x4 x1                x2 x3
              x1|y1 /------------------\ x2|y1      y1
                   /                    \
            x4|y2 *                      * x3|y2    y2
                   \                    /
              x1|y3 \------------------/ x2|y3      y3
            */
        $x1 = 10 ;
        $x2 = (int) round($this->width - $this->height / 2);
        $x3 = $this->width - 1;
        $x4 = 1 ;
        $y1 = 1 ;
        $y2 = (int) round($this->height / 2);
        $y3 = $this->height - 1;

        imageline  ( $this->img, $x1, $y1, $x2, $y1, $this->black ) ;
        imageline  ( $this->img, $x2, $y1, $x3, $y2, $this->black ) ;
        imageline  ( $this->img, $x3, $y2, $x2, $y3, $this->black ) ;
        imageline  ( $this->img, $x2, $y3, $x1, $y3, $this->black ) ;
        imageline  ( $this->img, $x1, $y3, $x4, $y2, $this->black ) ;
        imageline  ( $this->img, $x4, $y2, $x1, $y1, $this->black ) ;

        imagefill  ( $this->img, 1, 1, $this->lightblue );
      break;

      case "rund":
            /*
              x1|y1 ------------------ x2|y1      y1
                    *                *
              x1|y2 ------------------ x2|y2      y3
            */
        $x1 = (int) round($this->height / 2);
        $x2 = (int) round($this->width - $this->height / 2 - 1);

        $y1 = 0 ;
        $y2 = (int) round($this->height - 1);
        $y3 = (int) round($y2 / 2);
        $arc = (int) round($this->height);

        imageline  ( $this->img, $x1, $y1, $x2, $y1, $this->black ) ;
        imageline  ( $this->img, $x1, $y2, $x2, $y2, $this->black ) ;

        imagearc  ( $this->img, $x1, $y3, $arc, $arc, 90 , 270, $this->black  );
        imagearc  ( $this->img, $x2, $y3, $arc, $arc, 270, 450, $this->black  );

        imagefill  ( $this->img,            1,   1, $this->get_color ($this->m_bg) );
        imagefill  ( $this->img,            1, $y2, $this->get_color ($this->m_bg) );
        imagefill  ( $this->img, $this->width,   1, $this->get_color ($this->m_bg) );
        imagefill  ( $this->img, $this->width, $y2, $this->get_color ($this->m_bg) );
      break;

    }

    $bo = imagettftext ( $this->img, $this->m_fs, 0, (int) round($x1 - 3), (int) round($ober + $mittel + $unter + 1), $this->black, $this->m_font,  $this->m_text );

    header("Content-Type: image/gif");
    imagegif($this->img);
    imagedestroy($this->img);
  }
} // class button
/*******************************************************************************
*******************************************************************************/


define ("debug", false);

$from_uri = !empty($_POST) ? $_POST : (!empty($_GET) ? $_GET : []);

if ( debug ){
  echo "<br>----------------------------------- Info Block Begin-------------------------------------------<br>\n";
  echo "<b>fkt_rolle</b>".__file__.":".__LINE__."):<br>" ;
  echo "from_uri="; var_dump ($from_uri);    echo "#<br><br>\n";
  echo "GET="; var_dump ($_GET);    echo "#<br><br>\n";
  echo "POST="; var_dump ($_POST);   echo "#<br><br>\n";
  echo "COOKIE="; var_dump ($_COOKIE); echo "#<br><br>\n";
  if (isset($_SESSION)) {echo "SESSION="; var_dump ($_SESSION); echo "#<br>\n";}
  echo "SERVER  ="; var_dump ($_SERVER); echo "#<br>\n";
  echo "<br>----------------------------------- Info Block Ende-------------------------------------------<br>\n";
}

  if (isset ($from_uri["type"])){
    $form = $from_uri["m_form"] ?? ($from_uri["form"] ?? "");
    switch ($from_uri["type"]){
      /****************************************************************************************/
      case "tumbler":
        if (isset ( $from_uri ["ontext"] )) { $ontext = $from_uri ["ontext"];   } else { $ontext = "!FEHLER!"; }
        if (isset ( $from_uri ["offtext"] )) {$offtext = $from_uri ["offtext"]; } else { $offtext = "!FEHLER!"; }
        if (isset ( $from_uri ["color"] )) {  $color = $from_uri ["color"];     } else { $color = "blue"; }
        $button = new createbutton ("push",$text, $color, $form);
        break ;
      /****************************************************************************************/
      case "push":
        if (isset ( $from_uri ["text"] )) { $text = $from_uri ["text"];   } else { $text = "!FEHLER!"; }
        if (isset ( $from_uri ["color"] )) {$color = $from_uri ["color"]; } else { $color = "blue"; }
        $button = new createbutton ("push", $text, $color, debug);
        break ;
      /****************************************************************************************/
      case "radio":
        $textarr = explode(',', $text);
        $text = "!FEHLER!";

      break ;
      /****************************************************************************************/
      case "icon":
        $text = "";
	     $color = "";
	     $form = "";
        if (isset ($from_uri ['color'])) { $color  = $from_uri ['color'];  }
        if (isset ($from_uri ['form']))  { $form   = $from_uri ['form'] ;  }
        if (isset ($from_uri ['text']))  { $text   = $from_uri ['text'];   }

        $button = new createbutton ("icon",$text, $color, $form);
        break ;
      /****************************************************************************************/
      case "menue":
        
        if ( debug ){ echo "<b>fkt_rolle</b>".__file__.":".__LINE__."):<br>" ; }
        
        if (isset ( $from_uri ["m_text"] )) {
          $text = $from_uri ["m_text"];
        } else { $text = "!FEHLER!"; }
        
        if ( debug ){ 
          echo "<b>button.php</b>".__file__.":".__LINE__."):<br>" ; 
          echo "text = "; var_dump ($text); echo "#<br><br>\n";
        }
        
        if (isset ( $from_uri ["color"] )) {
          $color = $from_uri ["color"];
        } else {
          $color = "blue";
        }
        if ( debug ){ echo "<b>MARKE</b>".__file__.":".__LINE__."):<br>" ; }
        $button = new createbutton ("menue", $text, $color, $form);
        
        break ;
      /****************************************************************************************/
      default: echo __file__.":".__LINE__."<b><big><big><big>!!! DEFAULT !!! </big></big></big></b>";
    }
  }
  

if ( debug ){
  echo "<br>----------------------------------- Info Block Begin-------------------------------------------<br>\n";
  echo "<b>fkt_rolle</b>".__file__.":".__LINE__."):<br>" ;
  echo "GET="; var_dump ($_GET);    echo "#<br><br>\n";
  echo "POST="; var_dump ($_POST);   echo "#<br><br>\n";
  echo "COOKIE="; var_dump ($_COOKIE); echo "#<br><br>\n";
  if (isset($_SESSION)) echo "SESSION="; var_dump ($_SESSION); echo "#<br>\n";
  echo "<br>----------------------------------- Info Block Ende-------------------------------------------<br>\n";
}

?>