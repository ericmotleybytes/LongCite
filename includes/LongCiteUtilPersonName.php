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

    protected static $supportedLangCodes = array("en","de");
    protected static $prefTitleAbbrevs = array(
        "en" => array(
            "Archbishop" => array("archbishop","de=Erzbischof"),
            "Bishop" => array("bishop","bish.","bish","de=Bischof"),  //*1*
            "Chancellor" => array("chancellor","chan.","chan",
                "de=Kanzler/Kanzlerin"),
            "Dalai Lama" => array("dalai lama","de=Dalai Lama"),
            "Dir."  => array("dir.","dir","director","de=Dir."),
            "Dr."   => array("dr.","dr","doctor","de=Dr."),
            "Hon."  => array("hon.","hon",
                "honorable","honourable","de=Geehrter Hr."), //*2*
            "Lama"  => array("lama","de=Lama"),
            "Lord"  => array("lord","de=Gnädiger Hr."),
            "Miss"  => array("miss","de=Frl."),
            "Mr."   => array("mr.","mr","mister","master","de=Hr."),
            "Mrs."  => array("mrs.","mrs","mistress","missus","de=Fr."),
            "Ms."   => array("ms.","ms","de=Fr."),
            "P.M."  => array("p.m.","pm.","pm","prime minister","de=P.M."),
            "Pastor"=> array("pastor","pr.","pr","ptr.","ptr","de=Pastor"),
            "Pope"  => array("pope","de=Papst"),
            "Pres." => array("pres.","pres","president","de=Präs."),
            "Prof." => array("prof.","prof","professor","de=Prof."),
            "Rabbi" => array("rabbi","de=Rabbi"),
            "Rep."  => array("rep.","rep","representative",
                "congressman","congresswoman","de=Abgeordnete"),
            "Rev."  => array("rev.","rev","revd.","revd","reverend","de=Hochw."),
            "Rt. Rev." => array("rt. rev.","rt.rev.","rt.rev","rt rev",
                "right reverend","de=Hochw."),
            "Sen."  => array("sen.","sen","senator","de=Senator/Senatorin"),
            "Sir"   => array("sir","de=Gnädiger Hr."),
            "V.P."  => array("v.p.","vp","vice president","de=Vizepräs."),
        ),
        "de" => array(
            "Abgeordnete"  => array("abgeordnete","en=Rep."),
            "Bischof"=> array("bischof","en=Bishop"),
            "Dalai Lama" => array("dalai lama","en=Dalai Lama"),
            "Dir."  => array("dir.","dir","direktor","direktorin","de=Dir."),
            "Dr."    => array("dr.","dr","doktor","en=Dr."),
            "Erzbischof" => array("erzbischof","en=Archbishop"),
            "Fr."    => array("fr.","fr","frau","dame","en=Mrs."),
            "Frl."   => array("frl.","frl","fräulein","en=Miss"),
            "Geehrter Hr." =>
                array("geehrter hr.","geehrter hr.","geehrter herr","en=Hon."),
            "Gnädiger Hr." =>
                array("gnädiger hr.","gnädiger hr.","gnädiger herr","en=Sir"),
            "Hochw." => array("hochw.","hochw","hochwürden","en=Rev."),
            "Hr."    => array("hr.","hr","herr","en=Mr."),
            "Hrn."   => array("hrn.","hrn","herrn","en=Mr."),
            "Kanzler" => array("kanzler","en=Chancellor"),
            "Kanzlerin" => array("kanzlerin","en=Chancellor"),
            "Lama"   => array("lama","en=Lama"),
            "P.M."      => array("p.m.","pm.","pm",
                "premierminister","premireministerin","en=P.M."),
            "Papst"  => array("pope","en=Pope"),
            "Pastor" => array("pastor","en=Pastor"),
            "Präs."  => array("präs.","präs","präsident","präsidentin","en=Pres."),
            "Prof."  => array("prof.","prof","professor","en=Prof."),
            "Rabbi"  => array("rabbi","en=Rabbi"),
            "Senator"   => array("senator","en=Sen."),
            "Senatorin" => array("senatorin","en=Sen."),
            "Vizepräs." => array("vizepräs.","vizepräs",
                "vizepräsident","vizepräsidentin","en=V.P."),
        ),
    );
    protected static $prefCredAbbrevs = array(
        "en" => array(
            "J.D."  => array("j.d.","jd.","jd",
                "d.jur.","djur.","djur","doctor of jurisprudence","de=Dr. jud."),
            "M.D."  => array("m.d.","md.","md","de=Dr. med."),
            "P.M."  => array("p.m.","pm.","pm","prime minister","de=P.M."),
            "PhD."  => array("phd.","ph.d.","phd","doctor of philosophy","de=Dr. phil."),
        ),
        "de" => array(
            "Dr. med." => array("dr.","dr","doktor","en=M.D."),
            "Dr. jud." => array("dr. jud.","dr jud.","dr.jud.","dr jud","j.u.d.","jud",
                "juris utriusque doktor","juris utriusque","en=J.D." ),
            "Dr. phil." => array("dr. phil.","dr phil.","dr.phil.","dr phil",
                "doktor der philosophie","en=PhD."),
            "P.M."      => array("p.m.","pm.","pm",
                "premierminister","premireministerin","en=P.M."),
        ),
    );
    protected static $prefQualAbbrevs = array(
        "en" => array(
            "Jr."  => array("jr.","jr","junior","de=d.J."),
            "Sr."  => array("sr.","sr","senior","de=d.Ä."),
            "I"    => array("i","i.","first","the first","de=I"),
            "II"   => array("ii","ii.","second","the second","de=II"),
            "III"  => array("iii","iii.","third","the third","de=III"),
            "IV"   => array("iv","iv.","forth","the forth","de=IV"),
        ),
        "de" => array(
            "d.J." => array("d.j.","dj.","dj","der jüngere","en=Jr."),
            "d.Ä." => array("d.ä.","dä.","dä","der ältere","en=Sr."),
            "I"    => array("i","i.","erste","der erste","en=I"),
            "II"   => array("ii","ii.","zweite","der zweite","en=II"),
            "III"  => array("iii","iii.","dritte","der dritte","en=III"),
            "IV"   => array("iv","iv.","vierte","der vierte","en=IV"),
        ),
    );

    public static function standardTitle($raw,$inLangCode,$outLangCode,$isMasculine=null) {
        $msgKeyPrefix = "longcite-nst-";
        // check for Mr.
        
        $result = LongCiteUtil::i18nRender($this->langCode,$msgKey);
        
    }

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
        $adjName = trim($rawName);
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
            $adjName = trim($workName);
            $adjName = mb_ereg_replace('\ +'," ",$adjName); // collapse spaces
        }
        return $adjName;
    }

    protected function parseTitlesMatch($matches) {
        $matchString = $matches[1];
        $delimPat = "\\" . $this->inSemi;
        $titles = mb_split($delimPat,$matchString);
        foreach($titles as $title) {
            $this->rawTitles[] = trim($title);
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
            $adjName = trim($workName);
            $adjName = mb_ereg_replace('\ +'," ",$adjName); // collapse spaces
        }
        return $adjName;
    }

    protected function parsePositionsMatch($matches) {
        $matchString = $matches[1];
        $delimPat = "\\" . $this->inBar;
        $positions = mb_split($delimPat,$matchString);
        foreach($positions as $position) {
            $this->rawPositions[] = trim($position);
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
            $adjName = trim($workName);
            $adjName = mb_ereg_replace('\ +'," ",$adjName); // collapse spaces
        }
        return $adjName;
    }

    protected function parseCredentialsMatch($matches) {
        $matchString = $matches[1];
        $delimPat = "\\" . $this->inSemi;
        $credentials = mb_split($delimPat,$matchString);
        foreach($credentials as $credential) {
            $this->rawCredentials[] = trim($credential);
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
                $namePart = trim($namePart);
                if(mb_strlen($namePart)>0) {
                    $nameAnnotatedParts[] = array($namePart,self::NamePartNickname);
                }
                continue;
            }
            $isDisambiguator = mb_ereg($patDisambig,$namePart);
            if($isDisambiguator!==false) {
                $namePart = mb_ereg_replace($edd,"",$namePart);  // remove --'s
                $namePart = mb_ereg_replace($patUnderFix," ",$namePart);
                $namePart = trim($namePart);
                if(mb_strlen($namePart)>0) {
                    $nameAnnotatedParts[] = array($namePart,self::NamePartDisambiguator);
                }
                continue;
            }
            $isQualifier = mb_ereg($patQual,$namePart);
            if($isQualifier!==false) {
                $namePart = mb_ereg_replace($eCurlyL,"",$namePart);  // remove {'s
                $namePart = mb_ereg_replace($eCurlyR,"",$namePart);  // remove }'s
                $namePart = trim($namePart);
                $quals = mb_split("\\".$this->inSemi,$namePart);
                foreach($quals as $qual) {
                    $qual = mb_ereg_replace("\\" . $this->inUnder," ",$namePart);
                    $qual = trim($qual);
                    $nameAnnotatedParts[] = array($qual,self::NamePartQualifier);
                }
                continue;
            }
            $isSurname = mb_ereg($eu,$namePart);
            if($isSurname!==false) {
                $namePart = mb_ereg_replace($patUnderFix," ",$namePart);
                $namePart = trim($namePart);
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
        $result = trim($matches[1]);
        $result = mb_ereg_replace('\ ',$this->inUnder,$result);
        return $this->inQuote2 . $result . $this->inQuote2;
    }

    protected function parseNameDdMatch($matches) {
        $result = trim($matches[1]);
        $result = mb_ereg_replace('\ ',$this->inUnder,$result);
        $dd = $this->inDash . $this->inDash;
        return $dd . $result . $dd;
    }

    protected function parseNameCurlyMatch($matches) {
        $result = trim($matches[1]);
        $result = mb_ereg_replace('\ ',$this->inUnder,$result);
        return $this->inCurlyL . $result . $this->inCurlyR;
    }

    public function getRawName() { return $this->rawName; }
    public function getRawTitles() { return $this->rawTitles; }
    public function getRawPositions() { return $this->rawPositions; }
    public function getRawCredentials() { return $this->rawCredentials; }
    public function getRawNicknames() { return $this->rawNicknames; }
    public function getRawDisambiguators() { return $this->rawDisambigs; }
    public function getAnnNameParts() { return $this->annNameParts; }

    // Footnotes:
    // *1* : https://en.wikipedia.org/wiki/Index_of_religious_honorifics_and_titles
    // *2* : https://en.wikipedia.org/wiki/The_Honourable

}
?>
