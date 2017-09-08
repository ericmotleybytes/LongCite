<?php
/// Source code file for the LongCiteRenTag:: class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.\n
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

/// Class for the <longciteren> tag.
class LongCiteRenTag extends LongCiteTag {

    public function __construct($master, $input, $args, $parser, $frame=false) {
        parent::__construct($master, $input, $args, $parser, $frame);
    }

    public function render() {
        $result = parent::render();  // init html result
        $this->getMaster()->renderTrace();
        return $result;
    }

}
?>
