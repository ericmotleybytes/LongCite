<?php
/// Source code file for ExtensionTest unit testing class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.
use PHPUnit\Framework\Testcase;

/// Tests simple math.
class ExtensionTest extends Testcase {

    /// Test various checker functions.
    public function testMath() {
        // check the checker
        $this->assertEquals(4,2+2);
    }

    // check that extension.json is valid json.
    public function testExtensionJson() {
        $jsonFile = __DIR__."/../extension.json";
        $this->assertFileExists($jsonFile);
        $jsonStr = file_get_contents($jsonFile);
        $this->assertNotFalse($jsonStr);
        $jsonArr = json_decode($jsonStr,true);
        $this->assertNotNull($jsonArr);
    }
}
?>
