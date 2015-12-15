<?

require_once(__DIR__ . "/../KodiClass.php");  // diverse Klassen

class KodiDeviceInput extends KodiBase
{

    static $Namespace = 'Input';
    static $Properties = array();
    static $ExecuteAction = array("left",
        "right",
        "up",
        "down",
        "pageup",
        "pagedown",
        "select",
        "highlight",
        "parentdir",
        "parentfolder",
        "back",
        "previousmenu",
        "info",
        "pause",
        "stop",
        "skipnext",
        "skipprevious",
        "fullscreen",
        "aspectratio",
        "stepforward",
        "stepback",
        "bigstepforward",
        "bigstepback",
        "chapterorbigstepforward",
        "chapterorbigstepback",
        "osd",
        "showsubtitles",
        "nextsubtitle",
        "cyclesubtitle",
        "codecinfo",
        "nextpicture",
        "previouspicture",
        "zoomout",
        "zoomin",
        "playlist",
        "queue",
        "zoomnormal",
        "zoomlevel1",
        "zoomlevel2",
        "zoomlevel3",
        "zoomlevel4",
        "zoomlevel5",
        "zoomlevel6",
        "zoomlevel7",
        "zoomlevel8",
        "zoomlevel9",
        "nextcalibration",
        "resetcalibration",
        "analogmove",
        "analogmovex",
        "analogmovey",
        "rotate",
        "rotateccw",
        "close",
        "subtitledelayminus",
        "subtitledelay",
        "subtitledelayplus",
        "audiodelayminus",
        "audiodelay",
        "audiodelayplus",
        "subtitleshiftup",
        "subtitleshiftdown",
        "subtitlealign",
        "audionextlanguage",
        "verticalshiftup",
        "verticalshiftdown",
        "nextresolution",
        "audiotoggledigital",
        "number0",
        "number1",
        "number2",
        "number3",
        "number4",
        "number5",
        "number6",
        "number7",
        "number8",
        "number9",
        "osdleft",
        "osdright",
        "osdup",
        "osddown",
        "osdselect",
        "osdvalueplus",
        "osdvalueminus",
        "smallstepback",
        "fastforward",
        "rewind",
        "play",
        "playpause",
        "switchplayer",
        "delete",
        "copy",
        "move",
        "mplayerosd",
        "hidesubmenu",
        "screenshot",
        "rename",
        "togglewatched",
        "scanitem",
        "reloadkeymaps",
        "volumeup",
        "volumedown",
        "mute",
        "backspace",
        "scrollup",
        "scrolldown",
        "analogfastforward",
        "analogrewind",
        "moveitemup",
        "moveitemdown",
        "contextmenu",
        "shift",
        "symbols",
        "cursorleft",
        "cursorright",
        "showtime",
        "analogseekforward",
        "analogseekback",
        "showpreset",
        "nextpreset",
        "previouspreset",
        "lockpreset",
        "randompreset",
        "increasevisrating",
        "decreasevisrating",
        "showvideomenu",
        "enter",
        "increaserating",
        "decreaserating",
        "togglefullscreen",
        "nextscene",
        "previousscene",
        "nextletter",
        "prevletter",
        "jumpsms2",
        "jumpsms3",
        "jumpsms4",
        "jumpsms5",
        "jumpsms6",
        "jumpsms7",
        "jumpsms8",
        "jumpsms9",
        "filter",
        "filterclear",
        "filtersms2",
        "filtersms3",
        "filtersms4",
        "filtersms5",
        "filtersms6",
        "filtersms7",
        "filtersms8",
        "filtersms9",
        "firstpage",
        "lastpage",
        "guiprofile",
        "red",
        "green",
        "yellow",
        "blue",
        "increasepar",
        "decreasepar",
        "volampup",
        "volampdown",
        "volumeamplification",
        "createbookmark",
        "createepisodebookmark",
        "settingsreset",
        "settingslevelchange",
        "stereomode",
        "nextstereomode",
        "previousstereomode",
        "togglestereomode",
        "stereomodetomono",
        "channelup",
        "channeldown",
        "previouschannelgroup",
        "nextchannelgroup",
        "playpvr",
        "playpvrtv",
        "playpvrradio",
        "record",
        "leftclick",
        "rightclick",
        "middleclick",
        "doubleclick",
        "longclick",
        "wheelup",
        "wheeldown",
        "mousedrag",
        "mousemove",
        "tap",
        "longpress",
        "pangesture",
        "zoomgesture",
        "rotategesture",
        "swipeleft",
        "swiperight",
        "swipeup",
        "swipedown",
        "error",
        "noop"
    );

