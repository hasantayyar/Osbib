<?php

namespace Hasantayyar\Osbib;

/* * ******************************
  OSBib:
  A collection of PHP classes to create and manage bibliographic formatting for OS bibliography software
  using the OSBib standard.

  Released through http://bibliophile.sourceforge.net under the GPL licence.
  Do whatever you like with this -- some credit to the author(s) would be appreciated.

  If you make improvements, please consider contacting the administrators at bibliophile.sourceforge.net
  so that your improvements can be added to the release package.

  Mark Grimshaw 2005
  http://bibliophile.sourceforge.net
 * ****************************** */
/* * ***
 * UTF-8 class
 *
 * *** */

class UTF8 {

// Constructor
    function __construct() {
        $this->loadVars();
    }

// Decode UTF-8 ONLY if the input has been UTF-8-encoded.
// Adapted from 'nospam' in the user contributions at:
// http://www.php.net/manual/en/function.utf8-decode.php
    function smartUtf8_decode($inStr) {
// Replace ? with a unique string
        $newStr = str_replace("?", "w0i0k0i0n0d0x", $inStr);
// Try the utf8_decode
        $newStr = $this->decodeUtf8($newStr);
// if it contains ? marks
        if (strpos($newStr, "?") !== false)
// Something went wrong, set newStr to the original string.
            $newStr = $inStr;
        else
// If not then all is well, put the ?-marks back where is belongs
            $newStr = str_replace("w0i0k0i0n0d0x", "?", $newStr);
        return $newStr;
    }

// UTF-8 encoding - PROPERLY decode UTF-8 as PHP's utf8_decode can't hack it.
// Freely borrowed from morris_hirsch at http://www.php.net/manual/en/function.utf8-decode.php
// bytes bits representation
// 1  7  0bbbbbbb
// 2  11  110bbbbb 10bbbbbb
// 3  16  1110bbbb 10bbbbbb 10bbbbbb
// 4  21  11110bbb 10bbbbbb 10bbbbbb 10bbbbbb
// Each b represents a bit that can be used to store character data.
// input CANNOT have single byte upper half extended ascii codes
    function decodeUtf8($utf8_string) {

        $out = "";
        $ns = strlen($utf8_string);
        for ($nn = 0; $nn < $ns; $nn++) {
            $ch = $utf8_string [$nn];
            $ii = ord($ch);

//1 7 0bbbbbbb (127)

            if ($ii < 128)
                $out .= $ch;

//2 11 110bbbbb 10bbbbbb (2047)

            else if ($ii >> 5 == 6) {
                $b1 = ($ii & 31);

                $nn++;
                $ch = $utf8_string [$nn];
                $ii = ord($ch);
                $b2 = ($ii & 63);

                $ii = ($b1 * 64) + $b2;

                $ent = sprintf("&#%d;", $ii);
                $out .= $ent;
            }

//3 16 1110bbbb 10bbbbbb 10bbbbbb
            else if ($ii >> 4 == 14) {
                $b1 = ($ii & 31);

                $nn++;
                $ch = $utf8_string [$nn];
                $ii = ord($ch);
                $b2 = ($ii & 63);

                $nn++;
                $ch = $utf8_string [$nn];
                $ii = ord($ch);
                $b3 = ($ii & 63);

                $ii = ((($b1 * 64) + $b2) * 64) + $b3;

                $ent = sprintf("&#%d;", $ii);
                $out .= $ent;
            }

//4 21 11110bbb 10bbbbbb 10bbbbbb 10bbbbbb
            else if ($ii >> 3 == 30) {
                $b1 = ($ii & 31);

                $nn++;
                $ch = $utf8_string [$nn];
                $ii = ord($ch);
                $b2 = ($ii & 63);

                $nn++;
                $ch = $utf8_string [$nn];
                $ii = ord($ch);
                $b3 = ($ii & 63);

                $nn++;
                $ch = $utf8_string [$nn];
                $ii = ord($ch);
                $b4 = ($ii & 63);

                $ii = ((((($b1 * 64) + $b2) * 64) + $b3) * 64) + $b4;

                $ent = sprintf("&#%d;", $ii);
                $out .= $ent;
            }
        }
        return $out;
    }

    function encodeUtf8($str) {
        preg_match_all("/&#([0-9]*?);/", $str, $unicode);
        foreach ($unicode[0] as $key => $value) {
            $str = preg_replace("/" . $value . "/", $this->code2utf8($unicode[1][$key]), $str);
        }
        return $str;
    }

    /**
     * This function returns any UTF-8 encoded text as a list of
     * Unicode values:
     *
     * @author Scott Michael Reynen <scott@randomchaos.com>
     * @link   http://www.randomchaos.com/document.php?source=php_and_unicode
     * @see    unicode_to_utf8()
     */
    function utf8_to_unicode($str) {
        $unicode = array();
        $values = array();
        $lookingFor = 1;

        for ($i = 0; $i < strlen($str); $i++) {
            $thisValue = ord($str[$i]);
            if ($thisValue < 128)
                $unicode[] = $thisValue;
            else {
                if (count($values) == 0)
                    $lookingFor = ( $thisValue < 224 ) ? 2 : 3;
                $values[] = $thisValue;
                if (count($values) == $lookingFor) {
                    $number = ( $lookingFor == 3 ) ?
                            ( ( $values[0] % 16 ) * 4096 ) + ( ( $values[1] % 64 ) * 64 ) + ( $values[2] % 64 ) :
                            ( ( $values[0] % 32 ) * 64 ) + ( $values[1] % 64 );
                    $unicode[] = $number;
                    $values = array();
                    $lookingFor = 1;
                }
            }
        }
        return $unicode;
    }

