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

}
?>
