<?php
/// Source code file for the LongCiteOptTag:: class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.\n
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

/// Class for the <longciteopt> tag.
class LongCiteOptTag extends LongCiteTag {

    /// Class constructor.
    /// @param $master - The LongCiteMaster instance doing the instantiation.
    /// @param $input - Content between <longciteopt> and </longciteopt>.
    /// @param $args - Hash array of settings within opening <longciteopt> tag.
    /// @param $parser - The parser object.
    /// @param $frame - Recursive parsing frame.
    public function __construct($master, $input, $args, $parser, $frame=false) {
        parent::__construct($master, $input, $args, $parser, $frame);
    }

    /// Process the <longciteopt> tag.
    /// @return A string with rendered HTML.
    public function render() {
        parent::renderPreperation();
        $this->setRenderedOutput("");
        $this->renderedOutputAdd($this->getMessenger()->renderMessagesHtml(true),true);
        return $this->renderedOutputGet();
    }

}
?>
