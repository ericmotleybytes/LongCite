<?php
/// Source code file for LongCiteParamAlphaIdTest unit testing class.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

require_once __DIR__ . "/LongCiteWikiStub.php";

use PHPUnit\Framework\TestCase;

/// Some LongCite phpunit tests.
class LongCiteParamAlphaIdTest extends TestCase {

    /// Test functions.
    public function testFunctions() {
        // initialize
        LongCiteMaster::clearActiveMaster();
        LongCiteWikiStub::initialize();
        $master = LongCiteMaster::getActiveMaster();
        $parser = $master->getParser();
        $frame  = new LongCiteWikiPPFrameStub(array());
        // instantiate tag and param
        $tag = new LongCiteTag($master,"",array(),$parser,$frame);
        $tag->setOutputLangCode("en");
        $pnameKey = "longcite-pn-key";
        $param = new LongCiteParamAlphaId($pnameKey,false,$tag);
        $this->assertInstanceOf(LongCiteParamAlphaId::class,$param);
        $param = LongCiteParam::newParam($pnameKey,$tag);
        $this->assertInstanceOf(LongCiteParamAlphaId::class,$param);
        $param = $tag->newParam($pnameKey);
        $this->assertInstanceOf(LongCiteParamAlphaId::class,$param);
        # info
        $tag = $param->getTag();
        $this->assertInstanceOf(LongCiteTag::class,$tag);
        $parser2 = $param->getParser();
        $this->assertEquals($parser,$parser2);
        $frame2 = $param->getFrame();
        $this->assertEquals($frame,$frame2);
        $this->assertFalse($param->isMulti());
        $nameMsgKey = $param->getNameKey();
        $this->assertEquals($pnameKey,$nameMsgKey);
        $names = $param->getNames();
        $this->assertEquals("key",$names[0]);
        $type = $param->getType();
        $this->assertEquals("AlphaId",$type);
        $master = $param->getMaster();
        $this->assertInstanceOf(LongCiteMaster::class,$master);
        $this->assertEquals("en",$param->getInputLangCode());
        $this->assertEquals("en",$param->getOutputLangCode());
        # values
        $v1 = "Einstein (1920)";
        $v2 = "Smith (1902)";
        $param->addValues($v1);
        $this->assertEquals(array($v1),$param->getValues());
        $param->addValues($v2);
        $this->assertEquals(array($v2),$param->getValues());
    }

}
?>
