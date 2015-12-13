<?

require_once(__DIR__ . "/../KodiClass.php");  // diverse Klassen

class KodiDevicePlayer extends IPSModule
{

    static $Namespace = 'Application';
//    static $ProfilAssociations = array(
//        'Action.Kodi' => array(
//            array(0x01, "Ausführen", "", -1)
//        )
//    );

    static $Properties = array(
        "volume",
        "muted",
        "name",
        "version"
    );

    /*
      static $Properties = array(
      "volume" => array(
      "Ident" => "volume"//,
      //            "Profil" => "~Intensity.100",
      //            "Action" => true,
      //            "Typ" => vtInteger
      ),
      "muted" => array(
      "Ident" => "mute"//,
      //            "Profil" => "~Switch",
      //            "Action" => true,
      //            "Typ" => vtBoolean
      ),
      "name" => array(
      "Ident" => "name"//,
      //            "Typ" => vtString
      ),
      "version" => array(
      //            "Typ" => vtObject,
      "Ident" => "version",
      "Object" => array(
      "minor" => array(
      "Ident" => "minor"//,
      //                    "Typ" => vtInteger
      ),
      "major" => array(
      "Ident" => "major"//,
      //                    "Typ" => vtInteger
      ),
      "tag" => array(
      "Ident" => "tag"//,
      //                    "Typ" => vtString
      )
      )
      )
      );
     */

//static $Ident =  array("volume", "mute", "name", "version");
    public function Create()
    {

        parent::Create();
        $this->ConnectParent("{D2F106B5-4473-4C19-A48F-812E8BAA316C}");
//        $this->RegisterPropertyInteger("Player", 1);
    }

    public function ApplyChanges()
    {
        //Never delete this line!
        parent::ApplyChanges();
        $this->RegisterVariableString("ReplyJSONData", "ReplyJSONData", "", -3);
        IPS_SetHidden($this->GetIDForIdent('ReplyJSONData'), true);
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

        if (IPS_GetKernelRunlevel() == KR_READY)
            if ($this->HasActiveParent())
                $this->RequestProperties(self::$Properties);
    }

################## PRIVATE     

    private function RequestProperties(array $Properties)
    {
//        $KodiData = new Kodi_RPC_Data(self::$Namespace, 'GetProperties', array("properties" => array_keys($Properties)));
        $KodiData = new Kodi_RPC_Data(self::$Namespace, 'GetProperties', array("properties" => $Properties));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        return true;
    }

    private function Decode(Kodi_RPC_Data $KodiData)
    {
        $ret = $KodiData->GetEvent();
        IPS_LogMessage('DecodeJSONData', print_r($ret, true));

        foreach ($ret as $param => $value)
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

        /*
          foreach ($ret as $ident => $value)
          {
          $found = array_search(array('Ident' => $ident), self::$Properties);
          if ($found !== false)
          {
          if (array_key_exists("Object", self::$Properties[$found]))
          {
          $found = array_search(array('Ident' => $ident), self::$Properties["Object"]);
          if ($found !== false)
          $this->SetValue($ident, $value);
          }
          else
          {
          $this->SetValue($ident, $value);
          }
          }
          } */
    }

################## ActionHandler

