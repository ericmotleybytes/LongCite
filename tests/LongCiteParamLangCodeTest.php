<?php
/// Source code file for LongCiteParamLangCodeTest unit testing class.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

require_once __DIR__ . "/LongCiteWikiStub.php";

use PHPUnit\Framework\TestCase;

/// Some LongCite phpunit tests.
class LongCiteParamLangCodeTest extends TestCase {

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
        $pnameKey = "longcite-pn-alwayslang";
        $param = new LongCiteParamLangCode($pnameKey,false,$tag);
        $this->assertInstanceOf(LongCiteParamLangCode::class,$param);
        $param = LongCiteParam::newParam($pnameKey,$tag);
        $this->assertInstanceOf(LongCiteParamLangCode::class,$param);
        $param = $tag->newParam($pnameKey);
        $this->assertInstanceOf(LongCiteParamLangCode::class,$param);
        $tag2 = $param->getTag();
        $this->assertInstanceOf(LongCiteTag::class,$tag2);
        # info
        $this->assertFalse($param->isMulti());
        $nameMsgKey = $param->getNameKey();
        $this->assertEquals($pnameKey,$nameMsgKey);
        $name = $param->getName();
        $this->assertEquals("lang",$name);
        $type = $param->getType();
        $this->assertEquals("LangCode",$type);
        # values
        $v1 = "en";
        $v2 = "de";
        $v3 = "EN";
        $param->addValues($v1);
        $this->assertEquals(array($v1),$param->getValues());
        $param->addValues($v2);
        $this->assertEquals(array($v2),$param->getValues());
        $param->addValues($v3);
        $exp = array(strtolower($v3));
        $this->assertEquals($exp,$param->getValues());
    }

}
?>
