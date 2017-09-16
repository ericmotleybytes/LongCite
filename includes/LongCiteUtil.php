<?php
/// Source code file for the LongCiteUtil:: class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.\n
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

/// Class with utility routines.
class LongCiteUtil {
    const GenderMale     = "M";
    const GenderFemale   = "F";
    const GenderNeutral  = "N";
    const GenderUnknown  = "U";
    const GenderOther    = "O";

    protected static $i18nCache = array();  // hash language code to json

    public static function a_or_an($text,$upcase=true) {
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
    public static function generateGenericGuid() {
        // generic fallback method
        mt_srand((double)microtime() * 10000);
        $result = strtoupper(md5(uniqid(rand(), true)));
        return $result;
    }

    /// Generate a openssl random unique GUID/UUID as a 32 character uppercase hex string.
    /// @return A 32 character string with uppercase hex characters or false on error.
    public static function generateOpensslGuid() {
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

    /// Percent Hex encode a string.
    public static function percentHexEncode($text) {
        if(strlen($text)==0) { return ""; }
        $newText = preg_replace('/(..)/','%$1',bin2hex($text));
        return $newText;
    }
    
    /// Percent Hex decode a string.
    public static function percentHexDecode($text) {
        if(strlen($text)==0) { return ""; }
        $newText = preg_replace_callback(
            '/(\%[0-9a-fA-F]{2})/',
            function($matches){ return hex2bin(substr($matches[0],1,2));},
            $text);
        return $newText;
    }
    
    /// Convert all '\<c>' sequences to '%xx' urlencoding. Is utf-8 multibyte aware.
    public static function backslashes2percenthex($text) {
        $textLen = mb_strlen($text);
        if($textLen==0) { return ""; }
        $newText = "";
        $backslash = "\\";
        $offset=0;
        while(true) {
            $backslashPos = mb_strpos($text,$backslash);
            if($backslashPos===false) {
                $newText .= $text;
                break;
            }
            $beforePart = mb_substr($text,0,$backslashPos);
            $newText .= $beforePart;
            $nextChar = mb_substr($text,$backslashPos+1,1);
            $newText .= self::percentHexEncode($nextChar);
            $text = mb_substr($text,$backslashPos+2);
            if($text=="") { break; }
        }
        return $newText;
    }

    /// Remove outer dblquotes or outer singlequotes.
    public static function dequote($text) {
        $textLen = mb_strlen($text);
        if($textLen<=2) { return $text; }
        $qq = '"';  // double quote character
        $q  = "'";  // single quote character
        $begChar = mb_substr($text,0,1);
        $endChar = mb_substr($text,-1);
        $newText = $text;
        if($begChar==$qq) {
            if($endChar==$qq) {
                $newText = mb_substr($text,1,-1);
            }
        } elseif($begChar==$q) {            
            if($endChar==$q) {
                $newText = mb_substr($text,1,-1);
            }
        }
        return $newText;
    }

    public static function parse($text,$delim=null) {
        #LongCiteUtil::writeToTty("\nParse:\nbeg: text='$text'.\n");
        $result = array();
        #// trim whole
        #$text = trim($text);
        #if($text=="") { return array($text); }
        // backslash to percent hex whole
        $text = self::backslashes2percenthex($text);
        #LongCiteUtil::writeToTty("hex: text='$text'.\n");
        #// dequote whole
        #$text = self::dequote($text);
        // delim whole
        if(is_null($delim)) {
            $parts = array($text);
        } else {
            $parts = explode($delim,$text);
        }
        // process parts
        #$partCnt=0;
        foreach($parts as $part) {
            #$partCnt++;
            #LongCiteUtil::writeToTty("part$partCnt: part='$part'.\n");
            // trim part
            $part = trim($part);
            #LongCiteUtil::writeToTty("part$partCnt trimmed: part='$part'.\n");
            // dequote part
            $part = self::dequote($part);
            // de-percent-hex part
            $part = self::percentHexDecode($part);
            // save part to result
            $result[] = $part;
        }
        return $result;
    }

    public static function writeToTty($text) {
        $tty = fopen("/dev/tty","a");
        if($tty===false) { return false; }
        $bytes = fwrite($tty,$text);
        if($bytes===false) { fclose($tty); return false; }
        return fclose($tty);
    }

    public static function i18nCache($langCode) {
        $langCode = mb_strtolower($langCode);
        $jsonFile = __DIR__ . "/../i18n/" . $langCode . ".json";
        $jsonArr = false;
        if(array_key_exists($langCode,self::$i18nCache)) {
            $jsonArr = self::$i18nCache[$langCode];
        } else {
            if(!file_exists($jsonFile)) {
                #trigger_error("File not found ($jsonFile).",E_USER_WARNING);
                return false;
            }
            $jsonStr = file_get_contents($jsonFile);
            if($jsonStr===false) {
                #trigger_error("Could not read ($jsonFile).",E_USER_WARNING);
                return false;
            }
            $jsonArr = json_decode($jsonStr,true);
            if(is_null($jsonArr)) {
                #trigger_error("Could not json_decode $jsonFile.",E_USER_WARNING);
                return false;
            }
            self::$i18nCache[$langCode] = $jsonArr;
        }
        return $jsonArr;
    }

    public static function i18MsgKeys($langCode,$pattern=null) {
        $langCode = mb_strtolower($langCode);
        $jsonArr  = self::i18nCache($langCode);
        if($jsonArr===false) { return false; }
        $msgKeys  = array_keys($jsonArr);
        if(is_null($pattern)) {
            $results = $msgKeya;
        } else {
            $results = array();
            foreach($msgKeys as $msgKey) {
                if(mb_ereg($pattern,$msgKey)!==false) {
                    $results[] = $msgKey;
                }
            }
        }
        return $results;
    }

    public static function i18nRender($langCode,$msgKey,...$params) {
        $langCode = mb_strtolower($langCode);
        $jsonArr  = self::i18nCache($langCode);
        if($jsonArr===false) {
            trigger_error("Language code $langCode not found.",E_USER_WARNING);
            return false;
        }
        if(!array_key_exists($msgKey,$jsonArr)) {
            trigger_error("Message key $msgKey not found in $langCode.",E_USER_WARNING);
            return false;
        }
        $result = $jsonArr[$msgKey];
        $n = 0;
        foreach($params as $param) {
            $n += 1;
            $result = str_replace('$'.$n,$param,$result);
        }
        return $result;
    }

    public static function i18nTranslateWord($word,$fromLang,$toLang,$keyPat,$prefGend) {
        $results = array();
        $testWord = mb_strtolower(trim($word));
        $fromLang = mb_strtolower($fromLang);
        $toLang   = mb_strtolower($toLang);
        $prefGend = mb_strtoupper($prefGend);
        $fromArr  = self::i18nCache($fromLang);
        $toArr    = self::i18nCache($toLang);
        // find appropriate msgKey in from lang.
        $fromMatch = null;
        foreach($fromArr as $msgKey => $fromMsgValStr) {
            if(mb_ereg($keyPat,$msgKey)===false) { continue; }
            $fromVals = mb_split('\;',$fromMsgValStr);
            foreach($fromVals as $fromVal) {
                $fromGendForms = mb_split('\/',$fromVal);
                $cntFromGendForms = count($fromGendForms);
                $maxIdx = $cntFromGendForms - 1;
                $idx = -1;
                $prefForm = "";
                foreach($fromGendForms as $fromGendForm) {
                    $idx++;
                    if($idx==0) {
                        if($idx<$maxIdx) {
                            $indGend = self::GenderMale;
                        } else {
                            $indGend = self::GenderUnknown;
                        }
                    } elseif($idx==1) {
                        $indGend = self::GenderFemale;
                    } elseif($idx==2) {
                        $indGend = self::GenderNeutral;
                    } else {
                        $indGend = self::GenderUnknown;
                    }
                    $fromGendForm = trim($fromGendForm);
                    $testForm1 = mb_strtolower($$fromGendForm);
                    $testForm2 = mb_ereg_replace('\.',"",$testForm1);
                    if($testWord==$testForm1 or $testWord==$testForm2) {
                        $fromMatch = array($msgKey,$indGend);
                        break 3;
                    }
                }
            }
        }
        // return false if no match
        if(is_null($fromMatch)) { return false; }
        // lookup preferred translation in to lang
        $msgKey  = $fromMatch[0];
        $indGend = $fromMatch[1];
        if(!array_key_exists($msgKey,$toArr)) { return false; }
        $toMsgKeyValStr = $toArr[$msgKey];
        # TBD - match gendered parts
        return $results;
    }
}
?>
