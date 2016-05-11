<?

require_once(__DIR__ . "/../KodiClass.php");  // diverse Klassen
/*
 * @addtogroup kodi
 * @{
 *
 * @package       Kodi
 * @file          module.php
 * @author        Michael Tröger
 *
 */

/**
 * KodiDeviceApplication Klasse für den Namespace Player der KODI-API.
 * Erweitert KodiBase.
 *
 */
class KodiDevicePlayer extends KodiBase
{

    /**
     * PlayerID für Audio
     * 
     * @access private
     * @static integer
     * @value 0
     */
    const Audio = 0;

    /**
     * PlayerID für Video
     * 
     * @access private
     * @static integer
     * @value 1
     */
    const Video = 1;

    /**
     * PlayerID für Bilder
     * 
     * @access private
     * @static integer
     * @value 2
     */
    const Pictures = 2;

    /**
     * RPC-Namespace
     * 
     * @access private
     *  @var string
     * @value 'Application'
     */
    static $Namespace = 'Player';

    /**
     * Alle Properties des RPC-Namespace
     * 
     * @access private
     *  @var array 
     */
    static $Properties = array(
        "type",
        "partymode",
        "speed",
        "time",
        "percentage",
        "totaltime",
        "playlistid",
        "position",
        "repeat",
        "shuffled",
        "canseek",
        "canchangespeed",
        "canmove",
        "canzoom",
        "canrotate",
        "canshuffle",
        "canrepeat",
        "currentaudiostream",
        "audiostreams",
        "subtitleenabled",
        "currentsubtitle",
        "subtitles",
        "live"
    );

    /**
     * Ein Teil der Properties des RPC-Namespace für Statusmeldungen
     * 
     * @access private
     *  @var array 
     */
    static $PartialProperties = array(
        "type",
        "partymode",
        "speed",
        "time",
        "percentage",
        "repeat",
        "shuffled",
        "currentaudiostream",
        "subtitleenabled",
        "currentsubtitle"
    );

    /**
     * Alle Properties eines Item
     * 
     * @access private
     *  @var array 
     */
    static $ItemList = array(
        "title",
        "artist",
        "albumartist",
        "genre",
        "year",
        "rating",
        "album",
        "track",
        "duration",
        "comment",
        "lyrics",
        "musicbrainztrackid",
        "musicbrainzartistid",
        "musicbrainzalbumid",
        "musicbrainzalbumartistid",
        "playcount",
        "fanart",
        "director",
        "trailer",
        "tagline",
        "plot",
        "plotoutline",
        "originaltitle",
        "lastplayed",
        "writer",
        "studio",
        "mpaa",
        "cast",
        "country",
        "imdbnumber",
        "premiered",
        "productioncode",
        "runtime",
        "set",
        "showlink",
        "streamdetails",
        "top250",
        "votes",
        "firstaired",
        "season",
        "episode",
        "showtitle",
        "thumbnail",
        "file",
        "resume",
        "artistid",
        "albumid",
        "tvshowid",
        "setid",
        "watchedepisodes",
        "disc",
        "tag",
        "art",
        "genreid",
        "displayartist",
        "albumartistid",
        "description",
        "theme",
        "mood",
        "style",
        "albumlabel",
        "sorttitle",
        "episodeguide",
        "uniqueid",
        "dateadded",
        "channel",
        "channeltype",
        "hidden",
        "locked",
        "channelnumber",
        "starttime",
        "endtime");

    /**
     * Kleiner Teil der Properties eines Item
     * 
     * @access private
     *  @var array 
     */
    static $ItemListSmall = array(
        "title",
        "artist",
        "albumartist",
        "genre",
        "year",
        "album",
        "track",
        "duration",
        "plot",
        "runtime",
        "season",
        "episode",
        "showtitle",
        "thumbnail",
        "file",
        "disc",
        "albumlabel",
    );

    /**
     * Eigene PlayerId
     * 
     * @access private
     *  @var integer Kodi-Player-ID dieser Instanz 
     */
    private $PlayerId = null;

    /**
     * Wenn dieser Player in Kodi gerade Active ist, true sonst false.
     * 
     * @access private
     *  @var boolean true = aktiv, false = inaktiv, null wenn nicht bekannt.
     */
    private $isActive = null;

    /**
     * Zuordnung der von Kodi gemeldeten Medientypen zu den PlayerIDs
     * 
     * @access private
     *  @var array Key ist der Medientyp, Value die PlayerID
     */
    static $Playertype = array(
        "song" => 0,
        "audio" => 0,
        "video" => 1,
        "episode" => 1,
        "movie" => 1,
        "pictures" => 2
    );

    /**
     * Interne Funktion des SDK.
     *
     * @access public
     */
    public function Create()
    {
        parent::Create();
        $this->RegisterPropertyInteger('PlayerID', 0);
        $this->RegisterPropertyInteger('CoverSize', 300);
        $this->RegisterPropertyString('CoverTyp', 'thumb');
    }

