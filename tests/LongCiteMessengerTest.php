<?php
/// Source code file for LongCiteMessengerTest unit testing class.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

require_once __DIR__ . "/LongCiteWikiStub.php";

use PHPUnit\Framework\TestCase;

/// Some LongCite phpunit tests.
class LongCiteMessengerTest extends TestCase {

    /// Test functions.
    public function testFunctions() {
        // initialize
        LongCiteMaster::clearActiveMaster();
        LongCiteWikiStub::initialize();
        $master = LongCiteMaster::getActiveMaster();
        // get a messenger
        $mess = new LongCiteMessenger("en");
        $this->assertInstanceOf(LongCiteMessenger::class,$mess);
        $mess->clearMessages();
        $this->assertTrue($mess->getEnableError());
        $this->assertTrue($mess->getEnableWarning());
        $this->assertTrue($mess->getEnableNote());
        $debugSave = $mess->getEnableDebug();
        $traceSave = $mess->getEnableTrace();
        // do some messaging
        $mess->registerMessageError("My error.");
        $mess->registerMessageWarning("My warning.");
        $mess->registerMessageNote("My note.");
        $this->assertEquals(3,$mess->getMessageCount());
        $mess->setEnableDebug(false);
        $mess->registerMessageDebug("My debug 1.");  // should be suppressed
        $this->assertEquals(3,$mess->getMessageCount());
        $mess->setEnableDebug(true);
        $mess->registerMessageDebug("My debug 2.");  // should not be suppressed
        $this->assertEquals(4,$mess->getMessageCount());
        $mess->setEnableTrace(true);
        $this->sample($mess);
        $this->assertEquals(5,$mess->getMessageCount());
        // restore states
        $mess->setEnableDebug($debugSave);
        $mess->setEnableTrace($traceSave);
        // get text and html
        $text = $mess->renderMessagesText(false);
        $this->assertGreaterThan(0,strlen($text));
        $html = $mess->renderMessagesHtml(false);
        $this->assertGreaterThan(0,strlen($html));
        #$mess->renderMessagesToTty(false,false);
        #$mess->renderMessagesToTty(false,true);
        // clear messages
        $mess->clearMessages();
        $this->assertEquals(0,$mess->getMessageCount());
    }

    public function sample($mess) {
        $mess->registerMessageTrace("Just a sample.");
    }
}
?>
