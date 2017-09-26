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

    public function __construct($paramNameKey,$isMulti,$tag) {
        parent::__construct($paramNameKey,$isMulti,$tag);
        $longMode = LongCiteParam::ParamModeLong;
        $shortMode = LongCiteParam::ParamModeShort;
        $this->setInputDelimMsgKey("longcite-delimi-semi");
        $this->setOutputDelimMsgKey($longMode,"longcite-delimo-and");
        $this->setOutputDelimMsgKey($shortMode,"longcite-delimo-semi");
    }

    public function addValues($valuesStr,$okHtml=null) {
        $result = true;
        $inLangCode = $this->getInputLangCode();
        parent::addValues($valuesStr,$okHtml);
        $cnt = count($this->annValues);
        for($idx=0; $idx<$cnt; $idx++) {
            if(!$this->annValues[$idx][self::AnnValIsValid]) { continue; }
            $basicVal = $this->annValues[self::AnnValBasic];
            $dateObj = new LongCiteUtilDate($basicVal,$inLangCode);
            if($dateObj->getParsedOk()===false) {
                $result = false;
                $mess = $this->getMessenger();
                $msg = wfMessage("longcite-err-unrecdate",$basicVal);
                $msg = $msg->inLanguage($inLangCode)->plain();
                $mess->registerMessageWarning($msg);
                $this->annValues[$idx][self::AnnValIsRecog] = false;
            } else {
                $this->annValues[$idx][self::AnnValIsRecog] = true;
            }
            $this->annValues[$idx][self::AnnValAsObj] = $dateObj;
        }
        return $result;
    }

    public function getFormattedValues($fmtCode=LongCiteParamDate::NumMonth) {
        $result = array();
        $outLangCode = $this->getOutputLangCode();
        foreach($this->annValues as $annValue) {
            $dateObj = $annValue[self::AnnValAsObj];
            $result[] = $dateObj->getDateStr($fmtCode);
        }
        return $result;
    }
}
?>
