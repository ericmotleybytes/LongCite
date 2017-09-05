<?php
/// Source code file for the LongCiteDefTag:: class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.\n
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

/// Class for the <longcitedef> tag.
/// The point of this tag is to define citation details for use on the
/// current page and optionally to be stored in the database so that
/// these citation details can be used from other pages. It is also
/// optional whether or not these citation details are visibly rendered
/// onto the defining page or are defined silently (generally to be
/// rendered later via <longciteren>).
class LongCiteDefTag extends LongCiteTag {

    static protected $paramTypesDef = array(
        "id"     => "StringIdentifier/A",
        "author" => "PersonName/A"
    );

    /// Class constructor.
    /// @param $master - The LongCiteMaster instance doing the instantiation.
    public function __construct($master) {
        parent::__construct($master);
        $this->paramTypes = LongSiteTag::$paramTypesDef;
    }

    /// Process the <longcitedef> tag.
    /// @param $input - Content between <longcitedef> and </longcitedef>.
    /// @param $args - Hash array of settings within opening <longcitedef> tag.
    /// @param $parser - The parser object.
    /// @param $frame - Recursive parsing frame.
    /// @return A string with rendered HTML.
    public function render($content, $args, $parser, $frame) {
        $result = parent::render($content, $args, $parser, $frame);  // init html result
        $messenger = $this->master->getMessenger();
        $dbg = LongCiteMessenger::DebugType;
        $messenger->registerMessage($dbg,"In LongCiteHlp::render");
        $result .= $messenger->renderMessagesHtml();
        return $result;
    }

}
?>
