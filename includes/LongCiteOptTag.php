<?php
/// Source code file for the LongCiteOptTag:: class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.\n
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

/// Class for the <longciteopt> tag.
class LongCiteOptTag extends LongCiteTag {

    public function __construct($master) {
        parent::__construct($master);
    }

    public function render($content, $args, $parser, $frame) {
        $result = parent::render($content, $args, $parser, $frame);  // init html result
        $messenger = $this->master->getMessenger();
        $dbg = LongCiteMessenger::DebugType;
        $messenger->registerMessage($dbg,"In LongCiteOpt::render");
        $result .= $messenger->renderMessagesHtml();
        return $result;
    }
}
?>
