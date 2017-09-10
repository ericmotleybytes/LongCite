<?php
/// Source code file for the LongCiteParamBoolean:: class.
/// MIT License.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

/// For true/false/null values.
class LongCiteParamBoolean extends LongCiteParam {

    public function __construct($paramNameKey,$isMulti,$tag) {
        parent::__construct($paramNameKey,$isMulti,$tag);
    }

    public function addValues($values) {
        $values = mb_strtolower($values);
        $result = parent::addValues($values);
        return $result;
    }

    public function isValueValid($valueStr) {
        $check  = $this->parseBooleanValue($values,false);
        if(is_null($check)) {
            return false;
        }
        return true;
    }

}
?>
