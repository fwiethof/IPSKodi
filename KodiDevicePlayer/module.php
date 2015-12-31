<?

require_once(__DIR__ . "/../KodiClass.php");  // diverse Klassen

class KodiDevicePlayer extends KodiBase
{

    static $Namespace = 'Player';
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
    private $PlayerId = null;

    static $Playertype= array(
        "song" => 0,
        "episode" => 1,
        "movie" => 1,
        "pictures" => 2
    );
    public function Create()
    {
        parent::Create();
        $this->RegisterPropertyInteger('PlayerID', 0);
        $this->RegisterPropertyInteger('CoverSize', 300);
    }

    public function ApplyChanges()
    {
        $this->RegisterProfileIntegerEx("Status.Kodi", "Information", "", "", Array(
            //Array(0, "Prev", "", -1),
            Array(1, "Stop", "", -1),
            Array(2, "Play", "", -1),
            Array(3, "Pause", "", -1)
                //Array(4, "Next", "", -1)
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
        $this->RegisterProfileInteger("AudioTracks." . $this->InstanceID . ".Kodi", "", "", "", 1, 1, 1);

        $this->RegisterVariableInteger("Status", "Status", "Status.Kodi", 3);
        $this->EnableAction("Status");
        $this->RegisterVariableInteger("speed", "Geschwindigkeit", "Speed.Kodi", 10);
        $this->RegisterVariableBoolean("repeat", "Wiederholen", "~Switch", 11);
        $this->RegisterVariableBoolean("shuffled", "Zufall", "~Switch", 12);

        $this->RegisterVariableString("label", "Titel", "", 15);
        $this->RegisterVariableString("type", "Typ", "", 16);
        $this->RegisterVariableString("genre", "Genre", "", 17);
        $this->RegisterVariableString("artist", "Artist", "", 18);
        $this->RegisterVariableString("plot", "Handlung", "~TextBox", 19);

        $this->RegisterVariableString("totaltime", "Dauer", "", 24);
        $this->RegisterVariableString("time", "Spielzeit", "", 25);
        $this->RegisterVariableInteger("percentage", "Position", "Intensity.Kodi", 26);
        $this->RegisterVariableInteger("audioindex", "Aktueller Audiotrack", "AudioTracks." . $this->InstanceID . ".Kodi", 30);
        $this->RegisterVariableString("audiolanguage", "Sprache", "", 31);
        $this->RegisterVariableInteger("audiochannels", "Audiokanäle", "", 32);
        $this->RegisterVariableString("audiocodec", "Audio Codec", "", 23);
        $this->RegisterVariableInteger("audiobitrate", "Audio Bitrate", "", 34);

        $this->RegisterVariableInteger("audiostreams", "Anzahl Audiotracks", "", 35);
        $this->RegisterVariableBoolean("subtitleenabled", "Untertitel aktiv", "~Switch", 40);
        $this->RegisterVariableInteger("subtitles", "Anzahl Untertitel", "", 41);




//        $this->RegisterProfileIntegerEx("Action.Kodi", "", "", "", Array(
//            Array(0, "Ausführen", "", -1)
//        ));
//        $this->RegisterVariableString("name", "Name", "", 0);
//        $this->RegisterVariableString("version", "Version", "", 1);
//        $this->RegisterVariableInteger("quit", "Kodi beenden", "Action.Kodi", 2);
//        $this->EnableAction("quit");
//        $this->RegisterVariableBoolean("mute", "Mute", "~Switch", 3);
//        $this->EnableAction("mute");
//        $this->RegisterVariableInteger("volume", "Volume", "~Intensity.100", 4);
//        $this->EnableAction("volume");
        //Never delete this line!
        parent::ApplyChanges();
        $this->RegisterTimer('PlayerStatus', 0, 'KODIPLAYER_RequestState($_IPS[\'TARGET\'],"PARTIAL");');
    }

################## PRIVATE     

    private function Init()
    {
        if (is_null($this->PlayerId))
            $this->PlayerId = $this->ReadPropertyInteger('PlayerID');
    }

    protected function RequestProperties(array $Params)
    {
        $this->Init();
        $Params = array_merge($Params, array("playerid" => $this->PlayerId));
        parent::RequestProperties($Params);
    }

    protected function Decode($Method, $KodiPayload)
    {
        $this->Init();
        if (property_exists($KodiPayload, 'player')
                and ( $KodiPayload->player->playerid <> $this->PlayerId))
            return false;
        if (property_exists($KodiPayload, 'item')
                and ( self::$Playertype[(string)$KodiPayload->item->type] <> $this->PlayerId))
            return false;
        
        switch ($Method)
        {
            case 'GetProperties':
            case 'OnPropertyChanged':
                foreach ($KodiPayload as $param => $value)
                {
                    switch ($param)
                    {
                        case "percentage":
                            $this->SetValueInteger('percentage', (int) $value);
                            break;
                        case "totaltime":
                        case "time":
                            $this->SetValueString($param, $this->ConvertTime($value));
                            break;
                        case "audiostreams":
                            $this->SetValueInteger($param, count($value));
                            break;
                        case "currentaudiostream":
                            if (is_object($value))
                            {
                                $this->SetValueInteger('audiobitrate', (int) $value->bitrate);
                                $this->SetValueInteger('audiochannels', (int) $value->channels);
                                $this->SetValueInteger('audioindex', (int) $value->index);
                                $this->SetValueString('audiolanguage', (string) $value->language);
                                $this->SetValueString('audiocodec', (string) $value->name);
                            }
                            else
                            {
                                $this->SetValueInteger('audiobitrate', 0);
                                $this->SetValueInteger('audiochannels', 0);
                                $this->SetValueInteger('audioindex', 0);
                                $this->SetValueString('audiolanguage', "");
                                $this->SetValueString('audiocodec', "");
                            }
                            break;
                        /*    {"canrotate":false,"canzoom":false,
                          "currentsubtitle":null,
                          "live":false,"partymode":false,"playlistid":1,
                          "position":-1,
                          "subtitleenabled":false,"subtitles":[],
                         */
                        case "subtitleenabled":
                            $this->SetValueBoolean('subtitleenabled', $value);
                            break;
                        case "subtitles":
                            $this->SetValueInteger($param, count($value));
                            break;
                        case "currentsubtitle":
                            if (is_object($value))
                            {
                                /*                                $this->SetValueInteger('audiobitrate', 0);
                                  $this->SetValueInteger('audiochannels', 0);
                                  $this->SetValueInteger('audioindex', 0);
                                  $this->SetValueString('audiolanguage', "");
                                  $this->SetValueString('audiocodec', ""); */
//                                $this->DisableAction('subtitleenabled');
                            }
                            else
                            {
                                /*                                $this->SetValueInteger('audiobitrate', (int) $value->bitrate);
                                  $this->SetValueInteger('audiochannels', (int) $value->channels);
                                  $this->SetValueInteger('audioindex', (int) $value->index);
                                  $this->SetValueString('audiolanguage', (string) $value->language);
                                  $this->SetValueString('audiocodec', (string) $value->name); */
                                //                              $this->EnableAction('subtitleenabled');
                            }
                            break;
                        case "canseek":
                            if ($value)
                                $this->EnableAction('percentage');
                            else
                                $this->DisableAction('percentage');
                            break;
                        case "canshuffle":
                            if ($value)
                                $this->EnableAction('shuffled');
                            else
                                $this->DisableAction('shuffled');
                            break;
                        case "canrepeat":
                            if ($value)
                                $this->EnableAction('repeat');
                            else
                                $this->DisableAction('repeat');
                            break;
                        case "canchangespeed":
                            if ($value)
                                $this->EnableAction('speed');
                            else
                                $this->DisableAction('speed');
                            break;
                        case "repeat": //off
                            if ($value == "off")
                                $this->SetValueBoolean('repeat', false);
                            else
                                $this->SetValueBoolean('repeat', true);

                            break;
                        case "shuffled":
                            $this->SetValueBoolean('shuffled', $value);
                            break;
                        case "speed":
                            $this->SetValueInteger('speed', (int) $value);
                            break;
                        default:
                            IPS_LogMessage($param, print_r($value, true));
                            break;
                    }
                }
                break;
            case 'OnStop':
                $this->SetTimerInterval('PlayerStatus', 0);
                $this->SetValueInteger('Status', 1);
                $this->SetValueString('totaltime', '');
                $this->SetValueString('time', '');
                $this->SetValueInteger('percentage', 0);
                IPS_RunScriptText('<? KODIPLAYER_RequestState(' . $this->InstanceID . ',"ALL");');
                IPS_RunScriptText('<? KODIPLAYER_GetItemInternal(' . $this->InstanceID . ');');

                break;
            case 'OnPlay':
                $this->SetValueInteger('Status', 2);
                IPS_RunScriptText('<? KODIPLAYER_RequestState(' . $this->InstanceID . ',"ALL");');
                IPS_RunScriptText('<? KODIPLAYER_GetItemInternal(' . $this->InstanceID . ');');
                $this->SetTimerInterval('PlayerStatus', 2);
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
                IPS_LogMessage($Method, print_r($KodiPayload, true));
                break;
        }
    }

    private function SetCover($file)
    {
//        $Ext = pathinfo($file, PATHINFO_EXTENSION);
        $CoverID = @IPS_GetObjectIDByIdent('CoverIMG', $this->InstanceID);
        $filename = "media" . DIRECTORY_SEPARATOR . "Cover_" . $this->InstanceID . ".png";
        $Size = $this->ReadPropertyString("CoverSize");
        if ($CoverID === false)
        {
            $CoverID = IPS_CreateMedia(1);
            IPS_SetParent($CoverID, $this->InstanceID);
            IPS_SetIdent($CoverID, 'CoverIMG');
            IPS_SetName($CoverID, 'Cover');
            IPS_SetPosition($CoverID, 27);
            IPS_SetMediaCached($CoverID, true);
            IPS_SetMediaFile($CoverID, $filename, False);
        }

        if ($file == "")
            $CoverRAW = FALSE;
        else
            $CoverRAW = @Sys_GetURLContent($file);

        if (!($CoverRAW === false))
        {
            $image = imagecreatefromstring($CoverRAW);
            if (!($image === false))
            {
                $image = imagescale($image, $Size);
                if (imagepng($image, IPS_GetKernelDir() . $filename) === true)
                {
                    IPS_SendMediaEvent($CoverID);
                    return;
                }
            }
        }
        $CoverRAW = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "nocover.png");

        IPS_SetMediaContent($CoverID, base64_encode($CoverRAW));
        return;
    }

################## ActionHandler

    public function RequestAction($Ident, $Value)
    {
        switch ($Ident)
        {
            case "Status":
                switch ($Value)
                {
                    /*                    case 0: //Prev
                      //$this->PreviousButton();
                      $result = $this->PreviousTrack();
                      break; */
                    case 1: //Stop
                        $result = $this->Stop();
                        break;
                    case 2: //Play
                        $result = $this->Play();
                        break;
                    case 3: //Pause
                        $result = $this->Pause();
                        break;
                    /*                    case 4: //Next
                      //$this->NextButton();
                      $result = $this->NextTrack();
                      break; */
                }
                break;
//            case "mute":
//                return $this->Mute($Value);
//            case "volume":
//                return $this->Volume($Value);
//            case "quit":
//                return $this->Quit();
//            default:
//                return trigger_error('Invalid Ident.', E_USER_NOTICE);
        }
    }

################## PUBLIC
    /**
     * This function will be available automatically after the module is imported with the module control.
     * Using the custom prefix this function will be callable from PHP and JSON-RPC through:
     */

    public function RawSend(string $Namespace, string $Method, $Params)
    {
        return parent::RawSend($Namespace, $Method, $Params);
    }

    public function GetItemInternal()
    {
        $ret = $this->GetItem();
        if (is_null($ret))
            return null;
        $this->SetValueString('label', $ret->item->label);
        $this->SetValueString('type', $ret->item->type);
        $CoverURL = "";
        if ($ret->item->thumbnail <> "")
        {
            $CoverURL = rawurldecode(substr($ret->item->thumbnail, 8, 1));
        }
        $this->SetCover($CoverURL);

        if (count($ret->item->artist) > 0)
        {
            $this->SetValueString('artist', implode(', ', $ret->item->artist));
        }
        else
        {
            $this->SetValueString('artist', "");
        }
        if (count($ret->item->genre) > 0)
        {
            $this->SetValueString('genre', implode(', ', $ret->item->artist));
        }
        else
        {
            $this->SetValueString('genre', "");
        }
        $this->SetValueString('plot', $ret->item->plot);

        /*
          album
          ["art"]=>
          object(stdClass)#8 (2) {
          ["fanart"]=>
          string(89) "image://http%3a%2f%2fimage.tmdb.org%2ft%2fp%2foriginal%2fpugQ0pfT7bz9MFf6EFh2P3fBjkp.jpg/"
          ["poster"]=>
          string(198) "image://https%3a%2f%2fgfx.videobuster.de%2farchive%2fresized%2fw700%2f2008%2f02%2fimage%2fjpeg%2ff136aea8fcf90e95f8ad0a7b01be895d.jpg%3ftitle%3deragon%26k%3dDVD%2bonline%2bleihen%2bdownload%2bcover/"
          }
          ["artist"]=>
          array(0) {
          }

          }
          episode
          ["fanart"]=>
          string(89) "image://http%3a%2f%2fimage.tmdb.org%2ft%2fp%2foriginal%2fpugQ0pfT7bz9MFf6EFh2P3fBjkp.jpg/"
          ["file"]=>
          string(80) "smb://WHS/Videos/Filme/Eragon.AC3.BDRip/Eragon.2006.German.AC3.BDRip.XviD-SG.avi"
          ["genre"]=>
          array(1) {
          [0]=>
          string(7) "Fantasy"
          }
          plot
          ["video"]=>
          array(1) {
          [0]=>
          object(stdClass)#15 (6) {
          ["aspect"]=>
          float(2.3684198856354)
          ["codec"]=>
          string(4) "xvid"
          ["duration"]=>
          int(5986)
          ["height"]=>
          int(304)
          ["stereomode"]=>
          string(0) ""
          ["width"]=>
          int(720)
          }
          thumbnail */
    }

    public function GetItem()
    {
        $this->Init();
        $KodiData = new Kodi_RPC_Data(self::$Namespace, 'GetItem', array('playerid' => $this->PlayerId, 'properties' => self::$ItemList));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return null;
        return $ret;

//        var_dump($ret);
    }

    public function Play()
    {
        $this->Init();

        $KodiData = new Kodi_RPC_Data(self::$Namespace, 'PlayPause', array("playerid" => $this->PlayerId, "play" => true));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        if ($ret->speed === 1)
        {
            $this->SetValueInteger("Status", 2);
            return true;
        }
        return false;
    }

    public function Pause()
    {
        $this->Init();
        $KodiData = new Kodi_RPC_Data(self::$Namespace, 'PlayPause', array("playerid" => $this->PlayerId, "play" => false));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        if ($ret->speed === 0)
        {
            $this->SetValueInteger("Status", 3);
            return true;
        }
        return false;
    }

    public function Stop()
    {
        $this->Init();
        $KodiData = new Kodi_RPC_Data(self::$Namespace, 'Stop', array("playerid" => $this->PlayerId));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        if ($ret === "OK")
        {
            $this->SetValueInteger("Status", 1);
            return true;
        }
        return false;
    }

//    public function Volume(integer $Value)
//    {
//        if (!is_int($Value))
//        {
//            trigger_error('Value must be integer', E_USER_NOTICE);
//            return false;
//        }
////        $KodiData = new Kodi_RPC_Data(self::$Namespace, 'SetVolume', array("volume" => $Value));
//        $KodiData = new Kodi_RPC_Data(self::$Namespace);
//        $KodiData->SetVolume(array("volume" => $Value));
//        $ret = $this->Send($KodiData);
//        if (is_null($ret))
//            return false;
//        $this->SetValueInteger("volume", $ret);
//        return $ret['volume'] === $Value;
//    }
//
//    public function Quit()
//    {
//        $KodiData = new Kodi_RPC_Data(self::$Namespace, 'Quit');
//        $ret = $this->Send($KodiData);
//        if (is_null($ret))
//            return false;
//        return true;
//    }

    public function RequestState(string $Ident)
    {
        return parent::RequestState($Ident);
    }

    /*
      public function Pause()
      {

      }

      public function Stop()
      {

      }

     */
################## Datapoints

    public function ReceiveData($JSONString)
    {
        return parent::ReceiveData($JSONString);
    }

    /*
      protected function Send(Kodi_RPC_Data $KodiData)
      {
      return parent::Send($KodiData);
      }

      protected function SendDataToParent($Data)
      {
      return parent::SendDataToParent($Data);
      }
     */
}

?>