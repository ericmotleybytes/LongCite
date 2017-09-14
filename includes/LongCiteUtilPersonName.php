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


    protected $langCode    = "en";     /// e.g., "en" or "de"
    protected $rawName     = "";       /// e.g., 'Robert "Bob" K. von_Jones, Jr.'
    protected $givenName   = "";       /// e.g., "Robert"
    protected $middleNames = array();  /// e.g., ["K."]
    protected $surname     = "";       /// e.g., "Jones"
    protected $disambig    = "";       /// e.g., "of Elea","of Citium"
    protected $nicknames   = array();  /// e.g., ["Bob"]
    protected $salutation  = array();  /// e.g., ["Mr."]
    protected $titles      = array();  /// e.g., ["Dr.", ]
    protected $qualifiers  = array();  /// e.g., ["Jr."]
    protected $credentials = array();  /// e.g., ["M.D.","CPA"]
    protected $taglines    = array();  /// e.g., ["King of France"]
    protected $gender      = "";       /// e.g., "male", "female"

    /// Class constructor.
    public function __construct($rawName,$langCode="en") {
        $this->rawName  = $rawName;
        $this->langCode = $langCode;
    }

    // Footnotes:
    // *1* : https://en.wikipedia.org/wiki/Index_of_religious_honorifics_and_titles
    // *2* : https://en.wikipedia.org/wiki/The_Honourable

}
?>
