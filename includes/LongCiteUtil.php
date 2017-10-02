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

    public static function eregTrim($str) {
        $str = mb_ereg_replace('^[\ \t\n]+',"",$str); // trim leading spaces and tabs
        $str = mb_ereg_replace('[\ \t\n]+$',"",$str); // trim trailing spaces and tabs
        return $str;
    }

    /// RegEx quote a multibyte string to prep for mb_split.
    public static function eregQuote($delim) {
        $regexCharArr = array("\\",'+','*','?','[','^',']','$',
            '(',')','{','}','=','!','<','>','|',':','-',' ','/');
        $delimCharArr = preg_split('//u',$delim,null,PREG_SPLIT_NO_EMPTY);
        $result = "";
        foreach($delimCharArr as $delimChar) {
            if(in_array($delimChar,$regexCharArr)) {
                $result .= "\\" . $delimChar;
            } else {
                $result .= $delimChar;
            }
        }
        return $result;
    }

    public static function parseValuesStr($text,$delim=null) {
        $result = array();
        // backslash to percent hex whole
        $text = self::backslashes2percenthex($text);
        // delim whole
        if(is_null($delim)) {
            $parts = array($text);
        } else {
            $delimReg = self::eregQuote($delim);
            $parts = mb_split($delimReg,$text);
        }
        // process parts
        foreach($parts as $part) {
            // trim part
            $part = self::eregTrim($part);
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

    /// Use i18n definitions to translate an word read from input to a preferred
    /// word (possibly in a different language) using gender clues if available.
    /// This is used in the LongCiteUtilPersonName class.
    /// For example, the longcite-nst-* i18n message keys equate to ";" delimited
    /// name titles (such as Mr. or Mrs.). Any of the multiple title variations
    /// for a single title are listed, but the first one is the preferred form.
    /// The source can match any of the listed forms, but the translation will
    /// be the preferred (first) form. In addition, each form can have up to
    /// three gender specific variations, "/" delimited, in "masculine/feminine/neutral"
    /// forms. No slashes means all genders use the same form variation.
    public static function i18nTranslateWord($word,$fromLang,$toLang,
        $keyPat,$prefGend=null,&$indGend = null) {
        if($prefGend===null) { $prefGend = self::GenderUnknown; }
        if($indGend===null)  { $indGend  = self::GenderUnknown; }
        $testWord = mb_strtolower(self::eregTrim($word));
        $fromLang = mb_strtolower($fromLang);
        $toLang   = mb_strtolower($toLang);
        $prefGend = mb_strtoupper($prefGend);
        $fromArr  = self::i18nCache($fromLang);
        $toArr    = self::i18nCache($toLang);
        // find appropriate msgKey using from lang value.
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
                    $fromGendForm = self::eregTrim($fromGendForm);
                    $testForm1 = mb_strtolower($fromGendForm);
                    $testForm2 = mb_ereg_replace('\.',"",$testForm1);
                    if($testWord==$testForm1 or $testWord==$testForm2) {
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
                        // if word doesn't give gender clue, caller paramerter.
                        if($indGend==self::GenderUnknown) {
                            $indGend = $prefGend;
                        }
                        $fromMatch = array($msgKey,$indGend);
                        break 3;
                    }
                }
            }
        }
        // return false if no match
        if(is_null($fromMatch)) { return false; }
        // lookup preferred translation in to lang
        $trans =false;
        $msgKey  = $fromMatch[0];
        $indGend = $fromMatch[1];
        if(!array_key_exists($msgKey,$toArr)) { return false; }
        $toMsgValStr = $toArr[$msgKey];
        $toVals = mb_split('\;',$toMsgValStr);
        $toPrefVal = $toVals[0];
        $toGendForms = mb_split('\/',$toPrefVal);
        $cntToGendForms = count($toGendForms);
        if($cntToGendForms==1) {
            $trans = $toGendForms[0];
        } elseif($cntToGendForms==2) {
            if($indGend==self::GenderFemale) {
                $trans = $toGendForms[1];
            } else {
                $trans = $toGendForms[0];
            }
        } elseif($cntToGendForms>=3) {
            if($indGend==self::GenderMale) {
                $trans = $toGendForms[0];
            } elseif($indGend==self::GenderFemale) {
                $trans = $toGendForms[1];
            } elseif($indGend==self::GenderNeutral) {
                $trans = $toGendForms[2];
            } else {
                $trans = $toGendForms[2];
            }
        }
        return $trans;
    }

    /// Use i18n definitions to translate an item clause read from input to a preferred
    /// item clause (possibly in a different language) accepting variation hints.
    /// This is used in the LongCiteParamItem class.
    /// For example, the longcite-itm-* i18n message keys equate to ";" delimited
    /// item clauses (such as 'newspaper article' or 'radio program').
    /// Any of the multiple title variations for a item are listed, but the first one
    /// is the preferred form. The source can match any of the listed forms, but the
    /// translation will be the preferred (first) form. In addition, each form can
    /// optionally have a [] prefix whixh indicates the proper form of "a" and "the",
    /// such as [A/The] or [An/The]. The first variation within the [] brackets is
    /// used with the preferred form.
    public static function i18nTranslateItem($item,$fromLang,$toLang,$keyPat) {
        $testItem = mb_strtolower(self::eregTrim($item));
        $fromLang = mb_strtolower($fromLang);
        $toLang   = mb_strtolower($toLang);
        $fromArr  = self::i18nCache($fromLang);
        $toArr    = self::i18nCache($toLang);
        // find appropriate msgKey using from lang value.
        $matchedMsgKey = null;
        foreach($fromArr as $msgKey => $fromMsgValStr) {
            if(mb_ereg($keyPat,$msgKey)===false) { continue; }
            $fromVals = mb_split('\;',$fromMsgValStr);
            foreach($fromVals as $fromVal) {
                // look for variants in [] brackets
                $fromVariants = self::i18nVariants($fromVal);
                foreach($fromVariants as $fromVar) {
                    $fromVar = mb_strtolower($fromVar);
                    if($testItem==$fromVar) {
                        // found a match!
                        $matchedMsgKey = $msgKey;
                        break 3;
                    }
                }
            }
        }
        // return false if no match
        if(is_null($matchedMsgKey)) { return false; }
        // lookup preferred translation in to lang
        $trans =false;
        $msgKey  = $matchedMsgKey;
        if(!array_key_exists($msgKey,$toArr)) { return false; }
        $toMsgValStr = $toArr[$msgKey];
        $toVals = mb_split('\;',$toMsgValStr);
        $toPrefVal = $toVals[0];
        $toVars = self::i18nVariants($toPrefVal);
        $toPrefVar = $toVars[0];
        return $toPrefVar;
    }

    public static function i18nVariants($formStr) {
        $results = array();
        $matches = array();
        $util = "LongCiteUtil";
        $pat = '/\[[^\]]*\]/u';
        $cnt = preg_match_all($pat,$formStr,$matches,PREG_PATTERN_ORDER);
        if($cnt===false) {
            trigger_error("preg_match_all problem.",E_USER_WARNING);
            return false;
        } elseif($cnt=0) {
            $results = array($formStr);
            return $results;
        } else {
            $mats = $matches[0];
            $matCnt = 0;
            $variants = array();
            $base = array("");
            foreach($mats as $mat) {
                $matCore = mb_substr($mat,1,-1);
                $matIdx = $matCnt;
                $matCnt++;
                $marker = '[[' . $matCnt . ']]';
                $matPat = '/' . $util::eregQuote($mat) . '/u';
                $formStr = preg_replace($matPat,$marker,$formStr,1);
                $vars = mb_split('/',$matCore);
                array_push($vars,"");  // add empty string variant
                $newBase = array();
                foreach($base as $b) {
                    $varCnt = 0;
                    foreach($vars as $var) {
                        $varIdx = $varCnt;
                        $varCnt++;
                        $newBase[] = "$b/$var";
                    }
                }
                $base = $newBase;
            }
            foreach($base as $b) {
                $b = mb_ereg_replace('^/',"",$b);  // remove xtra leading slash
                $subs = mb_split('/',$b);
                $subCnt = 0;
                $workStr = $formStr;
                foreach($subs as $sub) {
                    $subCnt++;
                    $subPat = $util::eregQuote('[['.$subCnt.']]');
                    $workStr = mb_ereg_replace($subPat,$sub,$workStr);
                }
                $workStr = $util::eregTrim($workStr);
                $workStr = mb_ereg_replace('[\ ]+'," ",$workStr); // collapse
                $results[] = $workStr;
            }
        }
        return $results;
    }

    /// Test if a variable in an associative array.
    /// @param $var - The variable to test.
    /// @return TRUE if $var is an associative array, else false.
    public static function isArrayAssociative($var) {
        if (!is_array($var)) {
            return false;
        }
        $compare = array_diff_key($var,array_keys(array_keys($var)));
        if (count($compare)==0) {
            return false;
        } else {
            return true;
        }
    }

    /// Test if an array is infinitely recursive.
    public static function isArrayInfinite(&$var) {
        $testStr = "__been_here_before__";
        if (!is_array($var)) {
            return false;
        }
        // if this key is present, it means you already walked this array
        if(isset($var[$testStr])){
            return true; // we have been here before
        }
        $var[$testStr] = true;
        $infFound = false;
        foreach($var as $key => &$value) {
            if($key !== $testStr) {
                if(is_array($value)) {
                    $flag = self::isArrayInfinite($value);  // recurse
                    if($flag) {
                        $infFound = true;
                        break;
                    }
                }
            }
        }
        // unset when done because working with a reference...
        unset($var[$testStr]);
        return $infFound;
    }

    /// Get array depth, or -1 in infinitely recursive.
    public static function getArrayDepth(&$var) {
        $testStr = "__been_here_before__";
        $depth = 0;
        if (!is_array($var)) {
            return $depth;  // not an array
        }
        $depth++;
        // if this key is present, it means you already walked this array
        if(isset($var[$testStr])){
            return -1; // we have been here before
        }
        $var[$testStr] = true;
        $infFound = false;
        $maxSubDepth = 0;
        foreach($var as $key => &$value) {
            if($key !== $testStr) {
                if(is_array($value)) {
                    $subDepth = self::getArrayDepth($value);  // recurse
                    if($subDepth<0) {
                        $infFound = true;
                        break;
                    }
                    $maxSubDepth = max($maxSubDepth,$subDepth);
                }
            }
        }
        // unset when done because working with a reference...
        unset($var[$testStr]);
        if($infFound) {
            return -1;
        } else {
            $finalDepth = $depth + $maxSubDepth;
            return $finalDepth;
        }
    }

        public static function debugVariableToString($var,$maxLen=100) {
        $varType = gettype($var);
        if(is_null($var)) {
            $varVal = "null";
        } elseif(is_string($var)) {
            $varVal  = mb_ereg_replace('\n','<nl>',$var);
        } elseif(is_bool($var)) {
            if($var) { $varVal="true"; } else { $varVal="false"; }
        } elseif(is_array($var)) {
            $varCnt = count($var);
            $isAssoc = LongCiteUtil::isArrayAssociative($var);
            if($isAssoc) {
                $varType .= "/assoc/$varCnt";
            } else {
                $varType .= "/plain/$varCnt";
            }
            $isInfinite = LongCiteUtil::isArrayInfinite($var);
            if($isInfinite) {
                $varType .= "/infinite";
                $varVal = "?";
            } elseif($isAssoc) {
                $idx = -1;
                $varVal='[';
                foreach($var as $key => $val) {
                    $idx++;
                    if($idx>1) { $varVal .= ',...'; break; }
                    if($idx>0) { $varVal .= ','; }
                    $varVal .= $key . '=>';
                    if(is_array($val)) {
                        if(LongCiteUtil::isArrayAssociative($val)) {
                            $varVal .= '(array/assoc)';
                        } else {
                            $varVal .= '(array/plain)';
                        }
                    } else {
                        $varVal .= self::debugVariableToString($val,20);
                    }
                }
                $varVal.=']';
            } else {
                $idx = -1;
                $varVal='[';
                foreach($var as $val) {
                    $idx++;
                    if($idx>1) { $varVal .= ',...'; break; }
                    if($idx>0) { $varVal .= ','; }
                    if(is_array($val)) {
                        if(LongCiteUtil::isArrayAssociative($val)) {
                            $varVal .= '(array/assoc)';
                        } else {
                            $varVal .= '(array/plain)';
                        }
                    } else {
                        $varVal .= self::debugVariableToString($val,20);
                    }
                }
                $varVal.=']';
            }
        } elseif(is_numeric($var)) {
            $varVal = $var;
        } elseif(is_resource($var)) {
            $varVal = "(resource)";
        } elseif(is_object($var)) {
            $varVal = get_class($var) . "(classname)";
            if(method_exists($var,"__toString")) {
                $varVal .= '=' . mb_ereg_replace('\n','<nl>',$var->__toString());
            }
        } else {
            $varVal = print_r($var,true);
        }
        if(mb_strlen($varVal)>$maxLen) {
            $varVal = mb_substr($varVal,0,$maxLen) . "...";
        }
        return "($varType)'$varVal'";
    }

    public static function lookupArrayEntry($arr,$key,$default=null) {
        if(!array_key_exists($key,$arr)) { return $default; }
        return $arr[$key];
    }
}
?>
