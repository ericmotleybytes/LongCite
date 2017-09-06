<?php
/// Source code file for LongCiteParamPersonNameTest unit testing class.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

require_once __DIR__ . "/LongCiteWikiStub.php";

use PHPUnit\Framework\Testcase;

/// Some LongCite phpunit tests.
class LongCiteParamPersonNameTest extends Testcase {

    /// Test functions.
    public function testFunctions() {
        // initialize
        LongCiteMaster::clearActiveMaster();
        LongCiteWikiStub::initialize();
        $master = LongCiteMaster::getActiveMaster();
        // instantiate param
        $tag = new LongCiteTag($master);
        $pnameKey = "longcite-pn-author";
        $param = new LongCiteParamPersonName($pnameKey,$tag);
        $this->assertInstanceOf(LongCiteParamPersonName::class,$param);
        $param = LongCiteParam::newParam($pnameKey,$tag);
        $this->assertInstanceOf(LongCiteParamPersonName::class,$param);
        $param = $tag->newParam($pnameKey);
        $this->assertInstanceOf(LongCiteParamPersonName::class,$param);
        $tag2 = $param->getTag();
        $this->assertInstanceOf(LongCiteTag::class,$tag2);
        # values


        #$param = new LongCiteParam("dummy");
        #$this->assertInstanceOf(LongCiteParam::class,$param);
        #$this->assertFalse($param->getInputDelim());
        #$this->assertEquals("dummy",$param->getName());
        #$long = LongCiteParam::ParamModeLong;
        #$this->assertEquals("long",$long);
        #$short = LongCiteParam::ParamModeShort;
        #$this->assertEquals("short",$short);
        ##$longOutDelimMsg = $param->getOutputDelimMsgKey();
        ##$this->assertEquals("longcite-and-delim",$longOutDelimMsg);
        ##$longOutDelimMsg = $param->getOutputDelimMsgKey($long);
        ##$this->assertEquals("longcite-and-delim",$longOutDelimMsg);
        ##$shortOutDelimMsg = $param->getOutputDelimMsgKey($short);
        ##$this->assertEquals("longcite-semi-delim",$shortOutDelimMsg);
        ##$longDelim = $param->getOutputDelim();
        ##$this->assertEquals(" and ",$longDelim);
        ##$longDelim = $param->getOutputDelim($long);
        ##$this->assertEquals(" and ",$longDelim);
        ##$shortDelim = $param->getOutputDelim($short);
        ##$this->assertEquals(";",$shortDelim);
        # not try some in german
        ##$GLOBALS["wgLang"] = "de";
        ##$longDelim = $param->getOutputDelim($long);
        ##$this->assertEquals(" und ",$longDelim);
        ### reset to english
        ##$GLOBALS["wgLang"] = "en";
    }

}
?>