    public function RequestAction($Ident, $Value)
    {
        switch ($Ident)
        {
            case "mute":
                return $this->Mute($Value);
            case "volume":
                return $this->Volume($Value);
            case "quit":
                return $this->Quit();
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
        $KodiData = new Kodi_RPC_Data($Namespace, $Method, $Params);
        $ret = $this->Send($KodiData);
        return $ret;
    }

    public function Mute(boolean $Value)
    {
        if (!is_bool($Value))
        {
            trigger_error('Value must be boolean', E_USER_NOTICE);
            return false;
        }
        $KodiData = new Kodi_RPC_Data(self::$Namespace, 'SetMute', array("mute" => $Value));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        $this->SetValueBoolean("mute", $ret);
        return $ret['mute'] === $Value;
    }

    public function Volume(integer $Value)
    {
        if (!is_int($Value))
        {
            trigger_error('Value must be integer', E_USER_NOTICE);
            return false;
        }
        $KodiData = new Kodi_RPC_Data(self::$Namespace, 'SetVolume', array("volume" => $Value));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        $this->SetValueInteger("volume", $ret);
        return $ret['volume'] === $Value;
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
        if (!in_array($Ident, self::$Properties))
        {
            trigger_error('Property not found.');
            return false;
        }
        return $this->RequestProperties(array($Ident));
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
        $Data = json_decode($JSONString);
        if ($Data->DataID <> '{73249F91-710A-4D24-B1F1-A72F216C2BDC}')
            return false;

        if (property_exists($Data, 'Id')) //Reply
        {
            $ReplyJSONDataID = $this->GetIDForIdent('ReplyJSONData');
            if (!$this->lock('ReplyJSONData'))
                throw new Exception('ReplyJSONData is locked', E_USER_NOTICE);
            SetValueString($ReplyJSONDataID, $JSONString);
            $this->unlock('ReplyJSONData');
//        IPS_LogMessage('ReceiveAPIData2', print_r($APIData, true));
            return true;
        }
        $KodiData = new Kodi_RPC_Data();
        $KodiData->GetDataFromJSONKodiObject($Data);
//        IPS_LogMessage('ReceiveJSONData', print_r($KodiData, true));
        //Variable nachführen
        $this->Decode($KodiData);
    }

    private function Send(Kodi_RPC_Data $KodiData)
    {
        try
        {
            if (!$this->HasActiveParent())
                throw new Exception('Intance has no active parent.', E_USER_NOTICE);

            $ReplyJSONDataID = $this->GetIDForIdent('ReplyJSONData');

            if (!$this->lock('RequestSendData'))
                throw new Exception('RequestSendData is locked', E_USER_NOTICE);

            if (!$this->lock('ReplyJSONData'))
            {
                $this->unlock('ReplyJSONData');
                throw new Exception('ReplyJSONData is locked', E_USER_NOTICE);
            }
            SetValueString($ReplyJSONDataID, '');
            $this->unlock('ReplyJSONData');

            $ret = $this->SendDataToParent($KodiData);
            if ($ret === false)
            {
//            IPS_LogMessage('exc',print_r($ret,1));
                $this->unlock('RequestSendData');
                throw new Exception('Instance has no active Parent Instance!', E_USER_NOTICE);
            }
            $ReplayKodiData = $this->WaitForResponse($KodiData->Id);

            //        IPS_LogMessage('ReplayATData:'.$this->InstanceID,print_r($ReplayATData,1));

            if ($ReplayKodiData === false)
            {
                //          Senddata('TX_Status','Timeout');
                $this->unlock('RequestSendData');
                throw new Exception('Send Data Timeout', E_USER_NOTICE);
            }
            //            Senddata('TX_Status','OK')
            $this->unlock('RequestSendData');
            $ret = $ReplayKodiData->GetResult();
            if (is_a($ret, 'KodiRPCException'))
            {
                throw $ret;
            }
            return $ret;
        } catch (KodiRPCException $ex)
        {
            trigger_error('Error (' . $ex->getCode() . '): ' . $ex->getMessage(), E_USER_NOTICE);
        } catch (Exception $ex)
        {
            trigger_error($ex->getMessage(), $ex->getCode());
        }
        return NULL;
    }

    protected function SendDataToParent($Data)
    {
        // API-Daten verpacken und dann versenden.
        IPS_LogMessage('SendDataToSplitter:' . $this->InstanceID, print_r($Data, true));
        $JSONString = $Data->ToKodiObjectJSONString('{0222A902-A6FA-4E94-94D3-D54AA4666321}');

        // Daten senden
        return @IPS_SendDataToParent($this->InstanceID, $JSONString);
    }

################## DUMMYS / WOARKAROUNDS - private

    private function SetValueBoolean($Ident, $value)
    {
        $id = $this->GetIDForIdent($Ident);
        if (GetValueBoolean($id) <> $value)
        {
            SetValueBoolean($id, $value);
            return true;
        }
        return false;
    }

    private function SetValueInteger($Ident, $value)
    {
        $id = $this->GetIDForIdent($Ident);
        if (GetValueInteger($id) <> $value)
        {
            SetValueInteger($id, $value);
            return true;
        }
        return false;
    }

    private function SetValueString($Ident, $value)
    {
        $id = $this->GetIDForIdent($Ident);
        if (GetValueString($id) <> $value)
        {
            SetValueString($id, $value);
            return true;
        }
        return false;
    }

################## DUMMYS / WOARKAROUNDS - protected

    private function WaitForResponse($Id)
    {
        $ReplyJSONDataID = $this->GetIDForIdent('ReplyJSONData');
        for ($i = 0; $i < 300; $i++)
        {
            if (GetValueString($ReplyJSONDataID) === '')
                IPS_Sleep(5);
            else
            {
                if ($this->lock('ReplyJSONData'))
                {
                    $ret = GetValueString($ReplyJSONDataID);
                    SetValueString($ReplyJSONDataID, '');
                    $this->unlock('ReplyJSONData');
                    $JSON = json_decode($ret);
                    $Kodi_Data = new Kodi_RPC_Data();
                    $Kodi_Data->GetDataFromJSONKodiObject($JSON);
                    if ($Id == $Kodi_Data->Id)
                        return $Kodi_Data;
                    else
                    {
                        $i = $i - 100;
                        if ($i < 0)
                            $i = 0;
                    }
                }
            }
        }
        if ($this->lock('ReplyJSONData'))
        {
            SetValueString($ReplyJSONDataID, '');
            $this->unlock('ReplyJSONData');
        }

        return false;
    }

    protected function HasActiveParent()
    {
//        IPS_LogMessage(__CLASS__, __FUNCTION__); //          
        $instance = IPS_GetInstance($this->InstanceID);
        if ($instance['ConnectionID'] > 0)
        {
            $parent = IPS_GetInstance($instance['ConnectionID']);

            if ($parent['InstanceStatus'] == 102)
                return true;
        }
        return false;
    }

    /*    protected function GetVariable($Ident, $VarType, $VarName, $Profile, $EnableAction)
      {
      $VarID = @$this->GetIDForIdent($Ident);
      if ($VarID > 0)
      {
      if (IPS_GetVariable($VarID)['VariableType'] <> $VarType)
      {
      IPS_DeleteVariable($VarID);
      $VarID = false;
      }
      }
      if ($VarID === false)
      {
      $this->MaintainVariable($Ident, $VarName, $VarType, $Profile, 0, true);
      if ($EnableAction)
      $this->MaintainAction($Ident, true);
      $VarID = $this->GetIDForIdent($Ident);
      }
      return $VarID;
      }

      protected function RegisterTimer($Name, $Interval, $Script)
      {
      $id = @IPS_GetObjectIDByIdent($Name, $this->InstanceID);
      if ($id === false)
      $id = 0;


      if ($id > 0)
      {
      if (!IPS_EventExists($id))
      throw new Exception("Ident with name " . $Name . " is used for wrong object type", E_USER_NOTICE);

      if (IPS_GetEvent($id)['EventType'] <> 1)
      {
      IPS_DeleteEvent($id);
      $id = 0;
      }
      }

      if ($id == 0)
      {
      $id = IPS_CreateEvent(1);
      IPS_SetParent($id, $this->InstanceID);
      IPS_SetIdent($id, $Name);
      } IPS_SetName($id, $Name);
      IPS_SetHidden($id, true);
      IPS_SetEventScript($id, $Script);
      if ($Interval > 0)
      {
      IPS_SetEventCyclic($id, 0, 0, 0, 0, 1, $Interval);

      IPS_SetEventActive($id, true);
      }
      else
      {
      IPS_SetEventCyclic($id, 0, 0, 0, 0, 1, 1);

      IPS_SetEventActive($id, false);
      }
      }

      protected function UnregisterTimer($Name)
      {
      $id = @IPS_GetObjectIDByIdent($Name, $this->InstanceID);
      if ($id > 0)
      {
      if (!IPS_EventExists($id))
      throw new Exception('Timer not present', E_USER_NOTICE);
      IPS_DeleteEvent($id);
      }
      }

      protected function SetTimerInterval($Name, $Interval)
      {
      $id = @IPS_GetObjectIDByIdent($Name, $this->InstanceID);
      if ($id === false)
      throw new Exception('Timer not present', E_USER_NOTICE);
      if (!IPS_EventExists($id))
      throw new Exception('Timer not present', E_USER_NOTICE);

      $Event = IPS_GetEvent($id);

      if ($Interval < 1)
      {
      if ($Event['EventActive'])
      IPS_SetEventActive($id, false);
      }
      else
      {
      if
      ($Event['CyclicTimeValue'] <> $Interval)
      IPS_SetEventCyclic($id, 0, 0, 0, 0, 1, $Interval)
      ;
      if (!$Event['EventActive'])
      IPS_SetEventActive($id, true);
      }
      }

      protected function SetStatus($InstanceStatus)
      {
      if ($InstanceStatus <>
      IPS_GetInstance($this->InstanceID)['InstanceStatus'])
      parent::SetStatus($InstanceStatus);
      }

      protected
      function LogMessage($data, $cata)
      {

      }

      protected function SetSummary($data)
      {
      //        IPS_LogMessage(__CLASS__, __FUNCTION__ . "Data:" . $data); //
      }
     */

    //Remove on next Symcon update
    protected function RegisterProfileInteger($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $StepSize)
    {

        if (!IPS_VariableProfileExists($Name))
        {
            IPS_CreateVariableProfile($Name, 1);
        } else
        {
            $profile = IPS_GetVariableProfile($Name);
            if ($profile['ProfileType'] != 1)
                throw new Exception("Variable profile type does not match for profile " . $Name, E_USER_WARNING);
        }

        IPS_SetVariableProfileIcon($Name, $Icon);
        IPS_SetVariableProfileText($Name, $Prefix, $Suffix);
        IPS_SetVariableProfileValues($Name, $MinValue, $MaxValue, $StepSize);
    }

    protected function RegisterProfileIntegerEx($Name, $Icon, $Prefix, $Suffix, $Associations)
    {
        if (sizeof($Associations) === 0)
        {
            $MinValue = 0;
            $MaxValue = 0;
        } else
        {
            $MinValue = $Associations[0][0];
            $MaxValue = $Associations[sizeof($Associations) - 1][0];
        }

        $this->RegisterProfileInteger($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, 0);

        foreach ($Associations as $Association)
        {
            IPS_SetVariableProfileAssociation($Name, $Association[0], $Association[1], $Association[2], $Association[3]);
        }
    }

################## SEMAPHOREN Helper  - private  

    private function lock($ident)
    {
        for ($i = 0; $i < 100; $i ++)
        {
            if (IPS_SemaphoreEnter("KODI_" . (string) $this->InstanceID . (string) $ident, 1))
            {
                return true;
            } else
            {
                IPS_Sleep(mt_rand(1, 5));
            }
        }
        return false;
    }

    private function unlock($ident)
    {
        IPS_SemaphoreLeave("KODI_" . (string) $this->InstanceID . (string) $ident);
    }

}

?>