<?

require_once(__DIR__ . "/../KodiClass.php");  // diverse Klassen

class KodiDeviceFiles extends KodiBase
{

    static $Namespace = 'Files';
    static $Properties = array(
    );
    static $ItemListFull = array(
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
        "size",
        "lastmodified",
        "mimetype");
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

    public function Create()
    {
        parent::Create();
    }

    public function ApplyChanges()
    {
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
    }

################## PRIVATE     

    protected function Decode($Method, $KodiPayload)
    {
        return;
//        foreach ($KodiPayload as $param => $value)
//        {
//            switch ($param)
//            {
//                case "mute":
//                case "muted":
//                    $this->SetValueBoolean("mute", $value);
//                    break;
//                case "volume":
//                    $this->SetValueInteger("volume", $value);
//                    break;
//                case "name":
//                    $this->SetValueString("name", $value);
//                    break;
//                case "version":
//                    $this->SetValueString("version", $value->major . '.' . $value->minor);
//                    break;
//            }
//        }
    }

################## ActionHandler

    public function RequestAction($Ident, $Value)
    {
        switch ($Ident)
        {
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
    /*    public function RawSend(string $Namespace, string $Method, $Params)
      {
      return  parent::RawSend($Namespace, $Method, $Params);
      } */

    public function GetSources(string $Value)
    {
        if (!is_string($Value))
        {
            trigger_error('Value must be string', E_USER_NOTICE);
            return false;
        }

        $Value = strtolower($Value);
        if (!in_array($Value, array("video", "music", "pictures", "files", "programs")))
        {
            trigger_error('Value must be "video", "music", "pictures", "files" or "programs".', E_USER_NOTICE);
            return false;
        }

        $KodiData = new Kodi_RPC_Data(self::$Namespace, 'GetSources', array("media" => $Value));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;

        $Sources = array();
        foreach ($ret->sources as $source)
        {
            $Sources[$source->label] = $source->file;
        }

        if (count($Sources) == 0)
            return false;

        return $Sources;
    }

    public function GetFileDetails(string $File, string $Media)
    {
        if (!is_string($Value))
        {
            trigger_error('Value must be string', E_USER_NOTICE);
            return false;
        }
        if (!is_string($Media))
        {
            trigger_error('Media must be string', E_USER_NOTICE);
            return false;
        }

        $Media = strtolower($Media);
        if (!in_array($Media, array("video", "music", "pictures", "files", "programs")))
        {
            trigger_error('Media must be "video", "music", "pictures", "files" or "programs".', E_USER_NOTICE);
            return false;
        }

        $KodiData = new Kodi_RPC_Data(self::$Namespace, 'GetFileDetails', array("file" => $File, "media" => $Media, "properties" => static::$ItemListFull));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;

        return json_decode(json_encode($ret), true);
    }

    public function GetDirectory(string $Directory)
    {
        
    }

    public function GetDirectoryDetails(string $Directory, string $Media)
    {
        
    }

//
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
        return;  //parent::RequestState($Ident);
    }

    /*
      public function Pause()
      {

      }

      public function Sleep(integer $Value)
      {

      }

      public function Stop()
      {

      }

      public function Shutdown()
      {

      }
     */
################## Datapoints

    public function ReceiveData($JSONString)
    {
        return parent::ReceiveData($JSONString);
    }

    protected function Send(Kodi_RPC_Data $KodiData)
    {
        return parent::Send($KodiData);
    }

    protected function SendDataToParent($Data)
    {
        return parent::SendDataToParent($Data);
    }

}

?>