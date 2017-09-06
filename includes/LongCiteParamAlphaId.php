<?php
/// Source code file for the LongCiteParamAlphaId:: class.
/// MIT License.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

/// Parent Class for other LongCite tag classes.
class LongCiteParamAlphaId extends LongCiteParam {

    public function __construct($paramName) {
        parent::__construct($paramname);
        $this->isMulti = false;
    }

}
?>
