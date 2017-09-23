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

    protected $validDefParamNames = array(
        "id", "author"
    );

    /// Class constructor.
    /// @param $master - The LongCiteMaster instance doing the instantiation.
    /// @param $input - Content between <longcitedef> and </longcitedef>.
    /// @param $args - Hash array of settings within opening <longcitedef> tag.
    /// @param $parser - The parser object.
    /// @param $frame - Recursive parsing frame.
    public function __construct($master, $input, $args, $parser, $frame=false) {
        parent::__construct($master, $input, $args, $parser, $frame);
        #$mess = $this->getMessenger();
        #$mess->setEnableDebug(true);  // DBG true just for debugging
        #$mess->setDoTrigger(true);    // DBG true just for debugging
        // clear all param msg keys
        $this->clearParamMsgKeys();
        // lang msgkey
        $this->addParamMsgKeys("alwayslang");
        // ctrl msg keys
        $this->addParamMsgKeys("render","renlang","renctrl","rencore","rendesc","renverb");
        $this->addParamMsgKeys("renlong","renskip","renonly");
        // core msg keys
        $this->addParamMsgKeys("key");
        // desc msg keys
        $this->addParamMsgKeys("author","item","pubdate");
        // verbose msg keys
        $this->addParamMsgKeys("note");
    }

    /// Process the <longcitedef> tag.
    /// @return A string with rendered HTML.
    public function render() {
        parent::renderPreperation();
        // init rendering
        $this->setRenderedOutput("");
        $paramObjHash = $this->getParamObjectHash();
        // Process control params.
        foreach($paramObjHash as $paramMsgKey => $paramObj) {
            if($paramObj->getCategory()!=LongCiteParam::CatCore) { continue; }
            $paramNameMsgKey = $paramObj->getNameKey();
            if($paramNameMsgKey=="longcite-pn-renlang") {
                $paramClass = LongCiteParam::getParamClass($paramNameMsgKey);
                $values = $paramObj->getBasicValues();
                $outLangCode = $values[0];
                $tag->setOutputLangCode($outLangCode);
            }
        }
        // TBD
        // Render objects to display
        // render core params, if any.
        foreach($paramObjHash as $paramMsgKey => $paramObj) {
            if($paramObj->getCategory()!=LongCiteParam::CatCore) { continue; }
            $status = $paramObj->renderParam();
        }
        // render description params, if any.
        if(array_key_exists("longcite-pn-item",$paramObjHash)) {
            $paramObj = $paramObjHash["longcite-pn-item"];
            $status = $paramObj->renderParam();
        }
        // Render possible registered warning/error messages.
        $mess = $this->getMessenger();
        $html = $mess->renderMessagesHtml(true);
        $this->renderedOutputAdd($html,true);
        $result = LongCiteUtil::eregTrim($this->renderedOutputGet());
        return $result;
    }

}
?>
