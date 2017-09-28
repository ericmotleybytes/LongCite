<?php
/// Source code file for the LongCiteUtilPersonName:: class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.\n
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.

/// Class for the parsing person names.
/// Support raw names such as:
///  [Dr.] Robert "Bob" von_Jones {Jr.} (M.D.;J.D.) 
///  Robert Jones                ; Jones, Robert
///  Robert "Bob" Jones          ; Jones, Robert "Bob"
///  Mr. Robert "Bob" Jones, Jr. ; Jones, Robert "Bob",Jr. 
///  Dr. Robert "Bob" Jones, III ;
///  [Prof.,Dr.,Mr.] Robert "Bob" von_Jones, III {CPA, M.S.C.S.} (President of France)
///  Otto von_Bismark
class LongCiteUtilPersonName {

    const NamePartNickname      = "nickname";
    const NamePartDisambiguator = "disambiguator";
    const NamePartName          = "name";
    const NamePartSurname       = "surname";
    const NamePartTitle         = "title";
    const NamePartQualifier     = "qualifier";
    const NamePartCredential    = "credential";
    const NamePartPosition      = "position";

    protected $langCode    = "";       /// e.g., "en" or "de".
    protected $langCodeParsed = "";    /// langCode when parsed.
    protected $rawName     = "";       /// e.g., 'Robert "Bob" K. von_Jones, Jr.'.
    protected $givenName   = "";       /// e.g., "Robert".
    protected $middleNames = array();  /// e.g., ["K."].
    protected $surname     = "";       /// e.g., "Jones".
    protected $disambigs   = array();  /// e.g., "of Elea","of Citium".
    protected $nicknames   = array();  /// e.g., ["Bob"].
    protected $salutation  = array();  /// e.g., ["Mr."].
    protected $titles      = array();  /// e.g., ["Dr.", ].
    protected $qualifiers  = array();  /// e.g., ["Jr."].
    protected $credentials = array();  /// e.g., ["M.D.","CPA"].
    protected $positions   = array();  /// e.g., ["King of France"].
    protected $gender      = "";       /// e.g., "male", "female".
    protected $inSemi      = ";";      /// parsing delimiter (lang sensitive).
    protected $inBar       = "|";      /// parsing delimiter (lang sensitive).
    protected $inDash      = "-";      /// parsing delimiter (lang sensitive).
    protected $inUnder     = '_';      /// parsing delimiter (lang sensitive).
    protected $inQuote1    = "'";      /// parsing delimiter (lang sensitive).
    protected $inQuote2    = '"';      /// parsing delimiter (lang sensitive).
    protected $inBrackL    = '[';      /// parsing delimiter (lang sensitive).
    protected $inBrackR    = ']';      /// parsing delimiter (lang sensitive).
    protected $inCurlyL    = '{';      /// parsing delimiter (lang sensitive).
    protected $inCurlyR    = '}';      /// parsing delimiter (lang sensitive).
    protected $rawTitles   = array();  /// scratch space for titles.
    protected $rawPositions  = array(); /// temporary.
    protected $rawCredentials = array(); /// temporary.
    protected $rawNicknames = array();   /// temporary.
    protected $rawDisambigs = array();   /// temporary.
    protected $annNameParts = array();   /// Annotated results.

    /// Class constructor.
    public function __construct($rawName,$langCode="en") {
        $this->setLangCode($langCode);
        $this->parseAll($rawName);
    }

    public function i18nFull($msgKey) {
        $result = LongCiteUtil::i18nRender($this->langCode,$msgKey);
        return $result;
    }

