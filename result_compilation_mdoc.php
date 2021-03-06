<?php

/*! \file result_compilation_mdoc.php
  \brief This file display the automatic documentation generated by
  the Faust compiler
  \author Romain Michon

  Copyright (C) 2003-2011 GRAME, Centre National de Creation Musicale
  ---------------------------------------------------------------------
  This file is free software; you can redistribute it
  and/or modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; either version 3 of
  the License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; If not, see <http://www.gnu.org/licenses/>.

  EXCEPTION : As a special exception, you may create a larger work
  that contains this FAUST architecture section and distribute
  that work under terms of your choice, so long as this FAUST
  architecture section is not modified.
*/

//session is started
if (session_id()=="") session_start();

require("php/env.php");
require "php/functions.php";
require "php/form.php";
require "php/getfile.php";
require "inc/mess.inc";
require "php/make_element.php";

//create a new session folder if it doesn't exist
if($_SESSION['id'] == "") $_SESSION['id'] = session_id();
$_SESSION['path'] = $_SERVER['DOCUMENT_ROOT']."/onlinecompiler/tmp/".$_SESSION['id'];
system("scripts/new_session ".$_SESSION['path'], $ret);
if ($ret != 0) {erreur("result_compilation_mdoc.php: Unable to start a new session. Please, try later."); return 1;}

//download the pdf file
if ($_POST['submit'] == "Get the documentation") {
  getDocPdf();
  exit;
}

//global variable to set the position of the frame on navigation bar
$_SESSION['goto'] = "mdoc";

//get the code of compiler.html
if ($_SESSION['htmlCode'] == "" ) $_SESSION['htmlCode'] = read_file ("compiler.html");
$html = $_SESSION['htmlCode'];
$resultat = get_section($html, "resultatmdoc");

if($_SESSION['fullScreenMode'] == 1) $boxWidth = 820;
else $boxWidth = 690;

//the html page is filled, google doc viewer is used to display the pdf file
if ($_SESSION['resultat_mdoc'] == 1)
{
    error_log("SERVER : ".$_SERVER['DOCUMENT_ROOT'],0);
    $mdoc = get_section ($html, "mdoc");
    $googleViewer = "<iframe src=\"http://docs.google.com/viewer?url=http%3A%2F%2Ffaust.grame.fr%2Fonlinecompiler%2Ftmp%2F".$_SESSION['id']."%2Fworkdir%2F".$_SESSION['appli_name']."-mdoc%2Fpdf%2F".$_SESSION['appli_name'].$_SESSION['randMdoc'].".pdf&embedded=true\" height=\"780\" style=\"border: none;\"></iframe>";
    $assoc['__resultat__'] =  $googleViewer;
} else {
  if($_SESSION['code_faust'] == "Enter Faust code here") {
      $assoc['__resultat__'] = "Please, enter some Faust code in the \"Faust Code\" tab or drag a Faust file in the area above.";
  } else {
      $assoc['__resultat__'] =  "Woops, something went wrong... Please check the Faust code.";
  }
}
$resultat = fill_template($resultat, $assoc);

//the html page is displayed
display_header($html);
// display_catalog($html,"goto_mdoc.php");
display_dropFile($html,"document.location.replace(\"goto_mdoc.php\")");
display_navigation($html,0);
print $mdoc ;
print $resultat ;
display_footer($html);

?>
