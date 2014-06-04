<?php

namespace Hasantayyar\Osbib\Create;

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
 * 	Initialize a few default variables before we truly enter the system.
 *
 * *** */

class INIT {

    function __construct() {
// Set to error_reporting(0) before release!!!!!!!!!
// For debugging, set to E_ALL
        error_reporting(E_ALL);
        ob_start();
// make sure that Session output is XHTML conform ('&amp;' instead of '&')
        ini_set('arg_separator.output', '&amp;');
    }

// Make sure we get HTTP VARS in whatever format they come in
    function getVars() {
        return !empty($_POST) ? $_POST : (!empty($_GET) ? $_GET : NULL);
    }

// Add slashes to all incoming GET/POST data.  We now know what we're dealing with and can code accordingly.
    function magicSlashes($element) {
        if (is_array($element))
            return array_map(array("INIT", "magicSlashes"), $element);
        else
            return addslashes($element);
    }

}
