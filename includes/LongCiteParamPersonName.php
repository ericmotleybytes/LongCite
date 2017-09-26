<?php
/// Source code file for the LongCiteParamPersonName:: class.
/// MIT License.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

/// Class to manage tag parameters with person name values.
class LongCiteParamPersonName extends LongCiteParam {

    public function __construct($paramNameKey,$isMulti,$tag) {
        parent::__construct($paramNameKey,$isMulti,$tag);
        $longMode = LongCiteParam::ParamModeLong;
        $shortMode = LongCiteParam::ParamModeShort;
        $this->setInputDelimMsgKey("longcite-delimi-semi");
        #$this->setOutputDelimMsgKey($longMode,"longcite-delimo-and");
        $this->setOutputDelimMsgKey($longMode,"longcite-delimo-semi");
        $this->setOutputDelimMsgKey($shortMode,"longcite-delimo-semi");
    }

    public function addValues($valuesStr,$okHtml=null) {
        $result = true;
        $inLangCode = $this->getInputLangCode();
        parent::addValues($valuesStr,$okHtml);
        $cnt = count($this->annValues);
        for($idx=0; $idx<$cnt; $idx++) {
            if(!$this->annValues[$idx][self::AnnValIsValid]) { continue; }
            $basicVal = $this->annValues[$idx][self::AnnValBasic];
            $nameObj = new LongCiteUtilPersonName($basicVal,$inLangCode);
            $this->annValues[$idx][self::AnnValIsRecog] = true;
            $this->annValues[$idx][self::AnnValAsObj] = $nameObj;
        }
        return $result;
    }

    public function renderParamValues() {
        $tag = $this->getTag();
        $delim  = $this->getOutputDelim();
        $frLang = $this->getInputLangCode();
        $toLang = $this->getOutputLangCode();
        $annValues = $this->getAnnotatedValues();
        $htmlValues = array();
        $idx = -1;
        foreach($annValues as $annValue) {
            $idx++;
            if(!$annValue[LongCiteParam::AnnValIsValid]) {
                continue;
            }
            $nameObj = $annValue[self::AnnValAsObj];
            $nameFull = $nameObj->getRenderedNameAll($toLang);
            $htmlVal = htmlspecialchars($nameFull);
            $htmlValues[] = $htmlVal;
        }
        $stuff = implode($delim,$htmlValues);
        $tag->renderedOutputAdd($stuff,true);
    }

}
?>
