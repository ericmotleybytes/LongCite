<?php
/// Source code file for the LongCiteParamKey:: class.
/// MIT License.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

require_once __DIR__.'/LongCiteParamAlphaId.php';

/// Parent Class for other LongCite tag classes.
class LongCiteParamKey extends LongCiteParamAlphaId {

    public static function autoKey($nameObjs,$dateObjs,$exKeys=array()) {
        if(!is_array($nameObjs)) { $nameObjs = array($nameObjs); }
        if(!is_array($dateObjs)) { $dateObjs = array($dateObjs); }
        if(!is_array($exKeys))   { $exKeys   = array($exKeys); }
        // figure out the year part
        $years = array();
        foreach($dateObjs as $dateObj) {
            $years[] = $dateObj->getYear(true);
        }
        sort($years);
        $yearCnt = count($years);
        if($yearCnt==0) {
            $yearPart = "()";
        } elseif($yearCnt==1) {
            $yearPart = "($years[0])";
        } else {
            $year1 = array_shift($years);
            $year2 = array_pop($years);
            $yearPart = "($year1/$year2)";
        }
        $callable = array("LongCiteUtilDate","compareDateObjects");
        usort($dateObjs,$callable);
        // figure out the name part
        $names = array();
        foreach($nameObjs as $nameObj) {
            $name = $nameObj->getShortName();
            $names[] = $name;
        }
        // if more than 3 names, adjust names array
        $maxNames = 3;
        if(count($names)>$maxNames) {
            $names = array_slice($names,0,$maxNames);
            array_push($names,"...");
        }
        $namePart = implode(";",$names);
        $key = "$namePart $yearPart";
        $key = LongCiteUtil::eregTrim($key);
        // check if key is unique
        $keyBase = $key;
        $cnt = 0;
        while(in_array($key,$exKeys)) {
            $cnt++;
            if ($cnt>999) { return false; }
            $key = "$keyBase/$cnt";
        }
        return $key;
    }

    public function __construct($paramNameKey, $isMulti, $tag) {
        parent::__construct($paramNameKey, $isMulti, $tag);
    }

    public function renderParamValues() {
        $tag = $this->getTag();
        $delim  = $this->getOutputDelim();
        $htmlDelim = htmlspecialchars($delim);
        $valuesArr = $this->getBasicValues();
        $htmlValArr = array();
        foreach($valuesArr as $val) {
            $htmlValArr[] = '<b>' . htmlspecialchars($val) . '</b>';
        }
        $htmlStuff = implode($htmlDelim,$htmlValArr);
        $tag->renderedOutputAdd($htmlStuff,true);
    }

}
?>
