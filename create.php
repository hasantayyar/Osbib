<?php

namespace Hasantayyar\Osbib;

include __DIR__ . '/vendor/autoload.php';

use \Hasantayyar\Osbib\Create\ERRORS;
use Hasantayyar\Osbib\Create\INIT;
use Hasantayyar\Osbib\Create\ADMINSTYLE;
use Hasantayyar\Osbib\Create\CLOSE;
use Hasantayyar\Osbib\Create\CLOSEPOPUP;
use Hasantayyar\Osbib\Create\PREVIEWSTYLE;

session_start();
/* * ******************************
  OSBib:
  A collection of PHP classes to create and manage bibliographic formatting for OS bibliography software
  using the OSBib standard.

  Released through http://bibliophile.sourceforge.net under the GPL licence.
  Do whatever you like with this -- some credit to the author(s) would be appreciated.

  If you make improvements, please consider contacting the administrators at bibliophile.sourceforge.net
  so that your improvements can be added to the release package.

  Adapted from WIKINDX: http://wikindx.sourceforge.net

  Mark Grimshaw 2005
  http://bibliophile.sourceforge.net
 * ****************************** */

/* * ***
 * index.php 
 */
// Path to where the XML style files are kept.
define("OSBIB_STYLE_DIR", "styles/bibliography"); // CB


/**
 * Initialise
 */
$errors = new ERRORS();
$init = new INIT();
// Get user input in whatever form
$vars = $init->getVars();

if (!$vars) {
    $admin = new ADMINSTYLE($vars);
    $pString = $admin->gateKeep('display');
} else if ($vars["action"] == 'adminStyleAddInit') {
    $admin = new ADMINSTYLE($vars);
    $pString = $admin->gateKeep('addInit');
} else if ($vars["action"] == 'adminStyleAdd') {
    $admin = new ADMINSTYLE($vars);
    $pString = $admin->gateKeep('add');
} else if ($vars["action"] == 'adminStyleEditInit') {
    $admin = new ADMINSTYLE($vars);
    $pString = $admin->gateKeep('editInit');
} else if ($vars["action"] == 'adminStyleEditDisplay') {
    $admin = new ADMINSTYLE($vars);
    $pString = $admin->gateKeep('editDisplay');
} else if ($vars["action"] == 'adminStyleEdit') {
    $admin = new ADMINSTYLE($vars);
    $pString = $admin->gateKeep('edit');
} else if ($vars["action"] == 'adminStyleCopyInit') {
    $admin = new ADMINSTYLE($vars);
    $pString = $admin->gateKeep('copyInit');
} else if ($vars["action"] == 'adminStyleCopyDisplay') {
    $admin = new ADMINSTYLE($vars);
    $pString = $admin->gateKeep('copyDisplay');
} else if ($vars["action"] == 'previewStyle') {
    //$pString = print_r($vars);
    $preview = new PREVIEWSTYLE($vars);
    $pString = $preview->display();
    new CLOSEPOPUP($pString);
} else if ($vars["action"] == 'help') {
    $help = new HELPSTYLE();
    $pString = $help->display();
    new CLOSE($pString, FALSE);
} else {
    $pString = $errors->text("inputError", "invalid");
}
/* * ***
 * 	Close the HTML code by calling the constructor of CLOSE which also 
 * 	prints the HTTP header, body and flushes the print buffer.
 * *** */
new CLOSE($pString);
