<?php
/// Source code file for LongCiteParamAlphaIdTest unit testing class.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

require_once __DIR__ . "/LongCiteWikiStub.php";

use PHPUnit\Framework\Testcase;

/// Some LongCite phpunit tests.
class LongCiteParamAlphaIdTest extends Testcase {

    /// Test functions.
    public function testFunctions() {
        // initialize
        LongCiteMaster::clearActiveMaster();
        LongCiteWikiStub::initialize();
        $master = LongCiteMaster::getActiveMaster();
        // instantiate param
        $tag = new LongCiteTag($master);
        $pnameKey = "longcite-pn-key";
        $param = new LongCiteParamAlphaId($pnameKey,$tag);
        $this->assertInstanceOf(LongCiteParamAlphaId::class,$param);
        $param = LongCiteParam::newParam($pnameKey,$tag);
        $this->assertInstanceOf(LongCiteParamAlphaId::class,$param);
        $param = $tag->newParam($pnameKey);
        $this->assertInstanceOf(LongCiteParamAlphaId::class,$param);
        $tag2 = $param->getTag();
        $this->assertInstanceOf(LongCiteTag::class,$tag2);
        # values

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
