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

}
?>
