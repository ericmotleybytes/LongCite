<?php
/// Source code file for the LongCiteParamPersonName:: class.
/// MIT License.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

/// Parent Class for other LongCite tag classes.
class LongCiteParamPersonName extends LongCiteParam {

    public function __construct($paramNameKey,$tag) {
        parent::__construct($paramNameKey,$tag);
        $this->isMulti = true;
        $longMode = LongCiteParam::ParamModeLong;
        $shortMode = LongCiteParam::ParamModeShort;
        $this->setInputDelimMsgKey("longcite-delim-semi");
        $this->setOutputDelimMsgKey($longMode,"longcite-delim-and");
        $this->setOutputDelimMsgKey($shortMode,"longcite-delim-semi");
    }

}
?>
