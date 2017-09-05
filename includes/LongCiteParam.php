<?php
/// Source code file for the LongCiteTag:: class.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

/// Parent Class for other LongCite tag classes.
class LongCiteParam {
    protected $paramName = "";
    protected $paramType = "";        ///< Param type.
    protected $isMulti   = false;     ///< Are multivalues allowed.
    protected $defDelim  = ";";       ///< Multi value delimiter (if needed).

    public function __construct($paramName,$isMulti=false) {
        $this->paramName = $paramName;
        $this->paramType = "String";
        $this->isMulti   = $isMulti;
        $this->defDelim  = ";";
    }

    public function getParamName() {
        return $this->paramName;
    }

    public function getParamType() {
        return $this->paramType;
    }

}
?>
