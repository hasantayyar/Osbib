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

/**
 * 	Miscellaneous HTML elements
 *
 * 	@author Mark Grimshaw
 *
 * 	$Header: /cvsroot/bibliophile/OSBib/create/MISC.php,v 1.1 2005/06/20 22:26:51 sirfragalot Exp $
 */
class MISC {

// <hr>
    public static function hr($class = FALSE) {
        $string = <<< END
<hr class="$class" />
END;
        return $string . "\n";
    }

// <P>
    public static function p($data = '', $class = FALSE, $align = "left") {
        $string = <<< END
<p class="$class" align="$align">$data</p>
END;
        return $string . "\n";
    }

// <BR>
    public static function br() {
        $string = <<< END
<br />
END;
        return $string . "\n";
    }

// <UL>
    public static function ul($data, $class = FALSE) {
        $string = <<< END
<ul class="$class">$data</ul>
END;
        return $string . "\n";
    }

// <OL>
    public static function ol($data, $class = FALSE, $type = "1") {
        $string = <<< END
<ul class="$class" type="$type">$data</ul>
END;
        return $string . "\n";
    }

// <LI>
    public static function li($data, $class = FALSE) {
        $string = <<< END
<li class="$class">$data</li>
END;
        return $string . "\n";
    }

// <STRONG>
    public static function b($data, $class = FALSE) {
        return <<< END
<strong class="$class">$data</strong>
END;
    }

// <EM>
    public static function i($data, $class = FALSE) {
        return <<< END
<em class="$class">$data</em>
END;
    }

// <U>
    public static function u($data, $class = FALSE) {
        return <<< END
<u class="$class">$data</u>
END;
    }

// <SPAN>
    public static function span($data, $class = FALSE) {
        return <<< END
<span class="$class">$data</span>
END;
    }

// <Hx>
    public static function h($data, $class = FALSE, $level = 4) {
        $tag = 'h' . $level;
        $string = <<< END
<$tag class="$class">$data</$tag>
END;
        return $string . "\n";
    }

// <img>
    public static function img($src, $width, $height, $alt = "") {
        $string = <<< END
<img src="$src" border="0" width="$width" height="$height" alt="$alt" />
END;
        return $string . "\n";
    }

// <A>
    public static function a($class, $label, $link, $target = "_self") {
// NB - no blank line before END;
        return <<< END
<a class="$class" href="$link" target="$target">$label</a>
END;
    }

// <A NAME="...">
    public static function aName($name) {
        $string = <<< END
<a name="$name"></a>
END;
        return $string . "\n";
    }

// <script src="...">
    public static function jsExternal($src) {
        $string = <<< END
<script src="$src" type="text/javascript"></script>
END;
        return $string . "\n";
    }

}