    /**
     * This function converts a Unicode array back to its UTF-8 representation
     *
     * @author Scott Michael Reynen <scott@randomchaos.com>
     * @link   http://www.randomchaos.com/document.php?source=php_and_unicode
     * @see    utf8_to_unicode()
     */
    function unicode_to_utf8($str) {
        $utf8 = '';
        foreach ($str as $unicode) {
            if ($unicode < 128) {
                $utf8.= chr($unicode);
            } elseif ($unicode < 2048) {
                $utf8.= chr(192 + ( ( $unicode - ( $unicode % 64 ) ) / 64 ));
                $utf8.= chr(128 + ( $unicode % 64 ));
            } else {
                $utf8.= chr(224 + ( ( $unicode - ( $unicode % 4096 ) ) / 4096 ));
                $utf8.= chr(128 + ( ( ( $unicode % 4096 ) - ( $unicode % 64 ) ) / 64 ));
                $utf8.= chr(128 + ( $unicode % 64 ));
            }
        }
        return $utf8;
    }

    function code2utf8($num) {
        if ($num < 128)
            return chr($num);
        if ($num < 2048)
            return chr(($num >> 6) + 192) . chr(($num & 63) + 128);
        if ($num < 65536)
            return chr(($num >> 12) + 224) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
        if ($num < 2097152)
            return chr(($num >> 18) + 240) . chr((($num >> 12) & 63) + 128) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
        return '';
    }

    /**
     * This is a unicode aware replacement for strtolower()
     *
     * Uses mb_string extension if available
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     * @see    strtolower()
     * @see    utf8_strtoupper()
     */
    function utf8_strtolower($string) {
        if (function_exists('mb_strtolower'))
            return mb_strtolower($string, 'utf-8');

        $uni = $this->utf8_to_unicode($string);
        $cnt = count($uni);
        for ($i = 0; $i < $cnt; $i++) {
            if (array_key_exists($uni[$i], $this->UTF8_UPPER_TO_LOWER) && $this->UTF8_UPPER_TO_LOWER[$uni[$i]]) {
                $uni[$i] = $this->UTF8_UPPER_TO_LOWER[$uni[$i]];
            }
        }
        return $this->unicode_to_utf8($uni);
    }

    /**
     * This is a unicode aware replacement for strtoupper()
     *
     * Uses mb_string extension if available
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     * @see    strtoupper()
     * @see    utf8_strtoupper()
     */
    function utf8_strtoupper($string) {
        if (function_exists('mb_strtoupper'))
            return mb_strtoupper($string, 'utf-8');

        $uni = $this->utf8_to_unicode($string);
        for ($i = 0; $i < count($uni); $i++) {
            if (array_key_exists($uni[$i], $this->UTF8_LOWER_TO_UPPER) && $this->UTF8_LOWER_TO_UPPER[$uni[$i]]) {
                $uni[$i] = $this->UTF8_LOWER_TO_UPPER[$uni[$i]];
            }
        }
        return $this->unicode_to_utf8($uni);
    }

    /**
     * This is a unicode aware replacement for substr()
     *
     * Uses mb_string extension if available
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     * @see    substr()
     */
    function utf8_substr($str, $start, $length = null) {
        if (function_exists('mb_substr'))
            return mb_substr($str, $start, $length, 'utf-8');

        $uni = $this->utf8_to_unicode($str);
        return $this->unicode_to_utf8(array_slice($uni, $start, $length));
    }

    /**
     * This is a unicode aware replacement for ucfirst()
     *
     * @author Andrea Rossato <arossato@istitutocolli.org>
     * @see    ucfirst()
     */
    function utf8_ucfirst($str) {
        $fc = $this->utf8_substr($str, 0, 1);
        return $this->utf8_strtoupper($fc) . $this->utf8_substr($str, 1, $this->utf8_strlen($str));
    }

    /**
     * This is a unicode aware replacement for strlen()
     *
     * Uses mb_string extension if available
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     * @see    strlen()
     */
    function utf8_strlen($string) {
        if (!defined('UTF8_NOMBSTRING') && function_exists('mb_strlen'))
            return mb_strlen($string, 'utf-8');

        $uni = $this->utf8_to_unicode($string);
        return count($uni);
    }

    function utf8_htmlspecialchars($str) {
        $str = str_replace("\"", "&quot;", $str);
        $str = str_replace("<", "&lt;", $str);
        $str = str_replace(">", "&gt;", $str);
        $str = preg_replace("/&(?![a-zA-Z0-9#]+?;)/", "&amp;", $str);
        return $str;
    }

