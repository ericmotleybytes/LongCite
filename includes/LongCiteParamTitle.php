<?php
/// Source code file for the LongCiteParamTitle:: class.
/// MIT License.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

/// Parent Class for other LongCite tag classes.
class LongCiteParamTitle extends LongCiteParamString {

    public function __construct($paramNameKey, $isMulti, $tag) {
        parent::__construct($paramNameKey, $isMulti, $tag);
        $this->setRenderPrefixMsgKey("longcite-pre-title");
    }

    public function renderParamValues() {
        $tag = $this->getTag();
        $delim  = htmlspecialchars($this->getOutputDelim());
        $frLang = $this->getInputLangCode();
        $toLang = $this->getOutputLangCode();
        $quoteOpen  = $this->wikiMessageOut("longcite-pun-quoteopen")->plain();
        $quoteClose = $this->wikiMessageOut("longcite-pun-quoteclose")->plain();
        $annValues = $this->getAnnotatedValues();
        $htmlValues = array();
        $idx = -1;
        foreach($annValues as $annValue) {
            $idx++;
            if(!$annValue[LongCiteParam::AnnValIsValid]) {
                continue;
            }
            $basicVal = $annValue[LongCiteParam::AnnValBasic];
            $qVal = $quoteOpen . $basicVal . $quoteClose;
            $htmlVal = '<i>'.htmlspecialchars($qVal).'</i>';
            $htmlValues[] = $htmlVal;
        }
        $stuff = implode($delim,$htmlValues);
        $tag->renderedOutputAdd($stuff,true);
    }

}
?>
