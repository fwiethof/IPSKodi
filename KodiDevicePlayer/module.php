<?

require_once(__DIR__ . "/../KodiClass.php");  // diverse Klassen

class KodiDevicePlayer extends IPSModule
{

    public function Create()
    {
        parent::Create();
        $this->ConnectParent("{D2F106B5-4473-4C19-A48F-812E8BAA316C}");
        $this->RegisterPropertyInteger("Player", 1);
    }

    public function ApplyChanges()
    {
        //Never delete this line!
        parent::ApplyChanges();
        $this->RegisterVariableString("ReplyJSONData", "ReplyJSONData", "", -3);
        IPS_SetHidden($this->GetIDForIdent('ReplyJSONData'), true);

        if (IPS_GetKernelRunlevel() == KR_READY)
            $this->RequestState();
    }

################## PRIVATE     

    private function RequestState()
    {
        if (!$this->HasActiveParent())
            return;
    }

################## ActionHandler

    public function RequestAction($Ident, $Value)
    {
        
    }

################## PUBLIC
    /**
     * This function will be available automatically after the module is imported with the module control.
     * Using the custom prefix this function will be callable from PHP and JSON-RPC through:
     */
    
    public function RawSend(string $Namespace, string $Method, $Params)
    {
        $KodiData = new Kodi_RPC_Data($Namespace, $Method, $Params);
        $this->SendDataToParent($KodiData);
    }

    public function Mute(boolean $Value)
    {
        
    }

    public function Volume(integer $Value)
    {
        
    }

    public function Play()
    {
        
    }

    public function SelectInput(integer $Value)
    {
        
    }

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

            
            $KodiData = new Kodi_RPC_Data();
            $KodiData->GetDataFromJSONKodiObject($Data);
            IPS_LogMessage('ReceiveJSONDataRESPONSE', print_r($KodiData, true));
            
            $this->unlock('ReplyJSONData');
//        IPS_LogMessage('ReceiveAPIData2', print_r($APIData, true));
        } else
        {
            $KodiData = new Kodi_RPC_Data();
            $KodiData->GetDataFromJSONKodiObject($Data);
            IPS_LogMessage('ReceiveJSONDataEVENT', print_r($KodiData, true));
            // EVENT verarbeiten
        }
    }



    private function Send(Kodi_RPC_Data $KodiData)
    {
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
        return $ReplayKodiData;
    }

    protected function SendDataToParent($Data)
    {
        // API-Daten verpacken und dann versenden.
        IPS_LogMessage('SendDataToSplitter:'.$this->InstanceID,print_r($Data,true));        
        $JSONString = $Data->ToKodiObjectJSONString('{0222A902-A6FA-4E94-94D3-D54AA4666321}');

        // Daten senden
        return @IPS_SendDataToParent($this->InstanceID, $JSONString);
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
        }
        else
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
        }
        else
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
            }
            else
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