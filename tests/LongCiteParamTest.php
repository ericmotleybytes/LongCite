<?php
/// Source code file for LongCiteParamTest unit testing class.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

require_once __DIR__ . "/LongCiteWikiStub.php";

use PHPUnit\Framework\TestCase;

/// Some LongCite phpunit tests.
class LongCiteParamTest extends TestCase {

    /// Test functions.
    #public function testFunctions() {
    #    // initialize
    #    LongCiteMaster::clearActiveMaster();
    #    LongCiteWikiStub::initialize();
    #    $master = LongCiteMaster::getActiveMaster();
    #    // instantiate param
    #    $param = new LongCiteParam("dummy");
    #    $this->assertInstanceOf(LongCiteParam::class,$param);
    #    $this->assertFalse($param->getInputDelim());
    #    $this->assertEquals("dummy",$param->getNameKey());
    #    $long = LongCiteParam::ParamModeLong;
    #    $this->assertEquals("long",$long);
    #    $short = LongCiteParam::ParamModeShort;
    #    $this->assertEquals("short",$short);
    #    ##$longOutDelimMsg = $param->getOutputDelimMsgKey();
    #    ##$this->assertEquals("longcite-delimo-and",$longOutDelimMsg);
    #    ##$longOutDelimMsg = $param->getOutputDelimMsgKey($long);
    #    ##$this->assertEquals("longcite-delimo-and",$longOutDelimMsg);
    #    ##$shortOutDelimMsg = $param->getOutputDelimMsgKey($short);
    #    ##$this->assertEquals("longcite-delimo-semi",$shortOutDelimMsg);
    #    ##$longDelim = $param->getOutputDelim();
    #    ##$this->assertEquals(" and ",$longDelim);
    #    ##$longDelim = $param->getOutputDelim($long);
    #    ##$this->assertEquals(" and ",$longDelim);
    #    ##$shortDelim = $param->getOutputDelim($short);
    #    ##$this->assertEquals(";",$shortDelim);
    #    # not try some in german
    #    ##$GLOBALS["wgLang"] = "de";
    #    ##$longDelim = $param->getOutputDelim($long);
    #    ##$this->assertEquals(" und ",$longDelim);
    #    ### reset to english
    #    ##$GLOBALS["wgLang"] = "en";
    #}

    /// Test some static functions.
    public function testStaticFunctions() {
        // initialize
        LongCiteMaster::clearActiveMaster();
        LongCiteWikiStub::initialize();
        $master = LongCiteMaster::getActiveMaster();
        // statics
        $pk = "longcite-pn-author";
        $paramClass = LongCiteParam::getParamClass($pk);
        $this->assertEquals("LongCiteParamPersonName",$paramClass);
        $paramType = LongCiteParam::getParamType($pk);
        $this->assertEquals("PersonName",$paramType);
        $paramDescKey = LongCiteParam::getParamDescKey($pk);
        $this->assertEquals("longcite-pd-author",$paramDescKey);
        $paramDesc = LongCiteParam::getParamDescription($pk);
        $this->assertEquals("Specifies who wrote it.",$paramDesc);
    }

}
?>