    public function Create()
    {
        parent::Create();
    }

    public function ApplyChanges()
    {
        // Variablen und Profile für Aktionen fehlen.
        // Events fehlen
  /*      7.3 Input

    7.3.1 Input.OnInputFinished
    7.3.2 Input.OnInputRequested

*/
        
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
        foreach ($KodiPayload as $param => $value)
        {
            switch ($param)
            {
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
            }
        }
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

    public function RawSend(string $Namespace, string $Method, $Params)
    {
        return parent::RawSend($Namespace, $Method, $Params);
    }

    public function Up()
    {
        $KodiData = new Kodi_RPC_Data(self::$Namespace, 'Up');
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        return $ret === 'OK';
    }

    public function Down()
    {
        $KodiData = new Kodi_RPC_Data(self::$Namespace, 'Down');
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        return $ret === 'OK';
    }

    public function Left()
    {
        $KodiData = new Kodi_RPC_Data(self::$Namespace, 'Left');
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        return $ret === 'OK';
    }

    public function Right()
    {
        $KodiData = new Kodi_RPC_Data(self::$Namespace, 'Right');
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        return $ret === 'OK';
    }

    public function Back()
    {
        $KodiData = new Kodi_RPC_Data(self::$Namespace, 'Back');
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        return $ret === 'OK';
    }

    public function ContextMenu()
    {
        $KodiData = new Kodi_RPC_Data(self::$Namespace, 'ContextMenu');
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        return $ret === 'OK';
    }

    public function Home()
    {
        $KodiData = new Kodi_RPC_Data(self::$Namespace, 'Home');
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        return $ret === 'OK';
    }

    public function Info()
    {
        $KodiData = new Kodi_RPC_Data(self::$Namespace, 'Info');
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        return $ret === 'OK';
    }

    public function Select()
    {
        $KodiData = new Kodi_RPC_Data(self::$Namespace, 'Select');
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        return $ret === 'OK';
    }

    public function ShowOSD()
    {
        $KodiData = new Kodi_RPC_Data(self::$Namespace, 'ShowOSD');
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        return $ret === 'OK';
    }

    public function ShowCodec()
    {
        $KodiData = new Kodi_RPC_Data(self::$Namespace, 'ShowCodec');
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        return $ret === 'OK';
    }

    public function ExecuteAction(string $Action)
    {
        if (!is_string($Action))
        {
            trigger_error('Action must be string', E_USER_NOTICE);
            return false;
        }
        if (!in_array($Action, self::$ExecuteAction))
        {
            trigger_error('Unknown action.', E_USER_NOTICE);
            return false;
        }
        $KodiData = new Kodi_RPC_Data(self::$Namespace);
        $KodiData->ExecuteAction(array("action" => $Action));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        return $ret === 'OK';
    }

    public function SendText(string $Text, boolean $Done)
    {
        if (!is_string($Text))
        {
            trigger_error('Text must be string', E_USER_NOTICE);
            return false;
        }
        $KodiData = new Kodi_RPC_Data(self::$Namespace);
        $KodiData->SendText(array("text" => $Text, "done" => $Done));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        return $ret === 'OK';
    }

    public function RequestState(string $Ident)
    {

    }

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