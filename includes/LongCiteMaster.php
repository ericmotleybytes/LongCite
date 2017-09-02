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
    }

    public function register() {
        global $wgExtensionFunctions;
        global $wgExtensionCredits;
        global $wgHooks;
        // register the extension
        $wgExtensionFunctions[] = array(&$this,"setup");
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
        // Called after MediaWiki setup has finished.
        global $wgParser;
        // set hooks for parser functions
        $wgParser->setHook('longcitedef',array($this,"tagLongCiteDef"));
        $wgParser->setHook('longciteref',array($this,"tagLongCiteRef"));
        $wgParser->setHook('longciteren',array($this,"tagLongCiteRen"));
        $wgParser->setHook('longcitehlp',array($this,"tagLongCiteHlp"));
    }

    public function onArticleDeleteComplete($article) {
        $pageId = $article->getTitle()->getPrefixedDBkey();
        # TBD
        return true;
    }

    public function onArticleSave($article) {
        $pageId = $article->getTitle()->getPrefixedDBkey();
        # TBD
        return true;
    }

    public function tagLongCiteDef($input, $args, $parser, $frame) {
        return "<p>TBD</p>\n";
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
        // Instantiate the global LongCiteMaster.
        $wgLongCiteMasterInstance = new LongSiteMaster();
        // Register the LongCite extension.
        $wgLongCiteMasterInstance->register();
    }
}
?>
