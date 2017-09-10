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
        $parser = $master->getParser();
        $frame  = new LongCiteWikiPPFrameStub(array());
        // instantiate param
        $tag = new LongCiteTag($master,"",array(),$parser,$frame);
        $pnameKey = "longcite-pn-author";
        $param = new LongCiteParamPersonName($pnameKey,true,$tag);
        $this->assertInstanceOf(LongCiteParamPersonName::class,$param);
        $param = LongCiteParam::newParam($pnameKey,$tag);
        $this->assertInstanceOf(LongCiteParamPersonName::class,$param);
        $param = $tag->newParam($pnameKey);
        $this->assertInstanceOf(LongCiteParamPersonName::class,$param);
        $tag2 = $param->getTag();
        $this->assertInstanceOf(LongCiteTag::class,$tag2);
        # info
        $this->assertTrue($param->isMulti());
        $nameMsgKey = $param->getNameKey();
        $this->assertEquals($pnameKey,$nameMsgKey);
        $name = $param->getName();
        $this->assertEquals("author",$name);
        $type = $param->getType();
        $this->assertEquals("PersonName",$type);
        $master = $param->getMaster();
        $this->assertInstanceOf(LongCiteMaster::class,$master);
        $this->assertEquals("en",$param->getInputLangCode());
        $this->assertEquals("en",$param->getOutputLangCode());
        # delims
        $this->assertEquals("longcite-delim-semi",$param->getInputDelimMsgKey());
        $this->assertEquals(";",$param->getInputDelim());
        $msgKey1 = $param->getOutputDelimMsgKey("long");
        $msgKey2 = $param->getOutputDelimMsgKey("short");
        $this->assertEquals("longcite-delim-and",$msgKey1);
        $this->assertEquals("longcite-delim-semi",$msgKey2);
        $this->assertEquals(" and ",$param->getOutputDelim("long"));
        $this->assertEquals(";",$param->getOutputDelim("short"));
        # values

    }

}
?>
