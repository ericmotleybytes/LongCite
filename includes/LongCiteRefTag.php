<?php
/// Source code file for the LongCiteRefTag:: class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.\n
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

/// Class for the <longciteref> tag.
class LongCiteRefTag extends LongCiteTag {

    public function __construct($master, $input, $args, $parser, $frame=false) {
        parent::__construct($master, $input, $args, $parser, $frame);
    }

    public function render() {
        $this->setRenderedOutput(parent::render());
        return $this->renderedOutputAdd("");
    }

}
?>
