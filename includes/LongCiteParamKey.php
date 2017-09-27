<?php
/// Source code file for the LongCiteParamKey:: class.
/// MIT License.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

require_once __DIR__.'/LongCiteParamAlphaId.php';

/// Parent Class for other LongCite tag classes.
class LongCiteParamKey extends LongCiteParamAlphaId {

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
