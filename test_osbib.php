<?php

namespace Hasantayyar\Osbib;

use Hasantayyar\Osbib\Format\TestOsbib;

include __DIR__ . '/vendor/autoload.php';

$useStyle = loadStyle();
$testosbib = new TestOsbib($useStyle);
$testosbib->execute();
die; // exit
// External to class
// Load styles and print select box.

function loadStyle() {

    $styles = LOADSTYLE::loadDir("styles/bibliography");
    $styleKeys = array_keys($styles);
    print "<h2><font color='red'>OSBIB Bibliographic Formatting (Quick Test)</font></h2>";
    print "<table width=\"100%\" border=\"0\"><tr><td>";
    print "<form method = \"POST\">";
    print "<select name=\"style\" id=\"style\" size=\"10\">";
    if (array_key_exists('style', $_POST)) {
        $useStyle = $_POST['style'];
    } else {
        $useStyle = $styleKeys[0];
    }
    foreach ($styles as $style => $value) {
        print $style == $useStyle ?
                        "<option value=\"$style\" selected = \"selected\">$value</option>" :
                        "<option value=\"$style\">$value</option>";
    }
    print "</select>";
    print "<br /><input type=\"submit\" value=\"SUBMIT\" />";
    print "</form><td>";
    print "<td align=\"right\" valign=\"top\"> </td></tr></table>";
    return $useStyle;
}
