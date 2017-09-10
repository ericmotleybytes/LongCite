<?php
/// Source code file for the LongCiteParamNote:: class.
/// MIT License.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

/// Parent Class for other LongCite tag classes.
class LongCiteParamNote extends LongCiteParam {

    public function __construct($paramNameKey,$isMulti,$tag) {
        parent::__construct($paramNameKey,$isMulti,$tag);
        $longMode = LongCiteParam::ParamModeLong;
        $shortMode = LongCiteParam::ParamModeShort;
        $this->setInputDelimMsgKey("longcite-delim-bar");
        $this->setOutputDelimMsgKey($longMode,"longcite-delim-alsonote");
        $this->setOutputDelimMsgKey($shortMode,"longcite-delim-bar");
    }

}
?>