    /**
     * Interne Funktion des SDK.
     * 
     * @access public
     */
    public function ApplyChanges()
    {
        $this->Init();
        $this->RegisterVariableBoolean("_isactive", "isplayeractive", "", -5);
        IPS_SetHidden($this->GetIDForIdent('_isactive'), true);

        $this->RegisterProfileIntegerEx("Repeat.Kodi", "", "", "", Array(
            //Array(0, "Prev", "", -1),
            Array(0, "Aus", "", -1),
            Array(1, "Titel", "", -1),
            Array(2, "Playlist", "", -1)
        ));
        $this->RegisterProfileIntegerEx("Speed.Kodi", "Intensity", "", "", Array(
            //Array(0, "Prev", "", -1),
            Array(-32, "32 <<", "", -1),
            Array(-16, "16 <<", "", -1),
            Array(-8, "8 <<", "", -1),
            Array(-4, "4 <<", "", -1),
            Array(-2, "2 <<", "", -1),
            Array(-1, "1 <<", "", -1),
            Array(0, "Pause", "", 0x0000FF),
            Array(1, "Play", "", 0x00FF00),
            Array(2, "2 >>", "", -1),
            Array(4, "4 >>", "", -1),
            Array(8, "8 >>", "", -1),
            Array(16, "16 >>", "", -1),
            Array(32, "32 >>", "", -1)
        ));

        $this->RegisterProfileInteger("Intensity.Kodi", "Intensity", "", " %", 0, 100, 1);

        switch ($this->PlayerId)
        {
            case self::Audio:
                $this->UnregisterVariable("showtitle");
                $this->UnregisterVariable("season");
                $this->UnregisterVariable("episode");
                $this->UnregisterVariable("plot");
                $this->UnregisterVariable("audioindex");
                $this->UnregisterVariable("audiolanguage");
                $this->UnregisterVariable("audiochannels");
                $this->UnregisterVariable("audiocodec");
                $this->UnregisterVariable("audiobitrate");
                $this->UnregisterVariable("audiostreams");
                $this->UnregisterVariable("subtitleenabled");
                $this->UnregisterVariable("subtitle");
                $this->UnregisterVariable("subtitles");

                $this->UnregisterProfile("AudioTracks." . $this->InstanceID . ".Kodi");

                $this->RegisterProfileIntegerEx("Status." . $this->InstanceID . ".Kodi", "Information", "", "", Array(
                    Array(0, "Prev", "", -1),
                    Array(1, "Stop", "", -1),
                    Array(2, "Play", "", -1),
                    Array(3, "Pause", "", -1),
                    Array(4, "Next", "", -1)
                ));

                $this->RegisterVariableInteger("position", "Playlist Position", "", 9);
                $this->RegisterVariableInteger("repeat", "Wiederholen", "Repeat.Kodi", 11);
                $this->RegisterVariableBoolean("shuffled", "Zufall", "~Switch", 12);
                $this->RegisterVariableBoolean("partymode", "Partymodus", "~Switch", 13);
                $this->EnableAction("partymode");
                $this->RegisterVariableString("album", "Album", "", 15);
                $this->RegisterVariableInteger("track", "Track", "", 16);
                $this->RegisterVariableInteger("disc", "Disc", "", 17);
                $this->RegisterVariableString("artist", "Artist", "", 20);
                $this->RegisterVariableString("lyrics", "Lyrics", "", 30);

                break;
            case self::Video:
                $this->UnregisterVariable("position");
                $this->UnregisterVariable("repeat");
                $this->UnregisterVariable("shuffled");
                $this->UnregisterVariable("partymode");
                $this->UnregisterVariable("album");
                $this->UnregisterVariable("track");
                $this->UnregisterVariable("disc");
                $this->UnregisterVariable("artist");
                $this->UnregisterVariable("lyrics");

                $this->RegisterProfileIntegerEx("Status." . $this->InstanceID . ".Kodi", "Information", "", "", Array(
                    Array(1, "Stop", "", -1),
                    Array(2, "Play", "", -1),
                    Array(3, "Pause", "", -1)
                ));
                $this->RegisterProfileInteger("AudioTracks." . $this->InstanceID . ".Kodi", "", "", "", 1, 1, 1);

                $this->RegisterVariableString("showtitle", "Serie", "", 13);
                $this->RegisterVariableInteger("season", "Staffel", "", 15);
                $this->RegisterVariableInteger("episode", "Episode", "", 16);

                $this->RegisterVariableString("plot", "Handlung", "~TextBox", 19);
                $this->RegisterVariableInteger("audioindex", "Aktueller Audiotrack", "AudioTracks." . $this->InstanceID . ".Kodi", 30);
                $this->RegisterVariableString("audiolanguage", "Sprache", "", 31);
                $this->RegisterVariableInteger("audiochannels", "Audiokanäle", "", 32);
                $this->RegisterVariableString("audiocodec", "Audio Codec", "", 23);
                $this->RegisterVariableInteger("audiobitrate", "Audio Bitrate", "", 34);
                $this->RegisterVariableInteger("audiostreams", "Anzahl Audiotracks", "", 35);
                $this->RegisterVariableBoolean("subtitleenabled", "Untertitel aktiv", "~Switch", 40);
                $this->RegisterVariableInteger("subtitle", "Aktiver Untertitel", "Subtitels." . $this->InstanceID . ".Kodi", 41);
                $this->RegisterVariableInteger("subtitles", "Anzahl Untertitel", "", 42);
                break;
            case self::Pictures:
                $this->RegisterProfileIntegerEx("Status." . $this->InstanceID . ".Kodi", "Information", "", "", Array(
                    Array(0, "Prev", "", -1),
                    Array(1, "Stop", "", -1),
                    Array(2, "Play", "", -1),
                    Array(3, "Pause", "", -1),
                    Array(4, "Next", "", -1)
                ));
                break;
        }
        $this->RegisterVariableString("label", "Titel", "", 14);
        $this->RegisterVariableString("genre", "Genre", "", 21);
        $this->RegisterVariableInteger("Status", "Status", "Status." . $this->InstanceID . ".Kodi", 3);
        $this->EnableAction("Status");
        $this->RegisterVariableInteger("speed", "Geschwindigkeit", "Speed.Kodi", 10);
        $this->RegisterVariableInteger("year", "Jahr", "", 19);
//        $this->RegisterVariableString("type", "Typ", "", 20);
        $this->RegisterVariableString("duration", "Dauer", "", 24);
        $this->RegisterVariableString("time", "Spielzeit", "", 25);
        $this->RegisterVariableInteger("percentage", "Position", "Intensity.Kodi", 26);

        parent::ApplyChanges();


        $this->getActivePlayer();


        if ($this->isActive)
            $this->GetItemInternal();

        $this->RegisterTimer('PlayerStatus', 0, 'KODIPLAYER_RequestState($_IPS[\'TARGET\'],"PARTIAL");');
    }

################## PRIVATE     

    /**
     * Setzt die Eigenschaften isActive und PlayerId der Instanz
     * damit andere Funktionen Diese nutzen können
     * 
     * @access private
     */
    private function Init()
    {
        if (is_null($this->PlayerId))
            $this->PlayerId = $this->ReadPropertyInteger('PlayerID');
        if (is_null($this->isActive))
            $this->isActive = GetValueBoolean($this->GetIDForIdent('_isactive'));
    }

