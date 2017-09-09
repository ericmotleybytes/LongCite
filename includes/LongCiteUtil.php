<?php
/// Source code file for the LongCiteUtil:: class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.\n
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

/// Class with utility routines.
class LongCiteUtil {

    function a_or_an($text,$upcase=true) {
        $result = "";
        $lctext = strtolower($text);
        $t1 = substr($lctext,0,1);
        $t2 = substr($lctext,0,2);
        $t3 = substr($lctext,0,3);
        $t4 = substr($lctext,0,4);
        if($upcase) {
            $a  = "A";
            $an = "An";
        } else {
            $a  = "a";
            $an = "an";
        }
        if(in_array($t1,array("8","a","i","e"))) {
            return $an;
        }
        if($t1=="o") {
            if(in_array($lctext,array("one","once"))) {
                return $a;
            }
            if($t4=="one-") {
                return $a;
            }
            return $an;
        }
        if($t1=="u") {
            if($t2=="uk") {
                return $a;
            }
            if($t2=="uni") {
                return $a;
            }
            return $an;
        }
        if($t1=="h") {
            if(in_array($t3,array("her","hou","hon"))) {
                return $an;
            }
            return $a;
        }
        return $a;
    }

    /// Generate a generic random unique GUID/UUID as a 32 character uppercase hex string.
    /// @return A 32 character string with uppercase hex characters or false on error.
    public static function generateGenericGuid() : string {
        // generic fallback method
        mt_srand((double)microtime() * 10000);
        $result = strtoupper(md5(uniqid(rand(), true)));
        return $result;
    }

    /// Generate a openssl random unique GUID/UUID as a 32 character uppercase hex string.
    /// @return A 32 character string with uppercase hex characters or false on error.
    public static function generateOpensslGuid() : string {
        if (!function_exists('openssl_random_pseudo_bytes')) {
            $msg = "openssl_random_pseudo_bytes not available.";
            trigger_error($msg,E_USER_WARNING);
            return false;
        }
        // OSX/Linux generation method
        $data = openssl_random_pseudo_bytes(16);
        if ($data===false) {
            $msg = "openssl_random_pseudo_bytes error detected.";
            trigger_error($msg,E_USER_WARNING);
            return false;
        }
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
        $result = strtoupper(vsprintf('%s%s%s%s%s%s%s%s', str_split(bin2hex($data), 4)));
        return $result;
    }

}
?>
