<?php
/// Source code file for the LongCiteParamDate:: class.
/// MIT License.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

/// Class to manage tag parameters with date values.
class LongCiteParamDate extends LongCiteParam {

    const NumMonth  = LongCiteUtilDate::NumMonthFormat;
    const AbbrMonth = LongCiteUtilDate::AbbrMonthFormat;
    const FullMonth = LongCiteUtilDate::FullMonthFormat;

    protected $dateObjs = array();

    public function __construct($paramNameKey,$isMulti,$tag) {
        parent::__construct($paramNameKey,$isMulti,$tag);
        $longMode = LongCiteParam::ParamModeLong;
        $shortMode = LongCiteParam::ParamModeShort;
        $this->setInputDelimMsgKey("longcite-delimi-semi");
        $this->setOutputDelimMsgKey($longMode,"longcite-delimo-and");
        $this->setOutputDelimMsgKey($shortMode,"longcite-delimo-semi");
    }

    public function addValues($valuesStr) {
        $result = true;
        $inLangCode = $this->getInputLangCode();
        parent::addValues($valuesStr);
        $rawValues = parent::getValues();
        foreach($rawValues as $rawValue) {
            $dateObj = new LongCiteUtilDate($rawDate,$inLangCode);
            $this->dateObjs[] = $dateObj;
            if($dateObj->getParsedOk()===false) {
                $result = false;
                $mess = $this->getMessenger();
                $msg = wfMessage("longcite-err-unrecdate",$rawValue);
                $msg = $msg->inLanguage($inLangCode)->plain();
                $mess->registerMessageWarning($msg);
            }
        }
        return $result;
    }

    public function getValues($fmtCode=LongCiteParamDate::NumMonth) {
        $result = array();
        $outLangCode = $this->getOutputLangCode();
        foreach($this->dateObjs as $dateObj) {
            $dateObj->setLangCode($outLangCode);
            $result[] = $dateObj->getDateStr($fmtCode);
        }
        return $result;
    }
}
?>
