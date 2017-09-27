<?php
/// Source code file for the LongCiteParamEdition:: class.
/// MIT License.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

require_once __DIR__.'/LongCiteParamString.php';

/// Item edition parameter class.
class LongCiteParamEdition extends LongCiteParamString {
    public function __construct($paramNameKey, $isMulti, $tag) {
        parent::__construct($paramNameKey, $isMulti, $tag);
        $this->setRenderSuffixMsgKey("longcite-suf-edition");
    }

}
?>
