<?php
/// Source code file for the LongCiteParamPersonName:: class.
/// MIT License.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

/// Parent Class for other LongCite tag classes.
class LongCiteParamPersonName extends LongCiteParam {

    public function __construct($paramName) {
        parent::__construct($paramname);
        $this->isMulti = true;
        $longMode = LongCiteParam::ParamModeLong;
        $shortMode = LongCiteParam::ParamModeShort;
        $this->setOutputDelimMsgKey($longMode,"longcite-and-delim");
        $this->setOutputDelimMsgKey($shortMode,"longcite-semi-delim");
    }

}
?>
