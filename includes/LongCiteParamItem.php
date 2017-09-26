<?php
/// Source code file for the LongCiteParamItem:: class.
/// MIT License.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

/// Parent Class for other LongCite tag classes.
class LongCiteParamItem extends LongCiteParamAlphaId {
    const CssClassRecognized   = "mw-longcite-pv-recogitem";
    const CssClassUnrecognized = "mw-longcite-pv-unrecogitem";

    public function __construct($paramNameKey, $isMulti, $tag) {
        parent::__construct($paramNameKey, $isMulti, $tag);
    }

    public function renderParamValues() {
        $tag = $this->getTag();
        $delim  = $this->getOutputDelim();
        $keyPat = '^longcite-itm-.*$';
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
            $tranVal = LongCiteUtil::i18nTranslateItem($basicVal,$frLang,$toLang,$keyPat);
            if($tranVal===false) {
                $this->annValues[$idx][LongCiteParam::AnnValIsRecog]==false;
                $htmlVal  = '<span class="' . self::CssClassUnrecognized . '">';
                $htmlVal .= htmlspecialchars($basicVal) . '</span>';
            } else {
                $this->annValues[$idx][LongCiteParam::AnnValIsRecog]==true;
                $htmlVal  = '<span class="' . self::CssClassRecognized . '">';
                $htmlVal .= htmlspecialchars($tranVal) . '</span>';
            }
            $htmlValues[] = $htmlVal;
        }
        $stuff = implode($delim,$htmlValues);
        $tag->renderedOutputAdd($stuff,true);
    }

}
?>
