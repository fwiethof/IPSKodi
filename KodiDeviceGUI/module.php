<?

require_once(__DIR__ . "/../KodiClass.php");  // diverse Klassen

class KodiDeviceGUI extends KodiBase
{

    static $Namespace = 'GUI';
    static $Properties = array(
        "currentwindow",
        "currentcontrol",
        "skin",
        "fullscreen",
        "stereoscopicmode"
    );

    public function Create()
    {
        parent::Create();
    }

    public function ApplyChanges()
    {

        $this->RegisterVariableString("currentwindow", "Aktuelles Fenster", "", 0);
        $this->RegisterVariableInteger("_currentwindowid", "Aktuelles Fenster (id)", "", 0);
        IPS_SetHidden($this->GetIDForIdent('_currentwindowid'), true);
        $this->RegisterVariableString("currentcontrol", "Aktuelles Control", "", 1);
        $this->RegisterVariableString("skin", "Aktuelles Skin", "", 2);
        $this->RegisterVariableString("_skinid", "Aktuelles Skin (id)", "", 2);
        IPS_SetHidden($this->GetIDForIdent('_skinid'), true);
        $this->RegisterVariableBoolean("fullscreen", "Vollbild", "~Switch", 3);
        $this->EnableAction("fullscreen");
        $this->RegisterVariableBoolean("screensaver", "Bildschirmschoner", "~Switch", 4);
        //Never delete this line!
        parent::ApplyChanges();
    }

################## PRIVATE     

    protected function Decode($Method, $KodiPayload)
    {
        switch ($Method)
        {
            case 'GetProperties':
                foreach ($KodiPayload as $param => $value)
                {
                    switch ($param)
                    {
                        case "currentcontrol":
                            $this->SetValueString("currentcontrol", $value->label);
                            break;
                        case "currentwindow":
                            $this->SetValueString("currentwindow", $value->label);
                            $this->SetValueInteger("currentwindowid", $value->id);
                            break;
                        case "fullscreen":
                            $this->SetValueBoolean("fullscreen", $value);
                            break;
                        case "skin":
                            $this->SetValueString("skin", $value->name);
                            $this->SetValueString("skinid", $value->id);
                            break;
                    }
                }
                break;
            case 'OnScreensaverDeactivated':
                $this->SetValueBoolean("screensaver", false);
                break;
            case 'OnScreensaverActivated':
                $this->SetValueBoolean("screensaver", true);
                break;
        }
    }

################## ActionHandler

    public function RequestAction($Ident, $Value)
    {
        switch ($Ident)
        {
            case "fullscreen":
                $this->Fullscreen($Value);
                break;
            default:
                trigger_error('Invalid Ident.', E_USER_NOTICE);
                break;
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

    public function Fullscreen(boolean $Value)
    {
        if (!is_bool($Value))
        {
            trigger_error('Value must be boolean', E_USER_NOTICE);
            return false;
        }
        $KodiData = new Kodi_RPC_Data(self::$Namespace);
        $KodiData->SetFullscreen(array("fullscreen" => $Value));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        $this->SetValueBoolean("fullscreen", $ret);
        return $ret === $Value;
    }

    public function ShowNotification(string $Title, string $Message, string $Image, integer $Timeout)
    {
        if (!is_string($Title))
        {
            trigger_error('Title must be string', E_USER_NOTICE);
            return false;
        }
        if (!is_string($Message))
        {
            trigger_error('Message must be string', E_USER_NOTICE);
            return false;
        }
        if (!is_int($Timeout))
        {
            trigger_error('Timeout must be integer', E_USER_NOTICE);
            return false;
        }

        $Data = array("title" => $Title, "message" => $Message);

        if (is_string($Image))
            $Data['image'] = $Image;
        if ($Timeout <> 0)
            $Data['timeout'] = $Timeout;

        $KodiData = new Kodi_RPC_Data(self::$Namespace);
        $KodiData->ShowNotification($Data);
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        return $ret === $Title;
    }

    public function ActivateWindow(string $Window)
    {
        if (!is_string($Window))
        {
            trigger_error('Window must be string', E_USER_NOTICE);
            return false;
        }
        $KodiData = new Kodi_RPC_Data(self::$Namespace);
        $KodiData->ActivateWindow(array('window' => $Window));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        return $ret === $Window;
    }

    public function RequestState(string $Ident)
    {
        return parent::RequestState($Ident);
    }

 ################# Datapoints

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