    public function setLangCode($langCode) {
        $langCode = mb_strtolower($langCode);
        if($langCode==$this->langCode) { return; }  // already set
        $this->langCode = $langCode;
        $this->inSemi   = $this->i18nFull('longcite-delimi-semi');
        $this->inBar    = $this->i18nFull('longcite-delimi-bar');
        $this->inDash   = $this->i18nFull('longcite-delimi-dash');
        $this->inUnder  = $this->i18nFull('longcite-delimi-under');
        $this->inQuote1 = $this->i18nFull('longcite-delimi-quote1');
        $this->inQuote2 = $this->i18nFull('longcite-delimi-quote2');
        $this->inBrackL = $this->i18nFull('longcite-delimi-brackl');
        $this->inBrackR = $this->i18nFull('longcite-delimi-brackr');
        $this->inCurlyL = $this->i18nFull('longcite-delimi-curlyl');
        $this->inCurlyR = $this->i18nFull('longcite-delimi-curlyr');
    }

    protected function parseAll($rawName) {
        $this->langCodeParsed = $this->langCode;
        $adjName = LongCiteUtil::eregTrim($rawName);
        $adjName = mb_ereg_replace('[\ \t]+'," ",$adjName); // collapse spaces
        $this->rawName  = $adjName;
        $adjName = $this->parseTitles($adjName);
        $adjName = $this->parsePositions($adjName);
        $adjName = $this->parseCredentials($adjName);
        $workParts = $this->parseName($adjName);
        // setup results
        $annParts = array();
        foreach($this->rawTitles as $rawTitle) {
            $annParts[] = array($rawTitle,self::NamePartTitle);
        }
        foreach($workParts as $workPart) {
            $annParts[] = $workPart;  // workPart is already an array.
        }
        foreach($this->rawCredentials as $credential) {
            $annParts[] = array($credential,self::NamePartCredential);
        }
        foreach($this->rawPositions as $position) {
            $annParts[] = array($position,self::NamePartPosition);
        }
        $this->annNameParts = $annParts;
        return true;
    }

    protected function parseTitles($adjName) {
        $this->titles = array();
        $this->rawTitles = array();
        $bs = "\\";
        $bl = $this->inBrackL;
        $br = $this->inBrackR;
        $ebl = $bs . $bl;
        $ebr = $bs . $br;
        $pat = $ebl . '(' . '[^' .$ebr . ']*' . ')' . $ebr;
        $callable = array($this,"parseTitlesMatch");
        $workName = mb_ereg_replace_callback($pat,$callable,$adjName);
        if($workName!==false) {
            $adjName = LongCiteUtil::eregTrim($workName);
            $adjName = mb_ereg_replace('\ +'," ",$adjName); // collapse spaces
        }
        return $adjName;
    }

    protected function parseTitlesMatch($matches) {
        $matchString = $matches[1];
        $delimPat = "\\" . $this->inSemi;
        $titles = mb_split($delimPat,$matchString);
        foreach($titles as $title) {
            $this->rawTitles[] = LongCiteUtil::eregTrim($title);
        }
        return "";
    }

    protected function parsePositions($adjName) {
        $this->positions = array();
        $this->rawPositions = array();
        $pat = '\(\(' . '(' . '[^\)]*' . ')' . '\)\)';
        $callable = array($this,"parsePositionsMatch");
        $workName = mb_ereg_replace_callback($pat,$callable,$adjName);
        if($workName!==false) {
            $adjName = LongCiteUtil::eregTrim($workName);
            $adjName = mb_ereg_replace('\ +'," ",$adjName); // collapse spaces
        }
        return $adjName;
    }

    protected function parsePositionsMatch($matches) {
        $matchString = $matches[1];
        $delimPat = "\\" . $this->inBar;
        $positions = mb_split($delimPat,$matchString);
        foreach($positions as $position) {
            $this->rawPositions[] = LongCiteUtil::eregTrim($position);
        }
        return "";
    }

    protected function parseCredentials($adjName) {
        $this->credentials = array();
        $this->rawCredentials = array();
        $pat = '\(' . '(' . '[^\)]*' . ')' . '\)';
        $callable = array($this,"parseCredentialsMatch");
        $workName = mb_ereg_replace_callback($pat,$callable,$adjName);
        if($workName!==false) {
            $adjName = LongCiteUtil::eregTrim($workName);
            $adjName = mb_ereg_replace('\ +'," ",$adjName); // collapse spaces
        }
        return $adjName;
    }

