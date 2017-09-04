<?php
/// Source code file for the LongCiteMaster:: class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.\n
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.


/// Master control class for the LongCite MediaWiki extension.
/// Defines utility routines and data structures.
class LongCiteMaster {

    protected $messenger = null;  ///< Set to instance of LongCiteMessenger:: class.

    /// Class instance constructor.
    function __construct() {
        #$this->messenger = new LongCiteMessenger();
        $this->messenger = null; // defer instantiation
        #
        $this->messenger = new LongCiteMessenger();
        $this->messenger->registerMessage(LongCiteMessenger::DebugType,
            "Instantiated new LongCiteMaster at " . time());
        #$this->messenger->dumpToFile(false);
        $this->messenger->dumpToFile(true);
        $this->messenger->clearMessages();
        // Register the LongCite extension.
        $wgLongCiteMasterInstance->register();
        // Set up the extension tags.
        $wgLongCiteMasterInstance->setup();
    }

    public function register() {
        global $wgExtensionFunctions;
        global $wgExtensionCredits;
        global $wgHooks;
        $m = $this->getMessenger();
        $m->registerMessage(LongCiteMessenger::DebugType,"In Master::register");
        $m->dumpToFile();
        $m->clearMessages();
        // register the extension
        #$wgExtensionFunctions[] = array(&$this,"setup");
        #$wgExtensionCredits['parserhook'][] = array( 
        #    'name' => 'LongCite',
        #    'author' => 'Eric Alan Christiansen',
        #    'description' => 'Adds tags for reference citation management.',
        #    'url' => 'http://www.mediawiki.org/wiki/Extension:LongCite'
        #);
        // set top level hooks
        $wgHooks['ArticleDeleteComplete'][] = array(&$this,"onArticleDeleteComplete");
        $wgHooks['ArticleSave'][]           = array(&$this,"onArticleSave");
    }

    public function setup() {
        global $wgParser;
        $m = $this->getMessenger();
        $m->registerMessage(LongCiteMessenger::DebugType,"In Master::setup");
        $m->dumpToFile();
        $m->clearMessages();
        // set hooks for parser functions
        $wgParser->setHook('longcitedef',array($this,"tagLongCiteDef"));
        $wgParser->setHook('longciteref',array($this,"tagLongCiteRef"));
        $wgParser->setHook('longciteren',array($this,"tagLongCiteRen"));
        $wgParser->setHook('longcitehlp',array($this,"tagLongCiteHlp"));
    }

    public function onArticleDeleteComplete($article) {
        $pageId = $article->getTitle()->getPrefixedDBkey();
        $m = $this->getMessenger();
        $m->registerMessage(LongCiteMessenger::DebugType,
            "In Master::onArticleDeleteComplete for '$pageID'");
        $m->dumpToFile();
        $m->clearMessages();
        # TBD
        return true;
    }

    public function onArticleSave($article) {
        $pageTitle = $article->getTitle();
        $pageId = $article->getTitle()->getPrefixedDBkey();
        $art_r=print_r($article);
        $pgt_r=print_r($pageTitle);
        $pgi_r=print_r($pageId);
        $m = $this->getMessenger();
        $d = LongCiteMessenger::DebugType;
        $m->registerMessage($d,"In Master::onArticleSave...");
        $m->registerMessage($d,"...article=$art_r.");
        $m->registerMessage($d,"...pgtitle=$pgt_r.");
        $m->registerMessage($d,"...pgdbid =$pgi_r.");
        $m->dumpToFile();
        $m->clearMessages();
        # TBD
        return true;
    }

    public function tagLongCiteDef($input, $args, $parser, $frame) {
        $m = $this->getMessenger();
        $m->registerMessage(LongCiteMessenger::DebugType,"In Master::tagLongCiteDef");
        $m->dumpToFile();
        $m->clearMessages();
        $tagObj = new LongCiteDefTag($this);
        $result = $tagObj->render($input,$args,$parser,$frame);
        return $result;
    }
    public function tagLongCiteRef($input, $args, $parser, $frame) {
        $m = $this->getMessenger();
        $m->registerMessage(LongCiteMessenger::DebugType,"In Master::tagLongCiteRef");
        $m->dumpToFile();
        $m->clearMessages();
        $tagObj = new LongCiteRefTag($this);
        $result = $tagObj->render($input,$args,$parser,$frame);
        return $result;
    }
    public function tagLongCiteRen($input, $args, $parser, $frame) {
        $m = $this->getMessenger();
        $m->registerMessage(LongCiteMessenger::DebugType,"In Master::tagLongCiteRen");
        $m->dumpToFile();
        $m->clearMessages();
        $tagObj = new LongCiteRenTag($this);
        $result = $tagObj->render($input,$args,$parser,$frame);
        return $result;
    }
    public function tagLongCiteHlp($input, $args, $parser, $frame) {
        $m = $this->getMessenger();
        $m->registerMessage(LongCiteMessenger::DebugType,"In Master::tagLongCiteHlp");
        $m->dumpToFile();
        $m->clearMessages();
        $tagObj = new LongCiteHlpTag($this);
        $result = $tagObj->render($input,$args,$parser,$frame);
        return $result;
    }

    /// Get the LongCiteMessenger:: instance to use.
    /// @returns A LongCiteMessenger:: instance.
    public function getMessenger() {
        if(is_null($this->messenger)) {
            $this->messenger = new LongCiteMessenger();
        }
        return $this->messenger;
    }

    public static function initialize() {
        // Extension globals.
        global $wgLongCiteMasterInstance;
        // Instantiate the global LongCiteMaster if needed.
        if(!isset($wgLongCiteMasterInstance)) {
            $wgLongCiteMasterInstance = new LongCiteMaster();
        }
    }
}
?>
