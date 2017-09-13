<?php
/// Source code file for LongCiteTest unit testing class.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

#use PHPUnit\Framework\Testcase;  # original
use PHPUnit\Framework\TestCase;  # newer phpunit
#use PHPUnit_Framework_TestCase as TestCase


#require __DIR__ . "/../vendor/autoload.php";
#require __DIR__."/../vendor/phpunit/phpunit/src/Framework/TestCase.php";

/// Some LongCite phpunit tests.
class LongCiteTest extends TestCase {

    /// Test something simple functions, mostly to check phpunit itself.
    public function testMath() {
        // check simple math.
        $this->assertEquals(4,2+2);
    }

    // check that json files are each valid json.
    public function testJsonFiles() {
        $jsonFiles = array();
        $jsonFiles[] = __DIR__."/../extension.json";
        $jsonFiles[] = __DIR__."/../i18n/en.json";
        $jsonFiles[] = __DIR__."/../i18n/de.json";
        $jsonFiles[] = __DIR__."/../i18n/qqq.json";
        foreach($jsonFiles as $jsonFile) {
            $this->assertFileExists($jsonFile);
            $jsonStr = file_get_contents($jsonFile);
            $this->assertNotFalse($jsonStr);
            $jsonArr = json_decode($jsonStr,true);
            $this->assertNotNull($jsonArr);
        }
    }

}
?>
