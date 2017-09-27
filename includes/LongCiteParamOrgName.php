<?php
/// Source code file for the LongCiteParamOrgName:: class.
/// MIT License.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

/// Parent Class for other LongCite tag classes.
class LongCiteParamOrgName extends LongCiteParam {

    public function __construct($paramNameKey, $isMulti, $tag) {
        parent::__construct($paramNameKey, $isMulti, $tag);
    }

    public function renderParamValues() {
        $tag = $this->getTag();
        $delim  = htmlspecialchars($this->getOutputDelim());
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
            $basicVal = $annValue[LongCiteParam::AnnValBasic];
            $htmlVal = htmlspecialchars($basicVal);
            $htmlValues[] = $htmlVal;
        }
        $stuff = implode($delim,$htmlValues);
        $tag->renderedOutputAdd($stuff,true);
    }

}
?>