    function loadVars() {
        /**
         * UTF-8 Case lookup table
         *
         * This lookuptable defines the upper case letters to their correspponding
         * lower case letter in UTF-8
         *
         * @author Andreas Gohr <andi@splitbrain.org>
         */
        $this->UTF8_LOWER_TO_UPPER = array(
            0x00000061 => 0x00000041, 0x00000062 => 0x00000042, 0x00000063 => 0x00000043, 0x00000064 => 0x00000044,
            0x00000065 => 0x00000045, 0x00000066 => 0x00000046, 0x00000067 => 0x00000047, 0x00000068 => 0x00000048,
            0x00000069 => 0x00000049, 0x0000006a => 0x0000004a, 0x0000006b => 0x0000004b, 0x0000006c => 0x0000004c,
            0x0000006d => 0x0000004d, 0x0000006e => 0x0000004e, 0x0000006f => 0x0000004f, 0x00000070 => 0x00000050,
            0x00000071 => 0x00000051, 0x00000072 => 0x00000052, 0x00000073 => 0x00000053, 0x00000074 => 0x00000054,
            0x00000075 => 0x00000055, 0x00000076 => 0x00000056, 0x00000077 => 0x00000057, 0x00000078 => 0x00000058,
            0x00000079 => 0x00000059, 0x0000007a => 0x0000005a, 0x000000b5 => 0x0000039c, 0x000000e0 => 0x000000c0,
            0x000000e1 => 0x000000c1, 0x000000e2 => 0x000000c2, 0x000000e3 => 0x000000c3, 0x000000e4 => 0x000000c4,
            0x000000e5 => 0x000000c5, 0x000000e6 => 0x000000c6, 0x000000e7 => 0x000000c7, 0x000000e8 => 0x000000c8,
            0x000000e9 => 0x000000c9, 0x000000ea => 0x000000ca, 0x000000eb => 0x000000cb, 0x000000ec => 0x000000cc,
            0x000000ed => 0x000000cd, 0x000000ee => 0x000000ce, 0x000000ef => 0x000000cf, 0x000000f0 => 0x000000d0,
            0x000000f1 => 0x000000d1, 0x000000f2 => 0x000000d2, 0x000000f3 => 0x000000d3, 0x000000f4 => 0x000000d4,
            0x000000f5 => 0x000000d5, 0x000000f6 => 0x000000d6, 0x000000f8 => 0x000000d8, 0x000000f9 => 0x000000d9,
            0x000000fa => 0x000000da, 0x000000fb => 0x000000db, 0x000000fc => 0x000000dc, 0x000000fd => 0x000000dd,
            0x000000fe => 0x000000de, 0x000000ff => 0x00000178, 0x00000101 => 0x00000100, 0x00000103 => 0x00000102,
            0x00000105 => 0x00000104, 0x00000107 => 0x00000106, 0x00000109 => 0x00000108, 0x0000010b => 0x0000010a,
            0x0000010d => 0x0000010c, 0x0000010f => 0x0000010e, 0x00000111 => 0x00000110, 0x00000113 => 0x00000112,
            0x00000115 => 0x00000114, 0x00000117 => 0x00000116, 0x00000119 => 0x00000118, 0x0000011b => 0x0000011a,
            0x0000011d => 0x0000011c, 0x0000011f => 0x0000011e, 0x00000121 => 0x00000120, 0x00000123 => 0x00000122,
            0x00000125 => 0x00000124, 0x00000127 => 0x00000126, 0x00000129 => 0x00000128, 0x0000012b => 0x0000012a,
            0x0000012d => 0x0000012c, 0x0000012f => 0x0000012e, 0x00000131 => 0x00000049, 0x00000133 => 0x00000132,
            0x00000135 => 0x00000134, 0x00000137 => 0x00000136, 0x0000013a => 0x00000139, 0x0000013c => 0x0000013b,
            0x0000013e => 0x0000013d, 0x00000140 => 0x0000013f, 0x00000142 => 0x00000141, 0x00000144 => 0x00000143,
            0x00000146 => 0x00000145, 0x00000148 => 0x00000147, 0x0000014b => 0x0000014a, 0x0000014d => 0x0000014c,
            0x0000014f => 0x0000014e, 0x00000151 => 0x00000150, 0x00000153 => 0x00000152, 0x00000155 => 0x00000154,
            0x00000157 => 0x00000156, 0x00000159 => 0x00000158, 0x0000015b => 0x0000015a, 0x0000015d => 0x0000015c,
            0x0000015f => 0x0000015e, 0x00000161 => 0x00000160, 0x00000163 => 0x00000162, 0x00000165 => 0x00000164,
            0x00000167 => 0x00000166, 0x00000169 => 0x00000168, 0x0000016b => 0x0000016a, 0x0000016d => 0x0000016c,
            0x0000016f => 0x0000016e, 0x00000171 => 0x00000170, 0x00000173 => 0x00000172, 0x00000175 => 0x00000174,
            0x00000177 => 0x00000176, 0x0000017a => 0x00000179, 0x0000017c => 0x0000017b, 0x0000017e => 0x0000017d,
            0x0000017f => 0x00000053, 0x00000183 => 0x00000182, 0x00000185 => 0x00000184, 0x00000188 => 0x00000187,
            0x0000018c => 0x0000018b, 0x00000192 => 0x00000191, 0x00000195 => 0x000001f6, 0x00000199 => 0x00000198,
            0x0000019e => 0x00000220, 0x000001a1 => 0x000001a0, 0x000001a3 => 0x000001a2, 0x000001a5 => 0x000001a4,
            0x000001a8 => 0x000001a7, 0x000001ad => 0x000001ac, 0x000001b0 => 0x000001af, 0x000001b4 => 0x000001b3,
            0x000001b6 => 0x000001b5, 0x000001b9 => 0x000001b8, 0x000001bd => 0x000001bc, 0x000001bf => 0x000001f7,
            0x000001c6 => 0x000001c4, 0x000001c9 => 0x000001c7, 0x000001cc => 0x000001ca, 0x000001ce => 0x000001cd,
            0x000001d0 => 0x000001cf, 0x000001d2 => 0x000001d1, 0x000001d4 => 0x000001d3, 0x000001d6 => 0x000001d5,
            0x000001d8 => 0x000001d7, 0x000001da => 0x000001d9, 0x000001dc => 0x000001db, 0x000001dd => 0x0000018e,
            0x000001df => 0x000001de, 0x000001e1 => 0x000001e0, 0x000001e3 => 0x000001e2, 0x000001e5 => 0x000001e4,
            0x000001e7 => 0x000001e6, 0x000001e9 => 0x000001e8, 0x000001eb => 0x000001ea, 0x000001ed => 0x000001ec,
            0x000001ef => 0x000001ee, 0x000001f3 => 0x000001f1, 0x000001f5 => 0x000001f4, 0x000001f9 => 0x000001f8,
            0x000001fb => 0x000001fa, 0x000001fd => 0x000001fc, 0x000001ff => 0x000001fe, 0x00000201 => 0x00000200,
            0x00000203 => 0x00000202, 0x00000205 => 0x00000204, 0x00000207 => 0x00000206, 0x00000209 => 0x00000208,
            0x0000020b => 0x0000020a, 0x0000020d => 0x0000020c, 0x0000020f => 0x0000020e, 0x00000211 => 0x00000210,
            0x00000213 => 0x00000212, 0x00000215 => 0x00000214, 0x00000217 => 0x00000216, 0x00000219 => 0x00000218,
            0x0000021b => 0x0000021a, 0x0000021d => 0x0000021c, 0x0000021f => 0x0000021e, 0x00000223 => 0x00000222,
            0x00000225 => 0x00000224, 0x00000227 => 0x00000226, 0x00000229 => 0x00000228, 0x0000022b => 0x0000022a,
            0x0000022d => 0x0000022c, 0x0000022f => 0x0000022e, 0x00000231 => 0x00000230, 0x00000233 => 0x00000232,
            0x00000253 => 0x00000181, 0x00000254 => 0x00000186, 0x00000256 => 0x00000189, 0x00000257 => 0x0000018a,
            0x00000259 => 0x0000018f, 0x0000025b => 0x00000190, 0x00000260 => 0x00000193, 0x00000263 => 0x00000194,
            0x00000268 => 0x00000197, 0x00000269 => 0x00000196, 0x0000026f => 0x0000019c, 0x00000272 => 0x0000019d,
            0x00000275 => 0x0000019f, 0x00000280 => 0x000001a6, 0x00000283 => 0x000001a9, 0x00000288 => 0x000001ae,
            0x0000028a => 0x000001b1, 0x0000028b => 0x000001b2, 0x00000292 => 0x000001b7, 0x00000345 => 0x00000399,
            0x000003ac => 0x00000386, 0x000003ad => 0x00000388, 0x000003ae => 0x00000389, 0x000003af => 0x0000038a,
            0x000003b1 => 0x00000391, 0x000003b2 => 0x00000392, 0x000003b3 => 0x00000393, 0x000003b4 => 0x00000394,
            0x000003b5 => 0x00000395, 0x000003b6 => 0x00000396, 0x000003b7 => 0x00000397, 0x000003b8 => 0x00000398,
            0x000003b9 => 0x00000399, 0x000003ba => 0x0000039a, 0x000003bb => 0x0000039b, 0x000003bc => 0x0000039c,
            0x000003bd => 0x0000039d, 0x000003be => 0x0000039e, 0x000003bf => 0x0000039f, 0x000003c0 => 0x000003a0,
            0x000003c1 => 0x000003a1, 0x000003c2 => 0x000003a3, 0x000003c3 => 0x000003a3, 0x000003c4 => 0x000003a4,
            0x000003c5 => 0x000003a5, 0x000003c6 => 0x000003a6, 0x000003c7 => 0x000003a7, 0x000003c8 => 0x000003a8,
            0x000003c9 => 0x000003a9, 0x000003ca => 0x000003aa, 0x000003cb => 0x000003ab, 0x000003cc => 0x0000038c,
            0x000003cd => 0x0000038e, 0x000003ce => 0x0000038f, 0x000003d0 => 0x00000392, 0x000003d1 => 0x00000398,
            0x000003d5 => 0x000003a6, 0x000003d6 => 0x000003a0, 0x000003d9 => 0x000003d8, 0x000003db => 0x000003da,
            0x000003dd => 0x000003dc, 0x000003df => 0x000003de, 0x000003e1 => 0x000003e0, 0x000003e3 => 0x000003e2,
            0x000003e5 => 0x000003e4, 0x000003e7 => 0x000003e6, 0x000003e9 => 0x000003e8, 0x000003eb => 0x000003ea,
            0x000003ed => 0x000003ec, 0x000003ef => 0x000003ee, 0x000003f0 => 0x0000039a, 0x000003f1 => 0x000003a1,
            0x000003f2 => 0x000003a3, 0x000003f5 => 0x00000395, 0x00000430 => 0x00000410, 0x00000431 => 0x00000411,
            0x00000432 => 0x00000412, 0x00000433 => 0x00000413, 0x00000434 => 0x00000414, 0x00000435 => 0x00000415,
            0x00000436 => 0x00000416, 0x00000437 => 0x00000417, 0x00000438 => 0x00000418, 0x00000439 => 0x00000419,
            0x0000043a => 0x0000041a, 0x0000043b => 0x0000041b, 0x0000043c => 0x0000041c, 0x0000043d => 0x0000041d,
            0x0000043e => 0x0000041e, 0x0000043f => 0x0000041f, 0x00000440 => 0x00000420, 0x00000441 => 0x00000421,
            0x00000442 => 0x00000422, 0x00000443 => 0x00000423, 0x00000444 => 0x00000424, 0x00000445 => 0x00000425,
            0x00000446 => 0x00000426, 0x00000447 => 0x00000427, 0x00000448 => 0x00000428, 0x00000449 => 0x00000429,
            0x0000044a => 0x0000042a, 0x0000044b => 0x0000042b, 0x0000044c => 0x0000042c, 0x0000044d => 0x0000042d,
            0x0000044e => 0x0000042e, 0x0000044f => 0x0000042f, 0x00000450 => 0x00000400, 0x00000451 => 0x00000401,
            0x00000452 => 0x00000402, 0x00000453 => 0x00000403, 0x00000454 => 0x00000404, 0x00000455 => 0x00000405,
            0x00000456 => 0x00000406, 0x00000457 => 0x00000407, 0x00000458 => 0x00000408, 0x00000459 => 0x00000409,
            0x0000045a => 0x0000040a, 0x0000045b => 0x0000040b, 0x0000045c => 0x0000040c, 0x0000045d => 0x0000040d,
            0x0000045e => 0x0000040e, 0x0000045f => 0x0000040f, 0x00000461 => 0x00000460, 0x00000463 => 0x00000462,
            0x00000465 => 0x00000464, 0x00000467 => 0x00000466, 0x00000469 => 0x00000468, 0x0000046b => 0x0000046a,
            0x0000046d => 0x0000046c, 0x0000046f => 0x0000046e, 0x00000471 => 0x00000470, 0x00000473 => 0x00000472,
            0x00000475 => 0x00000474, 0x00000477 => 0x00000476, 0x00000479 => 0x00000478, 0x0000047b => 0x0000047a,
            0x0000047d => 0x0000047c, 0x0000047f => 0x0000047e, 0x00000481 => 0x00000480, 0x0000048b => 0x0000048a,
            0x0000048d => 0x0000048c, 0x0000048f => 0x0000048e, 0x00000491 => 0x00000490, 0x00000493 => 0x00000492,
            0x00000495 => 0x00000494, 0x00000497 => 0x00000496, 0x00000499 => 0x00000498, 0x0000049b => 0x0000049a,
            0x0000049d => 0x0000049c, 0x0000049f => 0x0000049e, 0x000004a1 => 0x000004a0, 0x000004a3 => 0x000004a2,
            0x000004a5 => 0x000004a4, 0x000004a7 => 0x000004a6, 0x000004a9 => 0x000004a8, 0x000004ab => 0x000004aa,
            0x000004ad => 0x000004ac, 0x000004af => 0x000004ae, 0x000004b1 => 0x000004b0, 0x000004b3 => 0x000004b2,
            0x000004b5 => 0x000004b4, 0x000004b7 => 0x000004b6, 0x000004b9 => 0x000004b8, 0x000004bb => 0x000004ba,
            0x000004bd => 0x000004bc, 0x000004bf => 0x000004be, 0x000004c2 => 0x000004c1, 0x000004c4 => 0x000004c3,
            0x000004c6 => 0x000004c5, 0x000004c8 => 0x000004c7, 0x000004ca => 0x000004c9, 0x000004cc => 0x000004cb,
            0x000004ce => 0x000004cd, 0x000004d1 => 0x000004d0, 0x000004d3 => 0x000004d2, 0x000004d5 => 0x000004d4,
            0x000004d7 => 0x000004d6, 0x000004d9 => 0x000004d8, 0x000004db => 0x000004da, 0x000004dd => 0x000004dc,
            0x000004df => 0x000004de, 0x000004e1 => 0x000004e0, 0x000004e3 => 0x000004e2, 0x000004e5 => 0x000004e4,
            0x000004e7 => 0x000004e6, 0x000004e9 => 0x000004e8, 0x000004eb => 0x000004ea, 0x000004ed => 0x000004ec,
            0x000004ef => 0x000004ee, 0x000004f1 => 0x000004f0, 0x000004f3 => 0x000004f2, 0x000004f5 => 0x000004f4,
            0x000004f9 => 0x000004f8, 0x00000501 => 0x00000500, 0x00000503 => 0x00000502, 0x00000505 => 0x00000504,
            0x00000507 => 0x00000506, 0x00000509 => 0x00000508, 0x0000050b => 0x0000050a, 0x0000050d => 0x0000050c,
            0x0000050f => 0x0000050e, 0x00000561 => 0x00000531, 0x00000562 => 0x00000532, 0x00000563 => 0x00000533,
            0x00000564 => 0x00000534, 0x00000565 => 0x00000535, 0x00000566 => 0x00000536, 0x00000567 => 0x00000537,
            0x00000568 => 0x00000538, 0x00000569 => 0x00000539, 0x0000056a => 0x0000053a, 0x0000056b => 0x0000053b,
            0x0000056c => 0x0000053c, 0x0000056d => 0x0000053d, 0x0000056e => 0x0000053e, 0x0000056f => 0x0000053f,
            0x00000570 => 0x00000540, 0x00000571 => 0x00000541, 0x00000572 => 0x00000542, 0x00000573 => 0x00000543,
            0x00000574 => 0x00000544, 0x00000575 => 0x00000545, 0x00000576 => 0x00000546, 0x00000577 => 0x00000547,
            0x00000578 => 0x00000548, 0x00000579 => 0x00000549, 0x0000057a => 0x0000054a, 0x0000057b => 0x0000054b,
            0x0000057c => 0x0000054c, 0x0000057d => 0x0000054d, 0x0000057e => 0x0000054e, 0x0000057f => 0x0000054f,
            0x00000580 => 0x00000550, 0x00000581 => 0x00000551, 0x00000582 => 0x00000552, 0x00000583 => 0x00000553,
            0x00000584 => 0x00000554, 0x00000585 => 0x00000555, 0x00000586 => 0x00000556, 0x00001e01 => 0x00001e00,
            0x00001e03 => 0x00001e02, 0x00001e05 => 0x00001e04, 0x00001e07 => 0x00001e06, 0x00001e09 => 0x00001e08,
            0x00001e0b => 0x00001e0a, 0x00001e0d => 0x00001e0c, 0x00001e0f => 0x00001e0e, 0x00001e11 => 0x00001e10,
            0x00001e13 => 0x00001e12, 0x00001e15 => 0x00001e14, 0x00001e17 => 0x00001e16, 0x00001e19 => 0x00001e18,
            0x00001e1b => 0x00001e1a, 0x00001e1d => 0x00001e1c, 0x00001e1f => 0x00001e1e, 0x00001e21 => 0x00001e20,
            0x00001e23 => 0x00001e22, 0x00001e25 => 0x00001e24, 0x00001e27 => 0x00001e26, 0x00001e29 => 0x00001e28,
            0x00001e2b => 0x00001e2a, 0x00001e2d => 0x00001e2c, 0x00001e2f => 0x00001e2e, 0x00001e31 => 0x00001e30,
            0x00001e33 => 0x00001e32, 0x00001e35 => 0x00001e34, 0x00001e37 => 0x00001e36, 0x00001e39 => 0x00001e38,
            0x00001e3b => 0x00001e3a, 0x00001e3d => 0x00001e3c, 0x00001e3f => 0x00001e3e, 0x00001e41 => 0x00001e40,
            0x00001e43 => 0x00001e42, 0x00001e45 => 0x00001e44, 0x00001e47 => 0x00001e46, 0x00001e49 => 0x00001e48,
            0x00001e4b => 0x00001e4a, 0x00001e4d => 0x00001e4c, 0x00001e4f => 0x00001e4e, 0x00001e51 => 0x00001e50,
            0x00001e53 => 0x00001e52, 0x00001e55 => 0x00001e54, 0x00001e57 => 0x00001e56, 0x00001e59 => 0x00001e58,
            0x00001e5b => 0x00001e5a, 0x00001e5d => 0x00001e5c, 0x00001e5f => 0x00001e5e, 0x00001e61 => 0x00001e60,
            0x00001e63 => 0x00001e62, 0x00001e65 => 0x00001e64, 0x00001e67 => 0x00001e66, 0x00001e69 => 0x00001e68,
            0x00001e6b => 0x00001e6a, 0x00001e6d => 0x00001e6c, 0x00001e6f => 0x00001e6e, 0x00001e71 => 0x00001e70,
            0x00001e73 => 0x00001e72, 0x00001e75 => 0x00001e74, 0x00001e77 => 0x00001e76, 0x00001e79 => 0x00001e78,
            0x00001e7b => 0x00001e7a, 0x00001e7d => 0x00001e7c, 0x00001e7f => 0x00001e7e, 0x00001e81 => 0x00001e80,
            0x00001e83 => 0x00001e82, 0x00001e85 => 0x00001e84, 0x00001e87 => 0x00001e86, 0x00001e89 => 0x00001e88,
            0x00001e8b => 0x00001e8a, 0x00001e8d => 0x00001e8c, 0x00001e8f => 0x00001e8e, 0x00001e91 => 0x00001e90,
            0x00001e93 => 0x00001e92, 0x00001e95 => 0x00001e94, 0x00001e9b => 0x00001e60, 0x00001ea1 => 0x00001ea0,
            0x00001ea3 => 0x00001ea2, 0x00001ea5 => 0x00001ea4, 0x00001ea7 => 0x00001ea6, 0x00001ea9 => 0x00001ea8,
            0x00001eab => 0x00001eaa, 0x00001ead => 0x00001eac, 0x00001eaf => 0x00001eae, 0x00001eb1 => 0x00001eb0,
            0x00001eb3 => 0x00001eb2, 0x00001eb5 => 0x00001eb4, 0x00001eb7 => 0x00001eb6, 0x00001eb9 => 0x00001eb8,
            0x00001ebb => 0x00001eba, 0x00001ebd => 0x00001ebc, 0x00001ebf => 0x00001ebe, 0x00001ec1 => 0x00001ec0,
            0x00001ec3 => 0x00001ec2, 0x00001ec5 => 0x00001ec4, 0x00001ec7 => 0x00001ec6, 0x00001ec9 => 0x00001ec8,
            0x00001ecb => 0x00001eca, 0x00001ecd => 0x00001ecc, 0x00001ecf => 0x00001ece, 0x00001ed1 => 0x00001ed0,
            0x00001ed3 => 0x00001ed2, 0x00001ed5 => 0x00001ed4, 0x00001ed7 => 0x00001ed6, 0x00001ed9 => 0x00001ed8,
            0x00001edb => 0x00001eda, 0x00001edd => 0x00001edc, 0x00001edf => 0x00001ede, 0x00001ee1 => 0x00001ee0,
            0x00001ee3 => 0x00001ee2, 0x00001ee5 => 0x00001ee4, 0x00001ee7 => 0x00001ee6, 0x00001ee9 => 0x00001ee8,
            0x00001eeb => 0x00001eea, 0x00001eed => 0x00001eec, 0x00001eef => 0x00001eee, 0x00001ef1 => 0x00001ef0,
            0x00001ef3 => 0x00001ef2, 0x00001ef5 => 0x00001ef4, 0x00001ef7 => 0x00001ef6, 0x00001ef9 => 0x00001ef8,
            0x00001f00 => 0x00001f08, 0x00001f01 => 0x00001f09, 0x00001f02 => 0x00001f0a, 0x00001f03 => 0x00001f0b,
            0x00001f04 => 0x00001f0c, 0x00001f05 => 0x00001f0d, 0x00001f06 => 0x00001f0e, 0x00001f07 => 0x00001f0f,
            0x00001f10 => 0x00001f18, 0x00001f11 => 0x00001f19, 0x00001f12 => 0x00001f1a, 0x00001f13 => 0x00001f1b,
            0x00001f14 => 0x00001f1c, 0x00001f15 => 0x00001f1d, 0x00001f20 => 0x00001f28, 0x00001f21 => 0x00001f29,
            0x00001f22 => 0x00001f2a, 0x00001f23 => 0x00001f2b, 0x00001f24 => 0x00001f2c, 0x00001f25 => 0x00001f2d,
            0x00001f26 => 0x00001f2e, 0x00001f27 => 0x00001f2f, 0x00001f30 => 0x00001f38, 0x00001f31 => 0x00001f39,
            0x00001f32 => 0x00001f3a, 0x00001f33 => 0x00001f3b, 0x00001f34 => 0x00001f3c, 0x00001f35 => 0x00001f3d,
            0x00001f36 => 0x00001f3e, 0x00001f37 => 0x00001f3f, 0x00001f40 => 0x00001f48, 0x00001f41 => 0x00001f49,
            0x00001f42 => 0x00001f4a, 0x00001f43 => 0x00001f4b, 0x00001f44 => 0x00001f4c, 0x00001f45 => 0x00001f4d,
            0x00001f51 => 0x00001f59, 0x00001f53 => 0x00001f5b, 0x00001f55 => 0x00001f5d, 0x00001f57 => 0x00001f5f,
            0x00001f60 => 0x00001f68, 0x00001f61 => 0x00001f69, 0x00001f62 => 0x00001f6a, 0x00001f63 => 0x00001f6b,
            0x00001f64 => 0x00001f6c, 0x00001f65 => 0x00001f6d, 0x00001f66 => 0x00001f6e, 0x00001f67 => 0x00001f6f,
            0x00001f70 => 0x00001fba, 0x00001f71 => 0x00001fbb, 0x00001f72 => 0x00001fc8, 0x00001f73 => 0x00001fc9,
            0x00001f74 => 0x00001fca, 0x00001f75 => 0x00001fcb, 0x00001f76 => 0x00001fda, 0x00001f77 => 0x00001fdb,
            0x00001f78 => 0x00001ff8, 0x00001f79 => 0x00001ff9, 0x00001f7a => 0x00001fea, 0x00001f7b => 0x00001feb,
            0x00001f7c => 0x00001ffa, 0x00001f7d => 0x00001ffb, 0x00001f80 => 0x00001f88, 0x00001f81 => 0x00001f89,
            0x00001f82 => 0x00001f8a, 0x00001f83 => 0x00001f8b, 0x00001f84 => 0x00001f8c, 0x00001f85 => 0x00001f8d,
            0x00001f86 => 0x00001f8e, 0x00001f87 => 0x00001f8f, 0x00001f90 => 0x00001f98, 0x00001f91 => 0x00001f99,
            0x00001f92 => 0x00001f9a, 0x00001f93 => 0x00001f9b, 0x00001f94 => 0x00001f9c, 0x00001f95 => 0x00001f9d,
            0x00001f96 => 0x00001f9e, 0x00001f97 => 0x00001f9f, 0x00001fa0 => 0x00001fa8, 0x00001fa1 => 0x00001fa9,
            0x00001fa2 => 0x00001faa, 0x00001fa3 => 0x00001fab, 0x00001fa4 => 0x00001fac, 0x00001fa5 => 0x00001fad,
            0x00001fa6 => 0x00001fae, 0x00001fa7 => 0x00001faf, 0x00001fb0 => 0x00001fb8, 0x00001fb1 => 0x00001fb9,
            0x00001fb3 => 0x00001fbc, 0x00001fbe => 0x00000399, 0x00001fc3 => 0x00001fcc, 0x00001fd0 => 0x00001fd8,
            0x00001fd1 => 0x00001fd9, 0x00001fe0 => 0x00001fe8, 0x00001fe1 => 0x00001fe9, 0x00001fe5 => 0x00001fec,
            0x00001ff3 => 0x00001ffc, 0x00002170 => 0x00002160, 0x00002171 => 0x00002161, 0x00002172 => 0x00002162,
            0x00002173 => 0x00002163, 0x00002174 => 0x00002164, 0x00002175 => 0x00002165, 0x00002176 => 0x00002166,
            0x00002177 => 0x00002167, 0x00002178 => 0x00002168, 0x00002179 => 0x00002169, 0x0000217a => 0x0000216a,
            0x0000217b => 0x0000216b, 0x0000217c => 0x0000216c, 0x0000217d => 0x0000216d, 0x0000217e => 0x0000216e,
            0x0000217f => 0x0000216f, 0x000024d0 => 0x000024b6, 0x000024d1 => 0x000024b7, 0x000024d2 => 0x000024b8,
            0x000024d3 => 0x000024b9, 0x000024d4 => 0x000024ba, 0x000024d5 => 0x000024bb, 0x000024d6 => 0x000024bc,
            0x000024d7 => 0x000024bd, 0x000024d8 => 0x000024be, 0x000024d9 => 0x000024bf, 0x000024da => 0x000024c0,
            0x000024db => 0x000024c1, 0x000024dc => 0x000024c2, 0x000024dd => 0x000024c3, 0x000024de => 0x000024c4,
            0x000024df => 0x000024c5, 0x000024e0 => 0x000024c6, 0x000024e1 => 0x000024c7, 0x000024e2 => 0x000024c8,
            0x000024e3 => 0x000024c9, 0x000024e4 => 0x000024ca, 0x000024e5 => 0x000024cb, 0x000024e6 => 0x000024cc,
            0x000024e7 => 0x000024cd, 0x000024e8 => 0x000024ce, 0x000024e9 => 0x000024cf, 0x0000ff41 => 0x0000ff21,
            0x0000ff42 => 0x0000ff22, 0x0000ff43 => 0x0000ff23, 0x0000ff44 => 0x0000ff24, 0x0000ff45 => 0x0000ff25,
            0x0000ff46 => 0x0000ff26, 0x0000ff47 => 0x0000ff27, 0x0000ff48 => 0x0000ff28, 0x0000ff49 => 0x0000ff29,
            0x0000ff4a => 0x0000ff2a, 0x0000ff4b => 0x0000ff2b, 0x0000ff4c => 0x0000ff2c, 0x0000ff4d => 0x0000ff2d,
            0x0000ff4e => 0x0000ff2e, 0x0000ff4f => 0x0000ff2f, 0x0000ff50 => 0x0000ff30, 0x0000ff51 => 0x0000ff31,
            0x0000ff52 => 0x0000ff32, 0x0000ff53 => 0x0000ff33, 0x0000ff54 => 0x0000ff34, 0x0000ff55 => 0x0000ff35,
            0x0000ff56 => 0x0000ff36, 0x0000ff57 => 0x0000ff37, 0x0000ff58 => 0x0000ff38, 0x0000ff59 => 0x0000ff39,
            0x0000ff5a => 0x0000ff3a, 0x00010428 => 0x00010400, 0x00010429 => 0x00010401, 0x0001042a => 0x00010402,
            0x0001042b => 0x00010403, 0x0001042c => 0x00010404, 0x0001042d => 0x00010405, 0x0001042e => 0x00010406,
            0x0001042f => 0x00010407, 0x00010430 => 0x00010408, 0x00010431 => 0x00010409, 0x00010432 => 0x0001040a,
            0x00010433 => 0x0001040b, 0x00010434 => 0x0001040c, 0x00010435 => 0x0001040d, 0x00010436 => 0x0001040e,
            0x00010437 => 0x0001040f, 0x00010438 => 0x00010410, 0x00010439 => 0x00010411, 0x0001043a => 0x00010412,
            0x0001043b => 0x00010413, 0x0001043c => 0x00010414, 0x0001043d => 0x00010415, 0x0001043e => 0x00010416,
            0x0001043f => 0x00010417, 0x00010440 => 0x00010418, 0x00010441 => 0x00010419, 0x00010442 => 0x0001041a,
            0x00010443 => 0x0001041b, 0x00010444 => 0x0001041c, 0x00010445 => 0x0001041d, 0x00010446 => 0x0001041e,
            0x00010447 => 0x0001041f, 0x00010448 => 0x00010420, 0x00010449 => 0x00010421, 0x0001044a => 0x00010422,
            0x0001044b => 0x00010423, 0x0001044c => 0x00010424, 0x0001044d => 0x00010425,
        );

        /**
         * UTF-8 Case lookup table
         *
         * This lookuptable defines the lower case letters to their correspponding
         * upper case letter in UTF-8 (it does so by flipping $UTF8_LOWER_TO_UPPER)
         *
         * @author Andreas Gohr <andi@splitbrain.org>
         */
        $this->UTF8_UPPER_TO_LOWER = array_flip($this->UTF8_LOWER_TO_UPPER);
    }

}

?>
