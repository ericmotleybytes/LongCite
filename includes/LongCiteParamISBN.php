<?php
/// Source code file for the LongCiteParamISBN:: class.
/// MIT License.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

/// Parent Class for other LongCite tag classes.
class LongCiteParamISBN extends LongCiteParam {

    public function __construct($paramNameKey, $isMulti, $tag) {
        parent::__construct($paramNameKey, $isMulti, $tag);
        $this->setInputDelimMsgKey("longcite-delimi-semi");
        $this->setOutputDelimMsgKey(self::ParamModeLong ,"longcite-delimo-semi");
        $this->setOutputDelimMsgKey(self::ParamModeShort,"longcite-delimo-semi");
        $this->setRenderPrefixMsgKey("longcite-pre-isbn");
    }

    public function renderParamValues() {
        $tag = $this->getTag();
        $frLang = $this->getInputLangCode();
        $toLang = $this->getOutputLangCode();
        $delim  = htmlspecialchars($this->getOutputDelim());
        $intDelim = $this->wikiMessageIn("longcite-delimi-bar")->plain();
        $intDelimPat = LongCiteUtil::eregQuote($intDelim);
        $parenOpen  = $this->wikiMessageOut("longcite-pun-parenopen")->plain();
        $parenClose = $this->wikiMessageOut("longcite-pun-parenclose")->plain();
        $retrieved  = $this->wikiMessageOut("longcite-pre-retrieved")->plain();
        $annValues = $this->getAnnotatedValues();
        $htmlValues = array();
        $idx = -1;
        foreach($annValues as $annValue) {
            $idx++;
            if(!$annValue[LongCiteParam::AnnValIsValid]) {
                continue;
            }
            $basicVal = $annValue[LongCiteParam::AnnValBasic];
            // parts=isbn|description
            $parts = mb_split($intDelimPat,$basicVal,2);
            $isbn = LongCiteUtil::eregTrim($parts[0]);
            $isbn = mb_ereg_replace('[\-\ ]',"",$isbn);
            $isbnMatch = mb_ereg('^[0-9xX]+$',$isbn);
            if($isbnMatch===false) {
                $isbnOk = false;
            } else {
                if(strlen($isbn)==10) {
                    $isbnOk = true;
                } elseif(strlen($isbn)==13) {
                    $isbnOk = true;
                } else {
                    $isbnOk = false;
                }
            }
            $cleanIsbn = htmlspecialchars($isbn);
            if(array_key_exists(1,$parts)) {
                $desc = LongCiteUtil::eregTrim($parts[1]);
            } else {
                $desc = "";
            }
            $cleanDesc = htmlspecialchars($desc);
            $htmlVal = "";
            if($isbnOk===false) {
                $badIsbn  = '<span class="mw-longcite-pv-unrecogitem">';
                $badIsbn .= $cleanIsbn . '</span>';
                $htmlVal = $badIsbn;
            } else {
                $paropt = $tag->getParser()->getOptions();
                $lnktgt = $paropt->getExternalLinkTarget();
                if("$lnktgt"!="") {
                    $lnktgt = "target=\"$lnktgt\"";
                }
                $rel = "rel=\"nofollow noreferrer noopener\"";
                $isbnHref  = 'http://www.worldcat.org/search';
                $isbnHref .= '?qt=worldcat_org_all&q=isbn%3A' . $cleanIsbn;
                $isbnLink  = "<a href=\"$isbnHref\" $lnktgt $rel>";
                $isbnLink .= $cleanIsbn . '</a>';
                $htmlVal = $isbnLink;
            }
            if($cleanDesc!="") {
                $fullDesc = $parenOpen . $cleanDesc . $parenClose;
                $htmlVal .= " " . $fullDesc;
            }
            $htmlValues[] = $htmlVal;
        }
        $stuff = implode($delim,$htmlValues);
        $tag->renderedOutputAdd($stuff,true);
    }

}
?>
