<?php
/// Source code file for the LongCiteUtilDate:: class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.\n
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

/// Class for working with dates.
/// Support dates such as:
class LongCiteUtilDate {

    protected $langCode    = "en";     /// e.g., "en" or "de"
    protected $rawDateStr  = "";
    protected $ad1     = "";
    protected $ad2     = "";
    protected $ce1     = "";
    protected $ce2     = "";
    protected $bc1     = "";
    protected $bc2     = "";
    protected $bce1    = "";
    protected $bce2    = "";
    protected $circa1  = "";
    protected $circa2  = "";
    protected $circa3  = "";
    protected $prefix  = "";
    protected $postfix = "";
    protected $dateParts = array();
    protected $isCirca   = false;
    protected $isBCE     = false;
    protected $year      = null;
    protected $month     = null;
    protected $day       = null;
    protected $parsedOk  = false;

    /// Class constructor.
    public function __construct($rawDateStr,$langCode="en") {
        $langCode = mb_strtolower($langCode);
        $this->rawDateStr  = $rawDateStr;
        $this->langCode = $langCode;
        $this->ad1    = $this->i18n("ad1");
        $this->ad2    = $this->i18n("ad2");
        $this->ce1    = $this->i18n("ce1");
        $this->ce2    = $this->i18n("ce2");
        $this->bc1    = $this->i18n("bc1");
        $this->bc2    = $this->i18n("bc2");
        $this->bce1   = $this->i18n("bce1");
        $this->bce2   = $this->i18n("bce2");
        $this->circa1 = $this->i18n("circa1");
        $this->circa2 = $this->i18n("circa2");
        $this->circa3 = $this->i18n("circa3");
        $this->parsedOk = $this->parse($this->rawDateStr);
    }

    public function i18n($msgKeySuffix) {
        $msgKey = "longcite-date-" . $msgKeySuffix;
        $result = LongCiteUtil::i18nRender($this->langCode,$msgKey);
        return $result;
    }

    function parse($dateStr) {
        $this->isCirca = false;
        $this->isBCE   = false;
        $this->year    = null;
        $this->month   = null;
        $this->day     = null;
        $adjDate = strtolower(trim($dateStr));
        // check prefix for c.. ca. and circa
        $circas = array($this->circa1,$this->circa2,$this->circa3);
        foreach($circas as $circa) {
            $circa = mb_strtolower($circa);
            $circaLen = mb_strlen($circa);
            $testStr = mb_strtolower(mb_substr($adjDate,0,$circaLen));
            if($testStr==$circa) {
                $this->isCirca = true;
                $adjDate = trim(mb_substr($adjDate,$circaLen));
                break;
            }
        }
        $bces = array($this->bc1,$this->bc2,$this->bce1,$this->bce2);
        foreach($bces as $bce) {
            $bce = mb_strtoupper($bce);
            $bceLen = mb_strlen($bce);
            $adjDateLen = mb_strlen($adjDate);
            $testStr = mb_strtoupper(mb_substr($adjDate,-$bceLen));
            if($testStr==$bce) {
                $this->isBCE = true;
                $adjDate = trim(mb_substr($adjDate,0,$adjDateLen-$bceLen));
                break;
            }
        }
        $ces = array();
        if($this->isBCE===false) {
            $ces = array($this->ad1,$this->ad2,$this->ce1,$this->ce2);
        }
        foreach($ces as $ce) {
            $ce = mb_strtoupper($ce);
            $ceLen = mb_strlen($ce);
            $adjDateLen = mb_strlen($adjDate);
            $testStr = mb_strtoupper(mb_substr($adjDate,-$ceLen));
            if($testStr==$ce) {
                $adjDate = trim(mb_substr($adjDate,0,$adjDateLen-$ceLen));
                break;
            }
        }
        if(preg_match('/^\-?[0-9]+$/',$adjDate)===1) {
            // year only
            $year = (int)$adjDate;
            if($this->isBCE===false) {
                if($year<0) {
                    $this->isBCE = true;
                    $year = -$year;
                }
            }
            $year = abs($year);
            $this->year = $year;
            return true;
        }
        $dateParts = date_parse($adjDate);
        if($dateParts===false) {
            // could not parse date
            return false;
        }
        $year  = $dateParts["year"];
        $month = $dateParts["month"];
        $day   = $dateParts["day"];
        if($year===false) {
            // could not parse critical year part
            return false;
        }
        if($year<0) { $this->isBCE = true; }
        $year = abs($year);
        $this->year = $year;
        if($month!==false) { $this->month = $month; }
        if($day!==false)   { $this->day   = $day; }
        return true;
    }

    public function getDateStr() {
        $result = "";
        if($this->isCirca) {
            $result .= $this->circa1 . " ";
        }
        if(is_null($this->year)) {
            return "?" . $this->rawDateStr . "?";
        } else {
            $result .= $this->year;
        }
        if($this->month!==null) {
            $result .= "-" . substr("0".$this->month,-2);
            if($this->day!==null) {
                $result .= "-" . substr("0".$this->day,-2);
            }
        }
        if($this->isBCE) {
            $result .= " " . $this->bce1;
        } elseif($this->year < 1000) {
            $result .= " " . $this->ce1;
        }
        return $result;
    }
}
?>
