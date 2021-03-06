<?php
/// Source code file for the LongCiteParamAuthor:: class.
/// MIT License.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

require_once __DIR__.'/LongCiteParamPersonName.php';

/// Parent Class for other LongCite tag classes.
class LongCiteParamAuthor extends LongCiteParamPersonName {

    public function __construct($paramNameKey, $isMulti, $tag) {
        parent::__construct($paramNameKey, $isMulti, $tag);
        $this->setRenderPrefixMsgKey("longcite-pre-author");
    }

}
?>
