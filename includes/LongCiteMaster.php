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
        $this->register();
        // Set up the extension tags.
        $this->setupParser();
    }

    public function register() {
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
        // set setup parser hook
        $wgHooks['ParserFirstCallInit'][] = array(
            &$this,"onSetupParser"
        );
        // set top level runtime hooks
        $wgHooks['ArticleDeleteComplete'][] = array(
            &$this,"onArticleDeleteComplete"
        );
        $wgHooks['ArticleSave'][] = array(
            &$this,"onArticleSave"
        );
        $wgHooks['PageContentSaveComplete'][] = array(
            &$this,"onPageContentSaveComplete"
        );
        // set database schema updates hook
        $wgHooks['LoadExtensionSchemaUpdates'][] = array(
            &$this,"setupSchema"
        );
    }

    public function setupSchema($updater) {
        global $wgDBprefix;
        $sqlFile = __DIR__.'/../GeneratedTables.sql';
        // Create the create table sql command on the fly so that
        // we can prepend the correct database prefix.
        if(isset($wgDBprefix)) {
            $dbPrefix = $wgDBprefix;
        } else {
            $dbPrefix = "";
        }
        $sqlHdl = fopen($sqlFile,"w");
        if($sqlHdl===false) {
            trigger_error("Could not create $sqlFile.",E_USER_WARNING);
            return "Error in setupSchema";
        }
        fwrite($sqlHdl,"CREATE TABLE IF NOT EXISTS " . $dbPrefix . "longcite_citation (\n");
        fwrite($sqlHdl,"    longcite_guid char(32),\n");
        fwrite($sqlHdl,"    longcite_id   varchar(255),\n");
        fwrite($sqlHdl,"    longcite_page varchar(255),\n");
        fwrite($sqlHdl,"    longcite_json varchar(20000),\n");
        fwrite($sqlHdl,"    UNIQUE KEY " . $dbPrefix . "longcite_guid_pk  (longcite_guid),\n");
        fwrite($sqlHdl,"    KEY        " . $dbPrefix . "longcite_id_idx   (longcite_id),\n");
        fwrite($sqlHdl,"    KEY        " . $dbPrefix . "longcite_page_idx (longcite_page)\n");    
        fwrite($sqlHdl,");\n");
        fclose($sqlHdl);
        $updater->addExtensionTable("longcite_citation",$sqlFile);
    }
    public function setupParser() {
        global $wgParser;
        $m = $this->getMessenger();
        $m->registerMessage(LongCiteMessenger::DebugType,"In Master::setupParser");
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
            "In Master::onArticleDeleteComplete for '$pageId'");
        $m->dumpToFile();
        $m->clearMessages();
        # TBD
        return true;
    }

    public function onArticleSave($article) {
        $pageTitle = $article->getTitle();
        $pageId = $article->getTitle()->getPrefixedDBkey();
        #$art_r=print_r($article,true);
        #$pgt_r=print_r($pageTitle,true);
        #$pgi_r=print_r($pageId,true);
        $m = $this->getMessenger();
        $d = LongCiteMessenger::DebugType;
        $m->registerMessage($d,"In Master::onArticleSave for '$pageId'");
        #$m->registerMessage($d,"...article=$art_r.");
        #$m->registerMessage($d,"...pgtitle=$pgt_r.");
        #$m->registerMessage($d,"...pgdbid =$pgi_r.");
        $m->dumpToFile();
        $m->clearMessages();
        # TBD
        return true;
    }

    public function onPageContentSaveComplete($article) {
        $pageTitle = $article->getTitle();
        $pageId = $article->getTitle()->getPrefixedDBkey();
        $m = $this->getMessenger();
        $d = LongCiteMessenger::DebugType;
        $m->registerMessage($d,"In Master::onPageContentSaveComplete for '$pageId'");
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
