<?php
/// Source code file for the LongCiteParamURL:: class.
/// MIT License.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

/// Parent Class for other LongCite tag classes.
class LongCiteParamURL extends LongCiteParam {

    public function __construct($paramNameKey, $isMulti, $tag) {
        parent::__construct($paramNameKey, $isMulti, $tag);
        $this->setInputDelimMsgKey("longcite-delimi-semi");
        $this->setOutputDelimMsgKey(self::ParamModeLong,"longcite-delimo-semi2");
        $this->setOutputDelimMsgKey(self::ParamModeShort,"longcite-delimo-semi2");
        $this->setRenderPrefixMsgKey("longcite-pre-url");
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
            $parts = mb_split($intDelimPat,$basicVal,4);
            $partCnt = count($parts);
            $addr = LongCiteUtil::eregTrim($parts[0]);
            $cleanAddr = filter_var($addr,FILTER_VALIDATE_URL,FILTER_FLAG_SCHEME_REQUIRED);
            if(array_key_exists(1,$parts)) {
                $disp = LongCiteUtil::eregTrim($parts[1]);
            } else {
                $disp = $addr;
            }
            $cleanDisp = htmlspecialchars($disp);
            if(array_key_exists(2,$parts)) {
                $desc = LongCiteUtil::eregTrim($parts[2]);
            } else {
                $desc = "";
            }
            $cleanDesc = htmlspecialchars($desc);
            if(array_key_exists(3,$parts)) {
                $retr = LongCiteUtil::eregTrim($parts[3]);
                if($retr!="") {
                    $retrDate = new LongCiteUtilDate($retr,$frLang);
                    if($retrDate->getParsedOk()) {
                        $cleanRetr = $retrieved . $retrDate->getDateStr() . ".";
                        $cleanRetr = htmlspecialchars($cleanRetr);
                    } else {
                        $cleanRetr  = htmlspecialchars($retrieved);
                        $cleanRetr .= '<span class="mw-longcite-pv-unrecogitem">';
                        $cleanRetr .= htmlspecialchars($retr) . '</span>.';
                    }
                } else {
                    $cleanRetr = "";
                }
            } else {
                $retr = "";
                $cleanRetr = "";
            }
            $htmlVal = "";
            if($cleanAddr===false) {
                $badAddr  = '<span class="mw-longcite-pv-unrecogitem">';
                $badAddr .= htmlspecialchars($addr) . '</span>';
                if($disp==$addr) {
                    $htmlVal = $badAddr;
                } else {
                    $htmlVal = "$badAddr $cleanDisp";
                }
            } else {
                $goodAddr  = '<a href="' . $cleanAddr . '">';
                $goodAddr .= $cleanDisp . '</a>';
                #$goodAddr = '[' . $cleanAddr . ' ' . $cleanDisp . ']';
                $htmlVal = $goodAddr;
            }
            if(mb_substr($cleanDesc,-1,1)=='.') {
                $fullDesc = $cleanDesc . $cleanRetr;
            } else {
                if($cleanRetr!="") {
                    $fullDesc = $cleanDesc . "." . $cleanRetr;
                } else {
                    $fullDesc = $cleanDesc;
                }
            }
            $fullDesc = LongCiteUtil::eregTrim($fullDesc);
            if($fullDesc!="") {
                $fullDesc = $parenOpen . $fullDesc . $parenClose;
                $htmlVal .= " " . $fullDesc;
            }
            $htmlValues[] = $htmlVal;
        }
        $stuff = implode($delim,$htmlValues);
        $tag->renderedOutputAdd($stuff,true);
    }

}
?>
