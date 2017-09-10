<?php
/// Source code file for LongCiteUtilTest unit testing class.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

require_once __DIR__ . "/../includes/LongCiteUtil.php";

use PHPUnit\Framework\Testcase;

/// Some LongCite phpunit tests.
class LongCiteUtilTest extends Testcase {

    /// Test guid functions.
    public function testGuid() {
        if (function_exists('openssl_random_pseudo_bytes')) {
            $guid = LongCiteUtil::generateOpensslGuid();
            $this->assertNotFalse($guid);
            $this->assertRegExp('/^[0-9A-F]{32}$/',$guid);            
        }
        $guid = LongCiteUtil::generateGenericGuid();
        $this->assertNotFalse($guid);
        $this->assertRegExp('/^[0-9A-F]{32}$/',$guid);            
    }

    /// Test a_or_an functions.
    public function testA_or_An() {
        $this->assertEquals("An",LongCiteUtil::a_or_an("elephant"));
        $this->assertEquals("A",LongCiteUtil::a_or_an("monkey"));
        $this->assertEquals("An",LongCiteUtil::a_or_an("elephant",true));
        $this->assertEquals("A",LongCiteUtil::a_or_an("monkey",true));
        $this->assertEquals("an",LongCiteUtil::a_or_an("elephant",false));
        $this->assertEquals("a",LongCiteUtil::a_or_an("monkey",false));
    }

    /// Test hex encode/decode.
    public function testHexEncodeDecode() {
        $this->assistHexEncode("b","%62");
        $this->assistHexDecode("%62","b");
        $this->assistHexDecode("x%62y%6262","xbyb62");
    }

    public function assistHexEncode($raw,$exp) {
        $act = LongCiteUtil::percentHexEncode($raw);
        $this->assertEquals($exp,$act);
    }

    public function assistHexDecode($raw,$exp) {
        $act = LongCiteUtil::percentHexDecode($raw);
        $this->assertEquals($exp,$act);
    }

    /// Test backslashes2urlencode.
    public function testBackslashes2hex() {
        $qq = '"';
        $this->assistBack2hex("","");
        $this->assistBack2hex("abc","abc");
        $this->assistBack2hex("a\\b\\$qq"."c","a%62%22c");
    }

    public function assistBack2hex($raw,$exp) {
        $act = LongCiteUtil::backslashes2percenthex($raw);
        $this->assertEquals($exp,$act);
    }

    /// Test parse.
    public function testParse() {
        $qq = '"';
        $q  = "'";
        $b  = "\\";
        $this->assistParse("abc",array("abc"));
        $this->assistParse("  abc  xyz  ",array("abc  xyz"));
        $this->assistParse("  $qq abc $qq  ",array(' abc '));
        $this->assistParse("  $b \\ abc  ",array('  abc'));
        $this->assistParse("abc|xyz",array("abc","xyz"),"|");
        $this->assistParse(" abc | xyz ",array("abc","xyz"),"|");
        $this->assistParse(" abc $b| xyz ",array("abc | xyz"),"|");
        $this->assistParse(" abc | xyz\\  ",array("abc","xyz "),"|");
    }

    public function assistParse($raw,$exp,$delim=null) {
        $act = LongCiteUtil::parse($raw,$delim);
        $this->assertEquals(count($exp),count($act));
        $this->assertEquals($exp,$act);
    }

}
?>