    /**
     * Fragt Kodi an ob der Playertyp der Instanz gerade aktiv ist.
     * 
     * @return boolean true wenn Player aktiv ist, sonset false
     */
    private function getActivePlayer()
    {
        $this->Init();
        $KodiData = new Kodi_RPC_Data(static::$Namespace, 'GetActivePlayers');
        $ret = $this->Send($KodiData);
        if (is_null($ret) or ( count($ret) == 0))
            $this->isActive = false;
        else
            $this->isActive = ((int) $ret[0]->playerid == $this->PlayerId);

        $this->SetValueBoolean('_isactive', $this->isActive);
        return (bool) $this->isActive;
    }

    /**
     * Setzt die Eigenschaft isActive sowie die dazugehörige IPS-Variable.
     * 
     * @access private
     * @param boolean $isActive True wenn Player als aktive gesetzt werden soll, sonder false.
     */
    private function setActivePlayer(boolean $isActive)
    {
        $this->isActive = $isActive;
        $this->SetValueBoolean('_isactive', $isActive);
    }

    /**
     * Werte der Eigenschaften anfragen.
     * 
     * @access protected
     * @param array $Params Enthält den Index "properties", in welchen alle anzufragenden Eigenschaften als Array enthalten sind.
     * @return boolean true bei erfolgreicher Ausführung und dekodierung, sonst false.
     */
    protected function RequestProperties(array $Params)
    {
        $this->Init();
        $Param = array_merge($Params, array("playerid" => $this->PlayerId));
        //parent::RequestProperties($Params);
        if (!$this->isActive)
            return false;
        $KodiData = new Kodi_RPC_Data(static::$Namespace, 'GetProperties', $Param);
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        $this->Decode('GetProperties', $ret);
        return true;
    }

    /**
     * Dekodiert die empfangenen Daten und führt die Statusvariablen nach.
     * 
     * @access protected
     * @param string $Method RPC-Funktion ohne Namespace
     * @param object $KodiPayload Der zu dekodierende Datensatz als Objekt.
     */
    protected function Decode($Method, $KodiPayload)
    {
        $this->Init();
        if (property_exists($KodiPayload, 'player'))
        {
            if ($KodiPayload->player->playerid <> $this->PlayerId)
                return false;
        }
        else
        {
            if (property_exists($KodiPayload, 'type'))
            {
                if (self::$Playertype[(string) $KodiPayload->type] <> $this->PlayerId)
                    return false;
            }
            else
            {
                if (property_exists($KodiPayload, 'item'))
                {
                    if (self::$Playertype[(string) $KodiPayload->item->type] <> $this->PlayerId)
                        return false;
                }
            }
        }
        switch ($Method)
        {
            case 'GetProperties':
            case 'OnPropertyChanged':
                foreach ($KodiPayload as $param => $value)
                {
                    /*
                      {"audiostreams":[{"bitrate":416825,"channels":6,"codec":"ac3","index":0,"language":"ger","name":"AC3 5.1"}],

                      "canchangespeed":true,"canmove":false,"canrepeat":true,"canrotate":false,
                      "canseek":true,"canshuffle":true,"canzoom":false,
                      "currentaudiostream":{"bitrate":416825,"channels":6,"codec":"ac3","index":0,"language":"ger","name":"AC3 5.1"},
                      "currentsubtitle":{},
                      "live":false,"partymode":false,
                      "percentage":1.855262279510498,
                      "playlistid":1,"position":-1,"repeat":"off",
                      "shuffled":false,"speed":1,
                      "subtitleenabled":true,"subtitles":[],"time":{"hours":0,"milliseconds":446,"minutes":2,"seconds":49},"totaltime":{"hours":2,"milliseconds":264,"minutes":32,"seconds":13},"type":"video"}
                     */
                    switch ($param)
                    {
                        // Object
                        case "currentsubtitle":
                            if ($this->PlayerId <> self::Video)
                                break;
                            if (is_object($value))
                            {
                                if (property_exists($value, 'index'))
                                {
                                    $this->SetValueInteger('subtitle', (int) $value->index);
//                                    $this->SetValueBoolean('subtitleenabled', true);
                                }
                                else
                                {
                                    $this->SetValueInteger('subtitle', -1);
                                    //                                  $this->SetValueBoolean('subtitleenabled', false);
                                }
                            }
                            else
                            {
                                $this->SetValueInteger('subtitle', -1);
                            }
                            break;
                        case "currentaudiostream":
                            if ($this->PlayerId <> self::Video)
                                break;
                            if (is_object($value))
                            {
                                if (property_exists($value, 'bitrate'))
                                    $this->SetValueInteger('audiobitrate', (int) $value->bitrate);
                                else
                                    $this->SetValueInteger('audiobitrate', 0);

                                if (property_exists($value, 'channels'))
                                    $this->SetValueInteger('audiochannels', (int) $value->channels);
                                else
                                    $this->SetValueInteger('audiochannels', 0);

                                if (property_exists($value, 'index'))
                                    $this->SetValueInteger('audioindex', (int) $value->index);
                                else
                                    $this->SetValueInteger('audioindex', 0);

                                if (property_exists($value, 'language'))
                                    $this->SetValueString('audiolanguage', (string) $value->language);
                                else
                                    $this->SetValueString('audiolanguage', "");

                                if (property_exists($value, 'name'))
                                    $this->SetValueString('audiocodec', (string) $value->name);
                                else
                                    $this->SetValueString('audiocodec', "");
                            } else
                            {
                                $this->SetValueInteger('audiobitrate', 0);
                                $this->SetValueInteger('audiochannels', 0);
                                $this->SetValueInteger('audioindex', 0);
                                $this->SetValueString('audiolanguage', "");
                                $this->SetValueString('audiocodec', "");
                            }
                            break;
                        //string
                        case "type":
//                            $this->SetValueString($param, (string) $value);
                            break;
                        //time
                        case "totaltime":
                            $this->SetValueString('duration', $this->ConvertTime($value));
                            break;
                        case "time":
                            $this->SetValueString($param, $this->ConvertTime($value));
                            break;
                        // Anzahl
                        case "audiostreams":
                            if ($this->PlayerId <> self::Video)
                                break;
                            $this->SetValueInteger($param, count($value));
                            //Profil anpassen
                            break;
                        case "subtitles":
                            if ($this->PlayerId <> self::Video)
                                break;
                            $this->SetValueInteger($param, count($value));
                            //Profil anpassen
                            break;
                        case "repeat": //off
                            if ($this->PlayerId == self::Video)
                                break;
                            $this->SetValueInteger($param, array_search((string) $value, array("off", "one", "all")));
                            break;
                        //boolean
                        case "shuffled":
                        case "partymode":
                            if ($this->PlayerId == self::Video)
                                break;
                            $this->SetValueBoolean($param, (bool) $value);
                            break;

                        case "subtitleenabled":
                            if ($this->PlayerId <> self::Video)
                                break;
                            $this->SetValueBoolean($param, (bool) $value);
                            break;
                        //integer
                        case "speed":
                            if ((int) $value == 0)
                                $this->SetValueInteger('Status', 3);
                            else
                                $this->SetValueInteger('Status', 2);
                        case "percentage":
                            $this->SetValueInteger($param, (int) $value);
                            break;
                        case "position":
                            //                        if ($this->PlayerId == self::Video)
                            // TODO
                            // PLAYLIST refresh ?                                
                            //                              break;
//                            $this->SetValueInteger($param, (int) $value + 1);
                            if ($KodiPayload->playlist <> -1)
                            {
                                if ($this->SetValueInteger($param, (int) $value))
                                {
                                    // PLAYLIST refresh !
                                }
                            }

                            break;

                        /*    {"canrotate":false,"canzoom":false,
                          "currentsubtitle":null,
                          "live":false,"playlistid":1,
                          "subtitles":[],
                         */

                        //Action en/disable
                        case "canseek":
                            if ((bool) $value)
                                $this->EnableAction('percentage');
                            else
                                $this->DisableAction('percentage');
                            break;
                        case "canshuffle":
                            if ($this->PlayerId == self::Video)
                                break;
                            if ((bool) $value)
                                $this->EnableAction('shuffled');
                            else
                                $this->DisableAction('shuffled');
                            break;
                        case "canrepeat":
                            if ($this->PlayerId == self::Video)
                                break;
                            if ((bool) $value)
                                $this->EnableAction('repeat');
                            else
                                $this->DisableAction('repeat');
                            break;
                        case "canchangespeed":
                            if ((bool) $value)
                                $this->EnableAction('speed');
                            else
                                $this->DisableAction('speed');
                            break;
                        case "playlist":
                            if ($value == -1)
                            {
                                //Playlistvariable prüfen auf änderung
                                // dann Playlist leeren oder neu erstellen.
                            }
                            break;
                        default:
//                            IPS_LogMessage($param, print_r($value, true));
                            $this->SendDebug($param, $value, 0);

                            break;
                    }
                }
                break;
            case 'OnStop':
                $this->SetTimerInterval('PlayerStatus', 0);
                $this->SetValueInteger('Status', 1);
                $this->SetValueString('duration', '');
                $this->SetValueString('totaltime', '');
                $this->SetValueString('time', '');
                $this->SetValueInteger('percentage', 0);
                $this->setActivePlayer(false);
                IPS_RunScriptText('<? KODIPLAYER_RequestState(' . $this->InstanceID . ',"ALL");');
                IPS_RunScriptText('<? KODIPLAYER_GetItemInternal(' . $this->InstanceID . ');');

                break;
            case 'OnPlay':
                $this->setActivePlayer(true);
                $this->SetValueInteger('Status', 2);
                IPS_RunScriptText('<? KODIPLAYER_RequestState(' . $this->InstanceID . ',"ALL");');
                IPS_RunScriptText('<? KODIPLAYER_GetItemInternal(' . $this->InstanceID . ');');
                $this->SetTimerInterval('PlayerStatus', 2000);
                break;
            case 'OnPause':
                $this->SetTimerInterval('PlayerStatus', 0);
                $this->SetValueInteger('Status', 3);
                IPS_RunScriptText('<? KODIPLAYER_RequestState(' . $this->InstanceID . ',"ALL");');
                break;
            case 'OnSeek':
                $this->SetValueString('time', $this->ConvertTime($KodiPayload->player->time));
                break;
            case 'OnSpeedChanged':
                IPS_RunScriptText('<? KODIPLAYER_RequestState(' . $this->InstanceID . ',"speed");');
                break;
            default:
//                IPS_LogMessage($Method, print_r($KodiPayload, true));
                $this->SendDebug($Method, $KodiPayload, 0);
                break;
        }
    }

