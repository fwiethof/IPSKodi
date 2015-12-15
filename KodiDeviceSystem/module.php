<?

require_once(__DIR__ . "/../KodiClass.php");  // diverse Klassen

class KodiDeviceSystem extends KodiBase
{

    static $Namespace = 'System';
    static $Properties = array(
        "canshutdown",
        "canhibernate",
        "cansuspend",
        "canreboot"
    );

    public function Create()
    {
        parent::Create();
    }

    public function ApplyChanges()
    {
        $this->RegisterVariableInteger("shutdown", "Herunterfahren", "Action.Kodi", 4);
        $this->EnableAction("shutdown");
        $this->RegisterVariableInteger("hibernate", "Ruhezustand", "Action.Kodi", 2);
        $this->EnableAction("hibernate");
        $this->RegisterVariableInteger("suspend", "Standby", "Action.Kodi", 1);
        $this->EnableAction("suspend");
        $this->RegisterVariableInteger("reboot", "Neustart", "Action.Kodi", 3);
        $this->EnableAction("reboot");
        $this->RegisterVariableInteger("ejectOpticalDrive", "Laufwerk öffnen/schließen", "Action.Kodi", 5);
        $this->EnableAction("ejectOpticalDrive");

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
                    IPS_SetHidden($this->GetIDForIdent(substr($param, 3)), $value);
                }
                break;
        }
    }

################## ActionHandler

    public function RequestAction($Ident, $Value)
    {
        switch ($Ident)
        {
            case "shutdown":
            case "reboot":
            case "hibernate":
            case "suspend":
            case "ejectOpticalDrive":
                $this->{ucfirt($Ident)}();
            default:
                return trigger_error('Invalid Ident.', E_USER_NOTICE);
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

    public function Shutdown()
    {
        $KodiData = new Kodi_RPC_Data(self::$Namespace, 'Shutdown');
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        return $ret === 'OK';
    }
    public function Hibernate()
    {
        $KodiData = new Kodi_RPC_Data(self::$Namespace, 'Hibernate');
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        return $ret === 'OK';
    }
    public function Suspend()
    {
        $KodiData = new Kodi_RPC_Data(self::$Namespace, 'Suspend');
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        return $ret === 'OK';
    }
    public function Reboot()
    {
        $KodiData = new Kodi_RPC_Data(self::$Namespace, 'Reboot');
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        return $ret === 'OK';
    }
    public function EjectOpticalDrive()
    {
        $KodiData = new Kodi_RPC_Data(self::$Namespace, 'EjectOpticalDrive');
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        return $ret === 'OK';
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