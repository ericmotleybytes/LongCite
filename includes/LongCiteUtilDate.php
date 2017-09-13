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
    const NumMonthFormat  = 10;
    const AbbrMonthFormat = 20;
    const FullMonthFormat = 30;

    protected $langCode    = "";     // e.g., "en" or "de".
    protected $locale      = "";     // e.g., "en-US" or "de-DE".
    protected $rawDateStr  = "";
    protected $bce     = "";
    protected $bceList = array();
    protected $ce      = "";
    protected $ceList  = array();
    protected $circa   = "";
    protected $circaList = array();
    protected $isCirca   = false;
    protected $isBCE     = false;
    protected $year      = null;
    protected $month     = null;
    protected $day       = null;
    protected $parsedOk  = false;
    protected $parsedMsg = "";
    protected $monthNamesLong  = array();
    protected $monthNamesShort = array();

    /// Class constructor.
    public function __construct($rawDateStr,$langCode="en") {
        $this->rawDateStr  = $rawDateStr;
        $this->setLangCode($langCode);
        $this->parsedOk = $this->parse($this->rawDateStr);
    }

    public function i18n($msgKeySuffix) {
        $msgKey = "longcite-date-" . $msgKeySuffix;
        $result = LongCiteUtil::i18nRender($this->langCode,$msgKey);
        return $result;
    }

    public function setLangCode($langCode) {
        $langCode = mb_strtolower($langCode);
        if($langCode==$this->langCode) { return; }  // already set
        $this->langCode = $langCode;
        $this->bce    = $this->i18n("bce");
        $this->ce     = $this->i18n("ce");
        $this->circa  = $this->i18n("circa");
        $this->bceList   = mb_split('\;',$this->bce);
        $this->ceList    = mb_split('\;',$this->ce);
        $this->circaList = mb_split('\;',$this->circa);
        $this->locale = $this->i18n("locale");
        $this->monthNamesLong  = array();
        $this->monthNamesShort = array();
        $saveLocale = setlocale(LC_TIME,0);
        $saveLocale = setlocale(LC_TIME,$this->locale);
        for($m=1; $m<=12; $m++) {
            $t = mktime(0,0,0,$m,1,2000);
            $this->monthNamesLong[$m]  = strftime('%B',$t);
            $this->monthNamesShort[$m] = strftime('%b',$t);
        }
        setlocale(LC_TIME,$saveLocale);  // restore locale
    }

    public function parse($dateStr) {
        $this->isCirca = false;
        $this->isBCE   = false;
        $this->year    = null;
        $this->month   = null;
        $this->day     = null;
        $this->parsedMsg = "";
        $adjDate = mb_strtolower(trim($dateStr));
        $adjDate = mb_ereg_replace('[\ \t]+'," ",$adjDate); // condense multi-spaces
        // check prefix for c.. ca. and circa
        #$circas = array($this->circa1,$this->circa2,$this->circa3);
        $circas = $this->circaList;
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
        #$bces = array($this->bc1,$this->bc2,$this->bce1,$this->bce2);
        $bces = $this->bceList;
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
            #$ces = array($this->ad1,$this->ad2,$this->ce1,$this->ce2);
            $ces = $this->ceList;
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
        $altDate = mb_ereg_replace('[\/\.\-\,]+'," ",$adjDate); // replace / . and ,
        $altDate = mb_ereg_replace('\ +'," ",$altDate); // condense multi-spaces
        $altParts = explode(" ",$altDate);
        $altPartsCnt = count($altParts);
        $year  = null;
        $month = null;
        $day   = null;
        $matches = array();
        $pat1 = '^([+-]?[0-9]+)$';   // just numeric year
        $pat2 = '^([+-]?[0-9]+)[\-\.\/]{1}([0-9]+)$';   // year-month
        $pat3 = '^([+-]?[0-9]+)[\-\.\/]{1}([0-9]+)[\-\.\/]{1}([0-9]+)$';  // yr-mon-day
        if(mb_ereg($pat1,$adjDate,$matches)!==false) {
            $year  = (int)$matches[1];
        } elseif(mb_ereg($pat2,$adjDate,$matches)!==false) {
            $year  = (int)$matches[1];
            $month = (int)$matches[2];
        } elseif(mb_ereg($pat3,$adjDate,$matches)!==false) {
            $year  = (int)$matches[1];
            $month = (int)$matches[2];
            $day   = (int)$matches[3];
        } elseif($altPartsCnt==2) {
            if(is_numeric($altParts[0])) {
                // should be like 1957 Oct
                $monthInfo = $this->lookupMonth($altParts[1]);
                if($monthInfo!==false) {
                    $month = $monthInfo[0];
                    $year  = (int)$altParts[0];
                }
            } elseif(is_numeric($altParts[1])) {
                // should be like Oct 1957
                $monthInfo = $this->lookupMonth($altParts[0]);
                if($monthInfo!==false) {
                    $month = $monthInfo[0];
                    $year  = (int)$altParts[1];
                }
            }
        } elseif($altPartsCnt==3) {
            if(is_numeric($altParts[0])) {
                if(is_numeric($altParts[2])) {
                    // should be like 4 Oct 1957
                    $monthInfo = $this->lookupMonth($altParts[1]);
                    if($monthInfo!==false) {
                        $day   = (int)$altParts[0];
                        $month = $monthInfo[0];
                        $year  = (int)$altParts[2];
                    }
                }
            } else {
                if(is_numeric($altParts[1]) and is_numeric($altParts[2])) {
                    // should be like Oct 4 1957
                    $monthInfo = $this->lookupMonth($altParts[0]);
                    if($monthInfo!==false) {
                        $day   = (int)$altParts[1];
                        $month = $monthInfo[0];
                        $year  = (int)$altParts[2];
                    }
                }
            }
        }
        #LongCiteUtil::writeToTty("\nY-M-D=$year-$month-$day\n");
        if($year!==null) {
            if($year<=0) {
                $this->isBCE = true;
                $year = abs($year-1);
            }
            $this->year = $year;
            if($month!==null) {
                if($month<0 or $month>12) {
                    $this->parsedMsg = "Bad month ($month)";
                    return $false;  // bad month
                }
                $this->month = $month;
                if($day!=null) {
                    if($day<0 or $day>31) {
                        $this->parsedMsg = "Bad day ($day)";
                        return false; // bad day
                    }
                    if($year>=1 and $year<=32767) {
                        $dateOk = checkdate($month,$day,$year);
                        if($dateOk===false) {
                            $this->parsedMsg = "Bad checkdate ($year-$month-$day)";
                            return false;
                        }
                    }
                    $this->day = $day;
                }
            }
            $this->parsedMsg = "ok";
            return true;
        }
        $this->parsedMsg = "Unrecognized ($dateStr).";
        return false;
    }

    public function lookupMonth($possibleMonth) {
        if(is_numeric($possibleMonth)) {
            $m = (int)$possibleMonth;
            if($m<1 or $m>12) { return false; }
            $long  = $this->monthNamesLong[$m];
            $short = $this->monthNamesShort[$m];
            return array($m,$long,$short);
        }
        $testStr = mb_strtolower($possibleMonth);
        for($m=1; $m<=12; $m++) {
            $long  = $this->monthNamesLong[$m];
            $short = $this->monthNamesShort[$m];
            if($testStr==mb_strtolower($long)) {
                return array($m,$long,$short);
            } elseif($testStr==mb_strtolower($short)) {
                return array($m,$long,$short);
            }
        }
        return false;
    }
    public function getLangCode() {
        return $this->langCode;
    }

    public function getDateStr($formatCode=LongCiteUtilDate::NumMonthFormat) {
        $formatCodes = array(
            self::NumMonthFormat,    // e.g., 1957-10-04
            self::AbbrMonthFormat,   // e.g., 04-Oct-1957
            self::FullMonthFormat    // e.g., 4 October 1957
        );
        if(!in_array($formatCode,$formatCodes)) {
            return "?" . $this->rawDateStr . "?";
        }
        $circaStrs = array(
            self::NumMonthFormat  => $this->circaList[1],
            self::AbbrMonthFormat => $this->circaList[1],
            self::FullMonthFormat => $this->circaList[0]
        );
        $datePart  = "";
        $circaPart = "";
        $eraPart   = "";
        if($this->isCirca) {
            $circaPart = $circaStrs[$formatCode] . " ";
        }
        if(is_null($this->year)) {
            return "?" . $this->rawDateStr . "?";
        } else {
            if($this->month==null) {
                $datePart .= $this->year;
            } else {
                // pad year when with month
                $yearStr = (string)$this->year;
                while(strlen($yearStr)<4) {
                    $yearStr = "0". $yearStr;
                }
                $datePart .= $yearStr;
            }
        }
        if($this->month!==null) {
            if($formatCode==self::NumMonthFormat) {
                $datePart .= "-" . substr("0".$this->month,-2);
            } elseif($formatCode==self::AbbrMonthFormat) {
                $monthInfo = $this->lookupMonth($this->month);
                $monthStr = $monthInfo[2];
                $datePart = $monthStr . "-" . $datePart;
            } else {
                $monthInfo = $this->lookupMonth($this->month);
                $monthStr = $monthInfo[1];
                $datePart = $monthStr . " " . $datePart;
            }
            if($this->day!==null) {
                if($formatCode==self::NumMonthFormat) {
                    $datePart .= "-" . substr("0".$this->day,-2);
                } elseif($formatCode==self::AbbrMonthFormat) {
                    $datePart = substr("0".$this->day,-2) . "-" . $datePart;
                } else {
                    $datePart = $this->day . " " . $datePart;
                }
            }
        }
        if($this->isBCE) {
            $eraPart .= " " . $this->bceList[0];
        } elseif($this->year < 1000) {
            $eraPart .= " " . $this->ceList[0];
        } elseif($this->year >= 10000) {
            $eraPart .= " " . $this->ceList[0];
        }
        $result = $circaPart . $datePart . $eraPart;
        return $result;
    }

    public function getIsCirca() {
        return $this->isCirca;
    }

    public function getIsBCE() {
        return $this->isBCE;
    }

    public function getYear() {
        return $this->year;
    }

    public function getMonth() {
        return $this->month;
    }

    public function getDay() {
        return $this->day;
    }

    public function getParsedOk() {
        return $this->parsedOk;
    }

    public function getParsedMsg() {
        return $this->parsedMsg;
    }

}
?>