    /**
     * Holt das über $flie übergebene Cover vom Kodi-Webinterface, skaliert und konvertiert dieses und speichert es in einem MedienObjekt ab.
     * 
     * @access private
     * @param string $file
     */
    private function SetCover(string $file)
    {
//        $Ext = pathinfo($file, PATHINFO_EXTENSION);
        $CoverID = @IPS_GetObjectIDByIdent('CoverIMG', $this->InstanceID);
        $Size = $this->ReadPropertyString("CoverSize");
        if ($CoverID === false)
        {
            $CoverID = IPS_CreateMedia(1);
            IPS_SetParent($CoverID, $this->InstanceID);
            IPS_SetIdent($CoverID, 'CoverIMG');
            IPS_SetName($CoverID, 'Cover');
            IPS_SetPosition($CoverID, 27);
            IPS_SetMediaCached($CoverID, true);
            $filename = "media" . DIRECTORY_SEPARATOR . "Cover_" . $this->InstanceID . ".png";
            IPS_SetMediaFile($CoverID, $filename, False);
        }

        if ($file == "")
            $CoverRAW = FALSE;
        else
        {
            $ParentID = $this->GetParent();
            if ($ParentID !== false)
                $CoverRAW = KODIRPC_GetImage($ParentID, $file);
        }

        if (!($CoverRAW === false))
        {
            $image = @imagecreatefromstring($CoverRAW);
            if (!($image === false))
            {
                $width = imagesx($image);
                $height = imagesy($image);
                if ($height > $Size)
                {
                    $factor = $height / $Size;
                    $image = imagescale($image, $width / $factor, $height / $factor);
                }
                ob_start();
                @imagepng($image);
                $CoverRAW = ob_get_contents(); // read from buffer                
                ob_end_clean(); // delete buffer                
            }
        }

        if ($CoverRAW === false)
            $CoverRAW = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "nocover.png");

        IPS_SetMediaContent($CoverID, base64_encode($CoverRAW));
    }