    protected function parseCredentialsMatch($matches) {
        $matchString = $matches[1];
        $delimPat = "\\" . $this->inSemi;
        $credentials = mb_split($delimPat,$matchString);
        foreach($credentials as $credential) {
            $this->rawCredentials[] = LongCiteUtil::eregTrim($credential);
        }
        return "";
    }

    protected function parseName($adjName) {
        // adjust quotes nickname to use underscores for spaces
        $bs = "\\";
        $qq = $this->inQuote2;
        $eqq = $bs . $qq;
        $pat = $eqq . '([^' . $eqq . ']*)' . $eqq;
        $callable = array($this,"parseNameQqMatch");
        $workName = mb_ereg_replace_callback($pat,$callable,$adjName);
        if($workName!==false) {
            $adjName = $workName;
        }
        // adjust disambiguation to use underscores for spaces
        $d = $this->inDash;
        $ed = $bs . $d;
        $edd = $ed . $ed;
        $pat = $edd . '(.*)' . $edd;
        $callable = array($this,"parseNameDdMatch");
        $workName = mb_ereg_replace_callback($pat,$callable,$adjName);
        if($workName!==false) {
            $adjName = $workName;
        }
        // adjust qualifiers to use underscores for spaces
        $pat = "\\" . $this->inCurlyL . '(.*)' . "\\" . $this->inCurlyR;
        $callable = array($this,"parseNameCurlyMatch");
        $workName = mb_ereg_replace_callback($pat,$callable,$adjName);
        if($workName!==false) {
            $adjName = $workName;
        }
        // breakup name using space delimited
        $nameParts = mb_split('\ ',$adjName);
        $u = $this->inUnder;
        $eu = "\\" . $u;
        $esp = "\\" . " ";
        $curlyL  = $this->inCurlyL;
        $curlyR  = $this->inCurlyR;
        $eCurlyL = "\\" . $this->inCurlyL;
        $eCurlyR = "\\" . $this->inCurlyR;
        $patUnderFix = '[' . $eu . $esp . ']+';
        $patNickname = '^' . $eqq . '.*' . $eqq;
        $patDisambig = '^' . $edd . '.*' . $edd;
        $patQual = '^' . $eCurlyL . '.*' . $eCurlyR;
        $nameAnnotatedParts = array();
        $bestSurnameIdx = -1;
        $lastNameIdx = -1;
        $idx = -1;
        foreach($nameParts as $namePart) {
            $idx++;
            $isNickname = mb_ereg($patNickname,$namePart);
            if($isNickname!==false) {
                $namePart = mb_ereg_replace($eqq,"",$namePart);  // remove quotes
                $namePart = mb_ereg_replace($patUnderFix," ",$namePart);
                $namePart = LongCiteUtil::eregTrim($namePart);
                if(mb_strlen($namePart)>0) {
                    $nameAnnotatedParts[] = array($namePart,self::NamePartNickname);
                }
                continue;
            }
            $isDisambiguator = mb_ereg($patDisambig,$namePart);
            if($isDisambiguator!==false) {
                $namePart = mb_ereg_replace($edd,"",$namePart);  // remove --'s
                $namePart = mb_ereg_replace($patUnderFix," ",$namePart);
                $namePart = LongCiteUtil::eregTrim($namePart);
                if(mb_strlen($namePart)>0) {
                    $nameAnnotatedParts[] = array($namePart,self::NamePartDisambiguator);
                }
                continue;
            }
            $isQualifier = mb_ereg($patQual,$namePart);
            if($isQualifier!==false) {
                $namePart = mb_ereg_replace($eCurlyL,"",$namePart);  // remove {'s
                $namePart = mb_ereg_replace($eCurlyR,"",$namePart);  // remove }'s
                $namePart = LongCiteUtil::eregTrim($namePart);
                $quals = mb_split("\\".$this->inSemi,$namePart);
                foreach($quals as $qual) {
                    $qual = mb_ereg_replace("\\" . $this->inUnder," ",$namePart);
                    $qual = LongCiteUtil::eregTrim($qual);
                    $nameAnnotatedParts[] = array($qual,self::NamePartQualifier);
                }
                continue;
            }
            $isSurname = mb_ereg($eu,$namePart);
            if($isSurname!==false) {
                $namePart = mb_ereg_replace($patUnderFix," ",$namePart);
                $namePart = LongCiteUtil::eregTrim($namePart);
                $bestSurnameIdx = $idx;
            }
            $lastNameIdx = $idx;
            $nameAnnotatedParts[] = array($namePart,self::NamePartName);
        }
        if($bestSurnameIdx>=0) {
            // If a surname was specifically flagged, update annotated parts
            $nameAnnotatedParts[$bestSurnameIdx][1] = self::NamePartSurname;
        } elseif($lastNameIdx>=0) {
            // Surname was not specifically flagged, use last name part listed.
            $nameAnnotatedParts[$lastNameIdx][1] = self::NamePartSurname;
        }
        return $nameAnnotatedParts;
    }

