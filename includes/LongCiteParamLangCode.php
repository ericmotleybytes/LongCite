<?php
/// Source code file for the LongCiteParamLangCode:: class.
/// MIT License.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

/// For ISO 639-1 language codes (2 characters long).
class LongCiteParamLangCode extends LongCiteParam {

    public function __construct($paramNameKey,$isMulti,$tag) {
        parent::__construct($paramNameKey,$isMulti,$tag);
    }

    public function addValues($values) {
        $values = mb_strtolower($values);
        $result = parent::addValues($values);
        return $result;
    }

}
?>
