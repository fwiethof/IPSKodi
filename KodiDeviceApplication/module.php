<?

require_once(__DIR__ . "/../KodiClass.php");  // diverse Klassen

class KodiDeviceApplication extends KodiBase
{

    static $Namespace = 'Application';
    static $Properties = array(
        "volume",
        "muted",
        "name",
        "version"
    );

    public function Create()
    {
        parent::Create();
    }

    public function ApplyChanges()
    {
        $this->RegisterProfileIntegerEx("Action.Kodi", "", "", "", Array(
            Array(0, "Ausführen", "", -1)
        ));
        $this->RegisterVariableString("name", "Name", "", 0);
        $this->RegisterVariableString("version", "Version", "", 1);
        $this->RegisterVariableInteger("quit", "Kodi beenden", "Action.Kodi", 2);
        $this->EnableAction("quit");
        $this->RegisterVariableBoolean("mute", "Mute", "~Switch", 3);
        $this->EnableAction("mute");
        $this->RegisterVariableInteger("volume", "Volume", "~Intensity.100", 4);
        $this->EnableAction("volume");

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
                case "mute":
                case "muted":
                    $this->SetValueBoolean("mute", $value);
                    break;
                case "volume":
                    $this->SetValueInteger("volume", $value);
                    break;
                case "name":
                    $this->SetValueString("name", $value);
                    break;
                case "version":
                    $this->SetValueString("version", $value->major . '.' . $value->minor);
                    break;
            }
        }
    }

################## ActionHandler

    public function RequestAction($Ident, $Value)
    {
        switch ($Ident)
        {
            case "mute":
                $this->Mute($Value);
                break;
            case "volume":
                $this->Volume($Value);
                break;
            case "quit":
                $this->Quit();
                break;
            default:
                trigger_error('Invalid Ident.', E_USER_NOTICE);
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

    public function Mute(boolean $Value)
    {
        if (!is_bool($Value))
        {
            trigger_error('Value must be boolean', E_USER_NOTICE);
            return false;
        }
        $KodiData = new Kodi_RPC_Data(self::$Namespace);
        $KodiData->SetMute(array("mute" => $Value));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        $this->SetValueBoolean("mute", $ret);
        return $ret === $Value;
    }

    public function Volume(integer $Value)
    {
        if (!is_int($Value))
        {
            trigger_error('Value must be integer', E_USER_NOTICE);
            return false;
        }
//        $KodiData = new Kodi_RPC_Data(self::$Namespace, 'SetVolume', array("volume" => $Value));
        $KodiData = new Kodi_RPC_Data(self::$Namespace);
        $KodiData->SetVolume(array("volume" => $Value));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        $this->SetValueInteger("volume", $ret);
        return $ret === $Value;
    }

    public function Quit()
    {
        $KodiData = new Kodi_RPC_Data(self::$Namespace, 'Quit');
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        return true;
    }

    public function RequestState(string $Ident)
    {
        return parent::RequestState($Ident);
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