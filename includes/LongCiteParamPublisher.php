<?php
/// Source code file for the LongCiteParamPublisher:: class.
/// MIT License.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

/// Parent Class for other LongCite tag classes.
class LongCiteParamPublisher extends LongCiteParamOrgName {

    public function __construct($paramNameKey, $isMulti, $tag) {
        parent::__construct($paramNameKey, $isMulti, $tag);
        $this->setRenderPrefixMsgKey("longcite-pre-publisher");
    }

}
?>