    protected function parseNameQqMatch($matches) {
        $result = LongCiteUtil::eregTrim($matches[1]);
        $result = mb_ereg_replace('\ ',$this->inUnder,$result);
        return $this->inQuote2 . $result . $this->inQuote2;
    }

    protected function parseNameDdMatch($matches) {
        $result = LongCiteUtil::eregTrim($matches[1]);
        $result = mb_ereg_replace('\ ',$this->inUnder,$result);
        $dd = $this->inDash . $this->inDash;
        return $dd . $result . $dd;
    }

    protected function parseNameCurlyMatch($matches) {
        $result = LongCiteUtil::eregTrim($matches[1]);
        $result = mb_ereg_replace('\ ',$this->inUnder,$result);
        return $this->inCurlyL . $result . $this->inCurlyR;
    }

    public function getRawName() { return $this->rawName; }
    public function getRawTitles() { return $this->rawTitles; }
    public function getRawPositions() { return $this->rawPositions; }
    public function getRawCredentials() { return $this->rawCredentials; }
    public function getRawNicknames() { return $this->rawNicknames; }
    public function getRawDisambiguators() { return $this->rawDisambigs; }
    public function getAnnNameParts($langCode=null) {
        if($langCode===null) {
            return $this->annNameParts;
        }
        $fromLangCode = $this->langCodeParsed;
        $toLangCode = mb_strtolower($langCode);
        $results = array();
        $prefGend = null;
        $indGend = null;
        foreach($this->annNameParts as $namePart) {
            $rawPart  = $namePart[0];
            $partType = $namePart[1];
            if($partType==self::NamePartTitle) {
                $pat = '^longcite\-nst\-.*$';
                $newPart = LongCiteUtil::i18nTranslateWord(
                    $rawPart,$fromLangCode,$toLangCode,$pat,$prefGend,$indGend
                );
                if($newPart===false) {
                    $results = $namePart;
                } else {
                    $results[] = array($newPart,$partType);
                    if($indGend!==LongCiteUtil::GenderUnknown) {
                        $prefGend = $indGend;
                    }
                }
            } elseif($partType==self::NamePartQualifier) {
                $pat = '^longcite\-nsq\-.*$';
                $newPart = LongCiteUtil::i18nTranslateWord(
                    $rawPart,$fromLangCode,$toLangCode,$pat,$prefGend,$indGend
                );
                if($newPart===false) {
                    $results = $namePart;
                } else {
                    $results[] = array($newPart,$partType);
                    if($indGend!==LongCiteUtil::GenderUnknown) {
                        $prefGend = $indGend;
                    }
                }
            } elseif($partType==self::NamePartCredential) {
                $pat = '^longcite\-nsc\-.*$';
                $newPart = LongCiteUtil::i18nTranslateWord(
                    $rawPart,$fromLangCode,$toLangCode,$pat,$prefGend,$indGend
                );
                if($newPart===false) {
                    $results = $namePart;
                } else {
                    $results[] = array($newPart,$partType);
                    if($indGend!==LongCiteUtil::GenderUnknown) {
                        $prefGend = $indGend;
                    }
                }
            } elseif($partType==self::NamePartDisambiguator) {
                $pat = '^longcite\-nsd\-.*$';
                $newPart = LongCiteUtil::i18nTranslateWord(
                    $rawPart,$fromLangCode,$toLangCode,$pat,$prefGend,$indGend
                );
                if($newPart===false) {
                    $newWords = array();
                    foreach(mb_split('\ ',$rawPart) as $rawWord) {
                        $newWord = LongCiteUtil::i18nTranslateWord(
                            $rawWord,$fromLangCode,$toLangCode,$pat,$prefGend,$indGend
                        );
                        if($newWord===false) {
                            $newWords[] = $rawWord;
                        } else {
                            $newWords[] = $newWord;
                        }
                    }
                    $newPart = implode(" ",$newWords);
                    $results[] = array($newPart,$partType);
                } else {
                    $results[] = array($newPart,$partType);
                    if($indGend!==LongCiteUtil::GenderUnknown) {
                        $prefGend = $indGend;
                    }
                }
            } else {
                $results[] = $namePart;
            }
        }
        return $results;
    }
    public function getShortName() {
        $annParts = $this->getAnnNameParts();
        $surname  = "";
        $initials = "";
        foreach($annParts as $annPart) {
            $val = $annPart[0];
            $typ = $annPart[1];
            if($typ==self::NamePartSurname) {
                $surname = $val;
            } elseif($typ==self::NamePartName) {
                $initials .= mb_substr($val,0,1);
            }
        }
        $shortName = "$surname $initials";
        $shortName = LongCiteUtil::eregTrim($shortName);
        $shortName = mb_ereg_replace('\ ',"_",$shortName);
        return $shortName;
    }