################## ActionHandler

    /**
     * Actionhandler der Statusvariablen. Interne SDK-Funktion.
     * 
     * @access public
     * @param string $Ident Der Ident der Statusvariable.
     * @param boolean|float|integer|string $Value Der angeforderte neue Wert.
     */
    public function RequestAction($Ident, $Value)
    {
        switch ($Ident)
        {
            case "Status":
                switch ($Value)
                {
                    case 0: //Prev
                        $result = $this->Previous();
                        break;
                    case 1: //Stop
                        $result = $this->Stop();
                        break;
                    case 2: //Play
                        $result = $this->Play();
                        break;
                    case 3: //Pause
                        $result = $this->Pause();
                        break;
                    case 4: //Next
                        $result = $this->Next();
                        break;
                }
                return $result;
            case "shuffled":
                return $this->SetShuffle($Value);
            case "repeat":
                return $this->SetRepeat($Value);
            case "speed":
                return $this->SetSpeed($Value);
            case "partymode":
                return $this->SetPartymode($Value);
            case "percentage":
                return $this->SetPosition($Value);
//            default:
//                return trigger_error('Invalid Ident.', E_USER_NOTICE);
        }
    }

################## PUBLIC
    /*
      public function RawSend(string $Namespace, string $Method, $Params)
      {
      return parent::RawSend($Namespace, $Method, $Params);
      }
     */

    /**
     * IPS-Instanz-Funktion 'KODIPLAYER_GetItemInternal'.
     * Holt sich die Daten des aktuellen wiedergegebenen Items, und bildet die Eigenschaften in IPS-Variablen ab.
     * 
     * @access public
     */
    public function GetItemInternal()
    {
        $ret = $this->GetItem();
        if (is_null($ret))
            return null;
        switch ($this->PlayerId)
        {
            case self::Audio:
                $this->SetValueString('label', $ret->label);
//                $this->SetValueString('type', $ret->type);

                if (property_exists($ret, 'displayartist'))
                    $this->SetValueString('artist', $ret->displayartist);
                else
                {
                    if (property_exists($ret, 'albumartist'))
                    {
                        if (is_array($ret->artist))
                            $this->SetValueString('artist', implode(', ', $ret->albumartist));
                        else
                            $this->SetValueString('artist', $ret->albumartist);
                    }
                    else
                    {
                        if (property_exists($ret, 'artist'))
                        {
                            if (is_array($ret->artist))
                                $this->SetValueString('artist', implode(', ', $ret->artist));
                            else
                                $this->SetValueString('artist', $ret->artist);
                        }
                        else
                            $this->SetValueString('artist', "");
                    }
                }

                if (property_exists($ret, 'genre'))
                {
                    if (is_array($ret->genre))
                        $this->SetValueString('genre', implode(', ', $ret->genre));
                    else
                        $this->SetValueString('genre', $ret->genre);
                }
                else
                    $this->SetValueString('genre', "");

                if (property_exists($ret, 'album'))
                    $this->SetValueString('album', $ret->album);
                else
                    $this->SetValueString('album', "");

                if (property_exists($ret, 'year'))
                    $this->SetValueInteger('year', $ret->year);
                else
                    $this->SetValueInteger('year', 0);

                if (property_exists($ret, 'track'))
                    $this->SetValueInteger('track', $ret->track);
                else
                    $this->SetValueInteger('track', 0);

                if (property_exists($ret, 'disc'))
                    $this->SetValueInteger('disc', $ret->disc);
                else
                    $this->SetValueInteger('disc', 0);

                if (property_exists($ret, 'duration'))
                    $this->SetValueString('duration', $this->ConvertTime($ret->duration));
                else
                    $this->SetValueString('duration', "");

                if (property_exists($ret, 'lyrics'))
                    $this->SetValueString('lyrics', $ret->lyrics);
                else
                    $this->SetValueString('lyrics', "");

                switch ($this->ReadPropertyString('CoverTyp'))
                {
                    case"artist":
                        if (property_exists($ret, 'art'))
                        {
                            if (property_exists($ret->art, 'artist.fanart'))
                                if ($ret->art->{'artist.fanart'} <> "")
                                {
                                    $this->SetCover($ret->art->{'artist.fanart'});
                                    break;
                                }
                        }
                        if (property_exists($ret, 'fanart'))
                            if ($ret->fanart <> "")
                            {
                                $this->SetCover($ret->fanart);
                                break;
                            }
                    default:
                        if (property_exists($ret, 'art'))
                        {
                            if (property_exists($ret->art, 'thumb'))
                                if ($ret->art->thumb <> "")
                                {
                                    $this->SetCover($ret->art->thumb);
                                    break;
                                }
                        }
                        if (property_exists($ret, 'thumbnail'))
                        {
                            if ($ret->thumbnail <> "")
                            {
                                $this->SetCover($ret->thumbnail);
                                break;
                            }
                        }
                        $this->SetCover("");
                        break;
                }

                break;
            case self::Video:
                if (property_exists($ret, 'showtitle'))
                    $this->SetValueString('showtitle', $ret->showtitle);
                else
                    $this->SetValueString('showtitle', "");

                $this->SetValueString('label', $ret->label);
//                $this->SetValueString('type', $ret->type);

                if (property_exists($ret, 'season'))
                    $this->SetValueInteger('season', $ret->season);
                else
                    $this->SetValueInteger('season', -1);

                if (property_exists($ret, 'episode'))
                    $this->SetValueInteger('episode', $ret->episode);
                else
                    $this->SetValueInteger('episode', -1);

                if (property_exists($ret, 'genre'))
                {
                    if (is_array($ret->genre))
                        $this->SetValueString('genre', implode(', ', $ret->genre));
                    else
                        $this->SetValueString('genre', $ret->genre);
                }
                else
                    $this->SetValueString('genre', "");

                if (property_exists($ret, 'runtime'))
                    $this->SetValueString('duration', $this->ConvertTime($ret->runtime));
                else
                    $this->SetValueString('duration', "");

                if (property_exists($ret, 'year'))
                    $this->SetValueInteger('year', $ret->year);
                else
                    $this->SetValueInteger('year', 0);

                if (property_exists($ret, 'plot'))
                    $this->SetValueString('plot', $ret->plot);
                else
                    $this->SetValueString('plot', "");

                switch ($this->ReadPropertyString('CoverTyp'))
                {
                    case"poster":
                        if (property_exists($ret, 'art'))
                        {
                            if (property_exists($ret->art, 'tvshow.poster'))
                            {
                                if ($ret->art->{'tvshow.poster'} <> "")
                                {
                                    $this->SetCover($ret->art->{'tvshow.poster'});
                                    break;
                                }
                            }
                            if (property_exists($ret->art, 'poster'))
                            {
                                if ($ret->art->{'poster'} <> "")
                                {
                                    $this->SetCover($ret->art->{'poster'});
                                    break;
                                }
                            }
                        }
                        if (property_exists($ret, 'poster'))
                        {
                            if ($ret->poster <> "")
                            {
                                $this->SetCover($ret->poster);
                                break;
                            }
                        }
                        if (property_exists($ret, 'art'))
                        {
                            if (property_exists($ret->art, 'tvshow.banner'))
                            {
                                if ($ret->art->{'tvshow.banner'} <> "")
                                {
                                    $this->SetCover($ret->art->{'tvshow.banner'});
                                    break;
                                }
                            }
                        }
                        if (property_exists($ret, 'banner'))
                        {
                            if ($ret->banner <> "")
                            {
                                $this->SetCover($ret->banner);
                                break;
                            }
                        }
                        if (property_exists($ret, 'art'))
                        {
                            if (property_exists($ret->art, 'thumb'))
                                if ($ret->art->thumb <> "")
                                {
                                    $this->SetCover($ret->art->thumb);
                                    break;
                                }
                        }
                        if (property_exists($ret, 'thumbnail'))
                        {
                            if ($ret->thumbnail <> "")
                            {
                                $this->SetCover($ret->thumbnail);
                                break;
                            }
                        }
                        $this->SetCover("");

                        break;
                    case"banner":
                        if (property_exists($ret, 'art'))
                        {
                            if (property_exists($ret->art, 'tvshow.banner'))
                            {
                                if ($ret->art->{'tvshow.banner'} <> "")
                                {
                                    $this->SetCover($ret->art->{'tvshow.banner'});
                                    break;
                                }
                            }
                        }
                        if (property_exists($ret, 'banner'))
                        {
                            if ($ret->banner <> "")
                            {
                                $this->SetCover($ret->banner);
                                break;
                            }
                        }
                        if (property_exists($ret, 'art'))
                        {
                            if (property_exists($ret->art, 'tvshow.poster'))
                            {
                                if ($ret->art->{'tvshow.poster'} <> "")
                                {
                                    $this->SetCover($ret->art->{'tvshow.poster'});
                                    break;
                                }
                            }
                            if (property_exists($ret->art, 'poster'))
                            {
                                if ($ret->art->{'poster'} <> "")
                                {
                                    $this->SetCover($ret->art->{'poster'});
                                    break;
                                }
                            }
                        }
                        if (property_exists($ret, 'poster'))
                        {
                            if ($ret->poster <> "")
                            {
                                $this->SetCover($ret->poster);
                                break;
                            }
                        }
                    default:
                        if (property_exists($ret, 'art'))
                        {
                            if (property_exists($ret->art, 'thumb'))
                                if ($ret->art->thumb <> "")
                                {
                                    $this->SetCover($ret->art->thumb);
                                    break;
                                }
                        }
                        if (property_exists($ret, 'thumbnail'))
                        {
                            if ($ret->thumbnail <> "")
                            {
                                $this->SetCover($ret->thumbnail);
                                break;
                            }
                        }
                        $this->SetCover("");

                        break;
                }
                break;

            /*      EPISODE

              "streamdetails":
              {
              "audio":[{"channels":6,"codec":"ac3","language":""}],
              "subtitle":[{"language":""}],
              "video":[{"aspect":1.7777800559997559,"codec":"h264","duration":3421,"height":720,"stereomode":"","width":1280}]
              }
             */
            /*      MOVIE
              ["streamdetails"]=>
              object(stdClass)#13 (3) {
              ["audio"]=>
              array(1) {
              [0]=>
              object(stdClass)#14 (3) {
              ["channels"]=>
              int(2)
              ["codec"]=>
              string(3) "mp3"
              ["language"]=>
              string(0) ""
              }
              }
              ["subtitle"]=>
              array(0) {
              }
              ["video"]=>
              array(1) {
              [0]=>
              object(stdClass)#15 (6) {
              ["aspect"]=>
              float(1.8181799650192)
              ["codec"]=>
              string(4) "xvid"
              ["duration"]=>
              int(6462)
              ["height"]=>
              int(352)
              ["stereomode"]=>
              string(0) ""
              ["width"]=>
              int(640)
              }
              }
              }
             */
        }
    }

    /**
     * IPS-Instanz-Funktion 'KODIPLAYER_GetItem'.
     * Holt sich die Daten des aktuellen wiedergegebenen Items, und gibt die Array zurück.
     * 
     * @access public
     * @return array|null Das Array mit den Eigenschaften des Item, im Fehlerfall null
     */
    public function GetItem()
    {
        $this->Init();
        $KodiData = new Kodi_RPC_Data(self::$Namespace, 'GetItem', array('playerid' => $this->PlayerId, 'properties' => self::$ItemList));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return null;
        return $ret->item;
    }

    /**
     * IPS-Instanz-Funktion 'KODIPLAYER_Play'.
     * Startes die Wiedergabe des aktuellen Items.
     * 
     * @access public
     * @return boolean True bei Erfolg, sonst false.
     */
    public function Play()
    {
        $this->Init();
        if (!$this->isActive)
        {
            trigger_error('Player not active', E_USER_NOTICE);
            return false;
        }
        $KodiData = new Kodi_RPC_Data(self::$Namespace, 'PlayPause', array("playerid" => $this->PlayerId, "play" => true));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        if ($ret->speed === 1)
        {
            $this->SetValueInteger("Status", 2);
            return true;
        }
        else
        {
            trigger_error('Error on send play.', E_USER_NOTICE);
        }

        return false;
    }

    /**
     * IPS-Instanz-Funktion 'KODIPLAYER_Pause'.
     * Pausiert die Wiedergabe des aktuellen Items.
     * 
     * @access public
     * @return boolean True bei Erfolg, sonst false.
     */
    public function Pause()
    {
        $this->Init();
        if (!$this->isActive)
        {
            trigger_error('Player not active', E_USER_NOTICE);
            return false;
        }
        $KodiData = new Kodi_RPC_Data(self::$Namespace, 'PlayPause', array("playerid" => $this->PlayerId, "play" => false));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        if ($ret->speed === 0)
        {
            $this->SetValueInteger("Status", 3);
            return true;
        }
        else
        {
            trigger_error('Error on send pause.', E_USER_NOTICE);
        }

        return false;
    }

    /**
     * IPS-Instanz-Funktion 'KODIPLAYER_Stop'.
     * Stoppt die Wiedergabe des aktuellen Items.
     * 
     * @access public
     * @return boolean True bei Erfolg, sonst false.
     */
    public function Stop()
    {
        $this->Init();
        if (!$this->isActive)
        {
            trigger_error('Player not active', E_USER_NOTICE);
            return false;
        }
        $KodiData = new Kodi_RPC_Data(self::$Namespace, 'Stop', array("playerid" => $this->PlayerId));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        if ($ret === "OK")
        {
            $this->SetValueInteger("Status", 1);
            return true;
        }
        else
        {
            trigger_error('Error on send stop.', E_USER_NOTICE);
        }
        return false;
    }

    /**
     * IPS-Instanz-Funktion 'KODIPLAYER_Next'.
     * Springt zum nächsten Item in der Wiedergabeliste.
     * 
     * @access public
     * @return boolean True bei Erfolg, sonst false.
     */
    public function Next()
    {
        $this->Init();
        if (!$this->isActive)
        {
            trigger_error('Player not active', E_USER_NOTICE);
            return false;
        }
        $KodiData = new Kodi_RPC_Data(self::$Namespace, 'GoTo', array("playerid" => $this->PlayerId, "to" => "next"));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        if ($ret === "OK")
            return true;
        trigger_error('Error on send next.', E_USER_NOTICE);
        return false;
    }

    /**
     * IPS-Instanz-Funktion 'KODIPLAYER_Previous'.
     * Springt zum vorherigen Item in der Wiedergabeliste.
     * 
     * @access public
     * @return boolean True bei Erfolg, sonst false.
     */
    public function Previous()
    {
        $this->Init();
        if (!$this->isActive)
        {
            trigger_error('Player not active', E_USER_NOTICE);
            return false;
        }
        $KodiData = new Kodi_RPC_Data(self::$Namespace, 'GoTo', array("playerid" => $this->PlayerId, "to" => "previous"));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        if ($ret === "OK")
            return true;
        trigger_error('Error on send previous.', E_USER_NOTICE);
        return false;
    }

    /**
     * IPS-Instanz-Funktion 'KODIPLAYER_GoToTrack'.
     * Springt auf ein bestimmtes Item in der Wiedergabeliste.
     * 
     * @access public
     * @param integer $Value Index in der Wiedergabeliste.
     * @return boolean True bei Erfolg, sonst false.
     */
    public function GoToTrack(integer $Value)
    {
        if (!is_int($Value))
        {
            trigger_error('Value must be integer', E_USER_NOTICE);
            return false;
        }
        $this->Init();
        if (!$this->isActive)
        {
            trigger_error('Player not active', E_USER_NOTICE);
            return false;
        }

        $KodiData = new Kodi_RPC_Data(self::$Namespace, 'GoTo', array("playerid" => $this->PlayerId, "to" => $Value + 1));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        if ($ret === "OK")
            return true;
        trigger_error('Error on goto track.', E_USER_NOTICE);
        return false;
    }

    /**
     * IPS-Instanz-Funktion 'KODIPLAYER_SetShuffle'.
     * Setzt den Zufallsmodus.
     * 
     * @access public
     * @param boolean $Value True für Zufallswiedergabe aktiv, false für deaktiv.
     * @return boolean True bei Erfolg, sonst false.
     */
    public function SetShuffle(boolean $Value)
    {
        if (!is_bool($Value))
        {
            trigger_error('Value must be boolean', E_USER_NOTICE);
            return false;
        }
        $this->Init();
        $KodiData = new Kodi_RPC_Data(self::$Namespace, 'SetShuffle', array("playerid" => $this->PlayerId, "shuffle" => $Value));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        if ($ret === "OK")
        {
            $this->SetValueBoolean("shuffled", $Value);
            return true;
        }
        else
        {
            trigger_error('Error on set shuffle.', E_USER_NOTICE);
        }

        return false;
    }

    /**
     * IPS-Instanz-Funktion 'KODIPLAYER_SetRepeat'.
     * Setzten den Wiederholungsmodus.
     * 
     * @access public
     * @param integer $Value Modus der Wiederholung.
     *   enum[0=aus, 1=Titel, 2=Alle]
     * @return boolean True bei Erfolg, sonst false.
     */
    public function SetRepeat(integer $Value)
    {
        if (!is_int($Value))
        {
            trigger_error('Value must be integer', E_USER_NOTICE);
            return false;
        }
        if (($Value < 0) or ( $Value > 2))
        {
            trigger_error('Value must be between 0 and 2', E_USER_NOTICE);
            return false;
        }
        $this->Init();
        $repeat = array("off", "one", "all");
        $KodiData = new Kodi_RPC_Data(self::$Namespace, 'SetRepeat', array("playerid" => $this->PlayerId, "repeat" => $repeat[$Value]));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        if ($ret === "OK")
        {
            $this->SetValueInteger("repeat", $Value);
            return true;
        }
        else
        {
            trigger_error('Error on set repeat.', E_USER_NOTICE);
        }

        return false;
    }

    /**
     * IPS-Instanz-Funktion 'KODIPLAYER_SetPartymode'.
     * Setzt den Partymodus.
     * 
     * @access public
     * @param boolean $Value True für Partymodus aktiv, false für deaktiv.
     * @return boolean True bei Erfolg, sonst false.
     */
    public function SetPartymode(boolean $Value)
    {
        if (!is_bool($Value))
        {
            trigger_error('Value must be boolean', E_USER_NOTICE);
            return false;
        }
        $this->Init();
        $KodiData = new Kodi_RPC_Data(self::$Namespace, 'SetPartymode', array("playerid" => $this->PlayerId, "partymode" => $Value));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        if ($ret === "OK")
        {
            $this->SetValueBoolean("partymode", $Value);
            return true;
        }
        else
        {
            trigger_error('Error on set partymode.', E_USER_NOTICE);
        }

        return false;
    }

    /**
     * IPS-Instanz-Funktion 'KODIPLAYER_SetSpeed'.
     * Setzten die Abspielgeschwindigkeit.
     * 
     * @access public
     * @param integer $Value Geschwindigkeit.
     *   enum[-32, -16, -8, -4, -2, 0, 1, 2, 4, 8, 16, 32]
     * @return boolean True bei Erfolg, sonst false.
     */
    public function SetSpeed(integer $Value)
    {
        if (!is_int($Value))
        {
            trigger_error('Value must be integer', E_USER_NOTICE);
            return false;
        }
        $this->Init();
        if (!$this->isActive)
        {
            trigger_error('Player not active', E_USER_NOTICE);
            return false;
        }

        if (!in_array($Value, array(-32, -16, -8, -4, -2, 0, 1, 2, 4, 8, 16, 32)))
        {
            trigger_error('Invalid Value for speed.', E_USER_NOTICE);
            return false;
        }
        $KodiData = new Kodi_RPC_Data(self::$Namespace, 'SetSpeed', array("playerid" => $this->PlayerId, "speed" => $Value));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        if ((int) $ret->speed == $Value)
        {
            $this->SetValueInteger("speed", $Value);
            return true;
        }
        else
        {
            trigger_error('Error on set speed.', E_USER_NOTICE);
        }
        return false;
    }

    /**
     * IPS-Instanz-Funktion 'KODIPLAYER_SetPosition'.
     * Springt auf eine absolute Position innerhalb einer Wiedergabe.
     * 
     * @access public
     * @param integer $Value Position in....
     * @return boolean True bei Erfolg, sonst false.
     */
    public function SetPosition(integer $Value)
    {
        if (!is_int($Value))
        {
            trigger_error('Value must be integer', E_USER_NOTICE);
            return false;
        }
        $this->Init();
        if (!$this->isActive)
        {
            trigger_error('Player not active', E_USER_NOTICE);
            return false;
        }
        //TODO
        /*        if ($this->PlayerId <> self::Audio)
          {
          trigger_error('Not supported', E_USER_NOTICE);
          return false;
          } */
    }

    private function Load(string $ItemTyp, string $ItemValue, $Ext = array())
    {
        $this->Init();
        $KodiData = new Kodi_RPC_Data(self::$Namespace, 'Open', array_merge(array("item" => array($ItemTyp => $ItemValue)), $Ext));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        if ($ret === "OK")
            return true;
        trigger_error('Error on load ' . $ItemTyp . '.', E_USER_NOTICE);
        return false;
    }

    public function LoadAlbum(integer $AlbumId)
    {
        if (!is_int($AlbumId))
        {
            trigger_error('AlbumId must be integer', E_USER_NOTICE);
            return false;
        }
        return $this->Load("albumid", $AlbumId);
    }

    public function LoadArtist(integer $ArtistId)
    {
        if (!is_int($ArtistId))
        {
            trigger_error('ArtistId must be integer', E_USER_NOTICE);
            return false;
        }
        return $this->Load("artistid", $ArtistId);
    }

    public function LoadDirectory(integer $Directory)
    {
        if (!is_string($Directory))
        {
            trigger_error('Directory must be string', E_USER_NOTICE);
            return false;
        }
        return $this->Load("directory", $Directory);
    }

    public function LoadDirectoryRecursive(integer $Directory)
    {
        if (!is_int($Directory))
        {
            trigger_error('Directory must be string', E_USER_NOTICE);
            return false;
        }
        return $this->Load("Directory", $Directory, array("recursive" => true));
    }

    public function LoadEpisode(integer $EpisodeId)
    {
        if (!is_int($EpisodeId))
        {
            trigger_error('EpisodeId must be integer', E_USER_NOTICE);
            return false;
        }
        return $this->Load("episodeid", $EpisodeId);
    }

    public function LoadFile(integer $File)
    {
        if (!is_string($File))
        {
            trigger_error('File must be string', E_USER_NOTICE);
            return false;
        }
        return $this->Load("file", $File);
    }

    public function LoadGenre(integer $GenreId)
    {
        if (!is_int($GenreId))
        {
            trigger_error('GenreId must be integer', E_USER_NOTICE);
            return false;
        }
        return $this->Load("genreid", $GenreId);
    }

    public function LoadMovie(integer $MovieId)
    {
        if (!is_int($MovieId))
        {
            trigger_error('MovieId must be integer', E_USER_NOTICE);
            return false;
        }
        return $this->Load("movieid", $MovieId);
    }

    public function LoadMusicvideo(integer $MusicvideoId)
    {
        if (!is_int($MusicvideoId))
        {
            trigger_error('MusicvideoId must be integer', E_USER_NOTICE);
            return false;
        }
        return $this->Load("musicvideoid", $MusicvideoId);
    }

    public function LoadPlaylist()
    {
        $this->Init();
        return $this->Load("playlistid", $this->PlayerId);
    }

    public function LoadSong(integer $SongId)
    {
        if (!is_int($SongId))
        {
            trigger_error('SongId must be integer', E_USER_NOTICE);
            return false;
        }
        return $this->Load("songid", $SongId);
    }

    /**
     * IPS-Instanz-Funktion 'KODIPLAYER_RequestState'. Frage eine oder mehrere Properties ab.
     *
     * @access public
     * @param string $Ident Enthält den Names des "properties" welches angefordert werden soll.
     * @return boolean true bei erfolgreicher Ausführung, sonst false.
     */
    /*
      public function RequestState(string $Ident)
      {
      return parent::RequestState($Ident);
      }
     */

    /**
     * Liefert den Parent der Instanz.
     * 
     * @return integer|boolean InstanzID des Parent, false wenn kein Parent vorhanden.
     */
    protected function GetParent()
    {
        $instance = IPS_GetInstance($this->InstanceID);
        return ($instance['ConnectionID'] > 0) ? $instance['ConnectionID'] : false;
    }

}

/** @} */
?>