    public function getRenderedNameAll($langCode=null) {
        if($langCode===null) {
            $langCode = $this->langCodeParsed;
        } else {
            $langCode = mb_strtolower($langCode);
        }
        $annParts = $this->getAnnNameParts($langCode);
        // init result
        $result = "";
        // do titles
        foreach($annParts as $annPart) {
            if($annPart[1]!==self::NamePartTitle) { continue; }
            $result .= " " . $annPart[0];
        }
        // do everything except credentials and positions (and titles)
        $skip = array(self::NamePartTitle,self::NamePartCredential,self::NamePartPosition);
        foreach($annParts as $annPart) {
            if(in_array($annPart[1],$skip)) { continue; }
            if($annPart[1]==self::NamePartName) {
                $result .= " " . $annPart[0];
            } elseif($annPart[1]==self::NamePartSurname) {
                $result .= " " . $annPart[0];
            } elseif($annPart[1]==self::NamePartNickname) {
                $result .= ' "' . $annPart[0] . '"';
            } elseif($annPart[1]==self::NamePartQualifier) {
                $result .= ', ' . $annPart[0];
            } elseif($annPart[1]==self::NamePartDisambiguator) {
                $result .= ' ' . $annPart[0];
            }
        }
        // do credentials
        foreach($annParts as $annPart) {
            if($annPart[1]!==self::NamePartCredential) { continue; }
            $result .= ", " . $annPart[0];
        }
        // do positions
        $posCnt = 0;
        foreach($annParts as $annPart) {
            if($annPart[1]!==self::NamePartPosition) { continue; }
            $result .= " (" . $annPart[0] . ")";
        }
        // return results
        $result = LongCiteUtil::eregTrim($result);
        $result = mb_ereg_replace('\ +'," ",$result);  // collapse spaces
        return $result;
    }

    // Footnotes:
    // *1* : https://en.wikipedia.org/wiki/Index_of_religious_honorifics_and_titles
    // *2* : https://en.wikipedia.org/wiki/The_Honourable
    // *   : https://dict.tu-chemnitz.de
    // *   : https://www.dict.cc/english-german/

}
?>
