<?

if (@constant('IPS_BASE') == null) //Nur wenn Konstanten noch nicht bekannt sind.
{
// --- BASE MESSAGE
    define('IPS_BASE', 10000);                             //Base Message
    define('IPS_KERNELSHUTDOWN', IPS_BASE + 1);            //Pre Shutdown Message, Runlevel UNINIT Follows
    define('IPS_KERNELSTARTED', IPS_BASE + 2);             //Post Ready Message
// --- KERNEL
    define('IPS_KERNELMESSAGE', IPS_BASE + 100);           //Kernel Message
    define('KR_CREATE', IPS_KERNELMESSAGE + 1);            //Kernel is beeing created
    define('KR_INIT', IPS_KERNELMESSAGE + 2);              //Kernel Components are beeing initialised, Modules loaded, Settings read
    define('KR_READY', IPS_KERNELMESSAGE + 3);             //Kernel is ready and running
    define('KR_UNINIT', IPS_KERNELMESSAGE + 4);            //Got Shutdown Message, unloading all stuff
    define('KR_SHUTDOWN', IPS_KERNELMESSAGE + 5);          //Uninit Complete, Destroying Kernel Inteface
// --- KERNEL LOGMESSAGE
    define('IPS_LOGMESSAGE', IPS_BASE + 200);              //Logmessage Message
    define('KL_MESSAGE', IPS_LOGMESSAGE + 1);              //Normal Message                      | FG: Black | BG: White  | STLYE : NONE
    define('KL_SUCCESS', IPS_LOGMESSAGE + 2);              //Success Message                     | FG: Black | BG: Green  | STYLE : NONE
    define('KL_NOTIFY', IPS_LOGMESSAGE + 3);               //Notiy about Changes                 | FG: Black | BG: Blue   | STLYE : NONE
    define('KL_WARNING', IPS_LOGMESSAGE + 4);              //Warnings                            | FG: Black | BG: Yellow | STLYE : NONE
    define('KL_ERROR', IPS_LOGMESSAGE + 5);                //Error Message                       | FG: Black | BG: Red    | STLYE : BOLD
    define('KL_DEBUG', IPS_LOGMESSAGE + 6);                //Debug Informations + Script Results | FG: Grey  | BG: White  | STLYE : NONE
    define('KL_CUSTOM', IPS_LOGMESSAGE + 7);               //User Message                        | FG: Black | BG: White  | STLYE : NONE
// --- MODULE LOADER
    define('IPS_MODULEMESSAGE', IPS_BASE + 300);           //ModuleLoader Message
    define('ML_LOAD', IPS_MODULEMESSAGE + 1);              //Module loaded
    define('ML_UNLOAD', IPS_MODULEMESSAGE + 2);            //Module unloaded
// --- OBJECT MANAGER
    define('IPS_OBJECTMESSAGE', IPS_BASE + 400);
    define('OM_REGISTER', IPS_OBJECTMESSAGE + 1);          //Object was registered
    define('OM_UNREGISTER', IPS_OBJECTMESSAGE + 2);        //Object was unregistered
    define('OM_CHANGEPARENT', IPS_OBJECTMESSAGE + 3);      //Parent was Changed
    define('OM_CHANGENAME', IPS_OBJECTMESSAGE + 4);        //Name was Changed
    define('OM_CHANGEINFO', IPS_OBJECTMESSAGE + 5);        //Info was Changed
    define('OM_CHANGETYPE', IPS_OBJECTMESSAGE + 6);        //Type was Changed
    define('OM_CHANGESUMMARY', IPS_OBJECTMESSAGE + 7);     //Summary was Changed
    define('OM_CHANGEPOSITION', IPS_OBJECTMESSAGE + 8);    //Position was Changed
    define('OM_CHANGEREADONLY', IPS_OBJECTMESSAGE + 9);    //ReadOnly was Changed
    define('OM_CHANGEHIDDEN', IPS_OBJECTMESSAGE + 10);     //Hidden was Changed
    define('OM_CHANGEICON', IPS_OBJECTMESSAGE + 11);       //Icon was Changed
    define('OM_CHILDADDED', IPS_OBJECTMESSAGE + 12);       //Child for Object was added
    define('OM_CHILDREMOVED', IPS_OBJECTMESSAGE + 13);     //Child for Object was removed
    define('OM_CHANGEIDENT', IPS_OBJECTMESSAGE + 14);      //Ident was Changed
// --- INSTANCE MANAGER
    define('IPS_INSTANCEMESSAGE', IPS_BASE + 500);         //Instance Manager Message
    define('IM_CREATE', IPS_INSTANCEMESSAGE + 1);          //Instance created
    define('IM_DELETE', IPS_INSTANCEMESSAGE + 2);          //Instance deleted
    define('IM_CONNECT', IPS_INSTANCEMESSAGE + 3);         //Instance connectged
    define('IM_DISCONNECT', IPS_INSTANCEMESSAGE + 4);      //Instance disconncted
    define('IM_CHANGESTATUS', IPS_INSTANCEMESSAGE + 5);    //Status was Changed
    define('IM_CHANGESETTINGS', IPS_INSTANCEMESSAGE + 6);  //Settings were Changed
    define('IM_CHANGESEARCH', IPS_INSTANCEMESSAGE + 7);    //Searching was started/stopped
    define('IM_SEARCHUPDATE', IPS_INSTANCEMESSAGE + 8);    //Searching found new results
    define('IM_SEARCHPROGRESS', IPS_INSTANCEMESSAGE + 9);  //Searching progress in %
    define('IM_SEARCHCOMPLETE', IPS_INSTANCEMESSAGE + 10); //Searching is complete
// --- VARIABLE MANAGER
    define('IPS_VARIABLEMESSAGE', IPS_BASE + 600);              //Variable Manager Message
    define('VM_CREATE', IPS_VARIABLEMESSAGE + 1);               //Variable Created
    define('VM_DELETE', IPS_VARIABLEMESSAGE + 2);               //Variable Deleted
    define('VM_UPDATE', IPS_VARIABLEMESSAGE + 3);               //On Variable Update
    define('VM_CHANGEPROFILENAME', IPS_VARIABLEMESSAGE + 4);    //On Profile Name Change
    define('VM_CHANGEPROFILEACTION', IPS_VARIABLEMESSAGE + 5);  //On Profile Action Change
// --- SCRIPT MANAGER
    define('IPS_SCRIPTMESSAGE', IPS_BASE + 700);           //Script Manager Message
    define('SM_CREATE', IPS_SCRIPTMESSAGE + 1);            //On Script Create
    define('SM_DELETE', IPS_SCRIPTMESSAGE + 2);            //On Script Delete
    define('SM_CHANGEFILE', IPS_SCRIPTMESSAGE + 3);        //On Script File changed
    define('SM_BROKEN', IPS_SCRIPTMESSAGE + 4);            //Script Broken Status changed
// --- EVENT MANAGER
    define('IPS_EVENTMESSAGE', IPS_BASE + 800);             //Event Scripter Message
    define('EM_CREATE', IPS_EVENTMESSAGE + 1);             //On Event Create
    define('EM_DELETE', IPS_EVENTMESSAGE + 2);             //On Event Delete
    define('EM_UPDATE', IPS_EVENTMESSAGE + 3);
    define('EM_CHANGEACTIVE', IPS_EVENTMESSAGE + 4);
    define('EM_CHANGELIMIT', IPS_EVENTMESSAGE + 5);
    define('EM_CHANGESCRIPT', IPS_EVENTMESSAGE + 6);
    define('EM_CHANGETRIGGER', IPS_EVENTMESSAGE + 7);
    define('EM_CHANGETRIGGERVALUE', IPS_EVENTMESSAGE + 8);
    define('EM_CHANGETRIGGEREXECUTION', IPS_EVENTMESSAGE + 9);
    define('EM_CHANGECYCLIC', IPS_EVENTMESSAGE + 10);
    define('EM_CHANGECYCLICDATEFROM', IPS_EVENTMESSAGE + 11);
    define('EM_CHANGECYCLICDATETO', IPS_EVENTMESSAGE + 12);
    define('EM_CHANGECYCLICTIMEFROM', IPS_EVENTMESSAGE + 13);
    define('EM_CHANGECYCLICTIMETO', IPS_EVENTMESSAGE + 14);
// --- MEDIA MANAGER
    define('IPS_MEDIAMESSAGE', IPS_BASE + 900);           //Media Manager Message
    define('MM_CREATE', IPS_MEDIAMESSAGE + 1);             //On Media Create
    define('MM_DELETE', IPS_MEDIAMESSAGE + 2);             //On Media Delete
    define('MM_CHANGEFILE', IPS_MEDIAMESSAGE + 3);         //On Media File changed
    define('MM_AVAILABLE', IPS_MEDIAMESSAGE + 4);          //Media Available Status changed
    define('MM_UPDATE', IPS_MEDIAMESSAGE + 5);
// --- LINK MANAGER
    define('IPS_LINKMESSAGE', IPS_BASE + 1000);           //Link Manager Message
    define('LM_CREATE', IPS_LINKMESSAGE + 1);             //On Link Create
    define('LM_DELETE', IPS_LINKMESSAGE + 2);             //On Link Delete
    define('LM_CHANGETARGET', IPS_LINKMESSAGE + 3);       //On Link TargetID change
// --- DATA HANDLER
    define('IPS_DATAMESSAGE', IPS_BASE + 1100);             //Data Handler Message
    define('DM_CONNECT', IPS_DATAMESSAGE + 1);             //On Instance Connect
    define('DM_DISCONNECT', IPS_DATAMESSAGE + 2);          //On Instance Disconnect
// --- SCRIPT ENGINE
    define('IPS_ENGINEMESSAGE', IPS_BASE + 1200);           //Script Engine Message
    define('SE_UPDATE', IPS_ENGINEMESSAGE + 1);             //On Library Refresh
    define('SE_EXECUTE', IPS_ENGINEMESSAGE + 2);            //On Script Finished execution
    define('SE_RUNNING', IPS_ENGINEMESSAGE + 3);            //On Script Started execution
// --- PROFILE POOL
    define('IPS_PROFILEMESSAGE', IPS_BASE + 1300);
    define('PM_CREATE', IPS_PROFILEMESSAGE + 1);
    define('PM_DELETE', IPS_PROFILEMESSAGE + 2);
    define('PM_CHANGETEXT', IPS_PROFILEMESSAGE + 3);
    define('PM_CHANGEVALUES', IPS_PROFILEMESSAGE + 4);
    define('PM_CHANGEDIGITS', IPS_PROFILEMESSAGE + 5);
    define('PM_CHANGEICON', IPS_PROFILEMESSAGE + 6);
    define('PM_ASSOCIATIONADDED', IPS_PROFILEMESSAGE + 7);
    define('PM_ASSOCIATIONREMOVED', IPS_PROFILEMESSAGE + 8);
    define('PM_ASSOCIATIONCHANGED', IPS_PROFILEMESSAGE + 9);
// --- TIMER POOL
    define('IPS_TIMERMESSAGE', IPS_BASE + 1400);            //Timer Pool Message
    define('TM_REGISTER', IPS_TIMERMESSAGE + 1);
    define('TM_UNREGISTER', IPS_TIMERMESSAGE + 2);
    define('TM_SETINTERVAL', IPS_TIMERMESSAGE + 3);
    define('TM_UPDATE', IPS_TIMERMESSAGE + 4);
    define('TM_RUNNING', IPS_TIMERMESSAGE + 5);
// --- STATUS CODES
    define('IS_SBASE', 100);
    define('IS_CREATING', IS_SBASE + 1); //module is being created
    define('IS_ACTIVE', IS_SBASE + 2); //module created and running
    define('IS_DELETING', IS_SBASE + 3); //module us being deleted
    define('IS_INACTIVE', IS_SBASE + 4); //module is not beeing used
// --- ERROR CODES
    define('IS_EBASE', 200);          //default errorcode
    define('IS_NOTCREATED', IS_EBASE + 1); //instance could not be created
// --- Search Handling
    define('FOUND_UNKNOWN', 0);     //Undefined value
    define('FOUND_NEW', 1);         //Device is new and not configured yet
    define('FOUND_OLD', 2);         //Device is already configues (InstanceID should be set)
    define('FOUND_CURRENT', 3);     //Device is already configues (InstanceID is from the current/searching Instance)
    define('FOUND_UNSUPPORTED', 4); //Device is not supported by Module

    define('vtBoolean', 0);
    define('vtInteger', 1);
    define('vtFloat', 2);
    define('vtString', 3);
    define('vtArray', 8);
    define('vtObject', 9);
}

class KodiBase extends IPSModule
{

    static $Namespace;
    static $Properties;

    public function Create()
    {
        parent::Create();
        $this->ConnectParent("{D2F106B5-4473-4C19-A48F-812E8BAA316C}");
    }

    public function ApplyChanges()
    {
        //Never delete this line!
        parent::ApplyChanges();
        $this->RegisterVariableString("ReplyJSONData", "ReplyJSONData", "", -3);
        IPS_SetHidden($this->GetIDForIdent('ReplyJSONData'), true);

        if (IPS_GetKernelRunlevel() == KR_READY)
            if ($this->HasActiveParent())
                $this->RequestProperties(array("properties" => static::$Properties));
    }

################## PRIVATE     

    protected function RequestProperties(array $Params)
    {
        if (count($Params["properties"]) == 0)
            return true;
        $KodiData = new Kodi_RPC_Data(static::$Namespace, 'GetProperties', $Params);
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        $this->Decode('GetProperties', $ret);
        return true;
    }

    protected function Decode($Method, $KodiPayload)
    {
        
    }

    protected function ConvertTime($Time)
    {
        $Time->minutes = str_pad($Time->minutes, 2, "00", STR_PAD_LEFT);
        $Time->seconds = str_pad($Time->seconds, 2, "00", STR_PAD_LEFT);
        if ($Time->hours > 0)
        {
            return $Time->hours . ":" . $Time->minutes . ":" . $Time->seconds;
        }
        return $Time->minutes . ":" . $Time->seconds;
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
        $ret = $this->Send($KodiData);
        return $ret;
    }

    public function RequestState(string $Ident)
    {
        if ($Ident == 'ALL')
            return $this->RequestProperties(array("properties" => static::$Properties));
        if ($Ident == 'PARTIAL')
            return $this->RequestProperties(array("properties" => static::$PartialProperties));
        if (!in_array($Ident, static::$Properties))
        {
            trigger_error('Property not found.');
            return false;
        }
        return $this->RequestProperties(array("properties" => array($Ident)));
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
            $this->unlock('ReplyJSONData');
            return true;
        }
        $KodiData = new Kodi_RPC_Data();
        $KodiData->GetDataFromJSONKodiObject($Data);
        if (is_array(static::$Namespace))
        {
            if (in_array($KodiData->Namespace, static::$Namespace))
            {
                $this->Decode($KodiData->Method, $KodiData->GetEvent());
                return true;
            }
        }
        else
        {
            if ($KodiData->Namespace == static::$Namespace)
            {
                $this->Decode($KodiData->Method, $KodiData->GetEvent());
                return true;
            }
        }
        return false;
    }

    protected function Send(Kodi_RPC_Data $KodiData, boolean $needResponse = null)
    {
        if (!is_null($needResponse))
        {
            try
            {
                $ret = $this->SendDataToParent($KodiData);
                if ($ret === false)
                {
                    throw new Exception('Instance has no active Parent Instance!', E_USER_NOTICE);
                }
            }
            catch (Exception $ex)
            {
                trigger_error($ex->getMessage(), $ex->getCode());
                return false;
            }
            return true;
        }
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
                $this->unlock('RequestSendData');
                throw new Exception('Instance has no active Parent Instance!', E_USER_NOTICE);
            }
            $ReplayKodiData = $this->WaitForResponse((int) $KodiData->Id);
            if ($ReplayKodiData === false)
            {
                $this->unlock('RequestSendData');
                throw new Exception('Send Data Timeout', E_USER_NOTICE);
            }
            $this->unlock('RequestSendData');
            $ret = $ReplayKodiData->GetResult();
            if (is_a($ret, 'KodiRPCException'))
            {
                throw $ret;
            }
            return $ret;
        }
        catch (KodiRPCException $ex)
        {
            trigger_error('Error (' . $ex->getCode() . '): ' . $ex->getMessage(), E_USER_NOTICE);
        }
        catch (Exception $ex)
        {
            trigger_error($ex->getMessage(), $ex->getCode());
        }
        return NULL;
    }

    protected function SendDataToParent($Data)
    {
        // API-Daten verpacken und dann versenden.
        //IPS_LogMessage('SendDataToSplitter:' . $this->InstanceID, print_r($Data, true));
        $JSONString = $Data->ToKodiObjectJSONString('{0222A902-A6FA-4E94-94D3-D54AA4666321}');

        // Daten senden
        return @IPS_SendDataToParent($this->InstanceID, $JSONString);
    }

################## DUMMYS / WOARKAROUNDS - protected

    protected function SetValueBoolean($Ident, $value)
    {
        $id = $this->GetIDForIdent($Ident);
        if (GetValueBoolean($id) <> $value)
        {
            SetValueBoolean($id, $value);
            return true;
        }
        return false;
    }

    protected function SetValueInteger($Ident, $value)
    {
        $id = $this->GetIDForIdent($Ident);
        if (GetValueInteger($id) <> $value)
        {
            SetValueInteger($id, $value);
            return true;
        }
        return false;
    }

    protected function SetValueString($Ident, $value)
    {
        $id = $this->GetIDForIdent($Ident);
        if (GetValueString($id) <> $value)
        {
            SetValueString($id, $value);
            return true;
        }
        return false;
    }

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
                    if ($Id === (int) $Kodi_Data->Id)
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
        return false;
    }

    protected function GetParent()
    {
        $instance = IPS_GetInstance($this->InstanceID);
        return ($instance['ConnectionID'] > 0) ? $instance['ConnectionID'] : false;
    }

    private function HasActiveParent()
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
        IPS_SetName($id, $Name);
        IPS_SetHidden($id, true);
        IPS_SetEventScript($id, $Script);
    }

    protected function UnregisterTimer($Name)
    {
        $id = @IPS_GetObjectIDByIdent($Name, $this->InstanceID);
        if ($id > 0)
        {
            if (IPS_EventExists($id))
                IPS_DeleteEvent($id);
        }
    }

    protected function SetTimerInterval($Name, $Interval)
    {
        $id = @IPS_GetObjectIDByIdent($Name, $this->InstanceID);
        if ($id === false)
            throw new Exception('Timer not present', E_USER_WARNING);
        if (!IPS_EventExists($id))
            throw new Exception('Timer not present', E_USER_WARNING);
        $Event = IPS_GetEvent($id);
        if ($Interval < 1)
        {
            if ($Event['EventActive'])
                IPS_SetEventActive($id, false);
        }
        else
        {
            if ($Event['CyclicTimeValue'] <> $Interval)
                IPS_SetEventCyclic($id, 0, 0, 0, 0, 1, $Interval);
            if (!$Event['EventActive'])
                IPS_SetEventActive($id, true);
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

class KodiRPCException extends Exception
{

    public function __construct($message, $code, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}

class Kodi_RPC_Data extends stdClass
{

    private $Namespace;
    private $Method;
    private $Error;
    private $Params;
    private $Result;
    private $Id;

    public function __get($name)
    {
        return $this->{$name};
    }

    /*
      public function __set($name, $value)
      {
      $this->{$name} = $value;
      } */

    public function __construct($Namespace = null, $Method = null, $Params = null, $Id = null)
    {
        if (!is_null($Namespace))
            $this->Namespace = $Namespace;
        if (!is_null($Method))
            $this->Method = $Method;
        if (is_array($Params))
            $this->Params = (object) $Params;
        if (is_object($Params))
            $this->Params = (object) $Params;
        if (is_null($Id))
            $this->Id = round(fmod(microtime(true) * 1000, 10000));
        else
        {
            if ($Id > 0)
                $this->Id = $Id;
        }
    }

    public function __call($name, $arguments)
    {
        $this->Method = $name;
//        IPS_LogMessage('ParamCall1', print_r($arguments[0], true));
        if (is_array($arguments[0]))
            $this->Params = (object) $arguments[0];
//        IPS_LogMessage('ParamCall2', print_r($this->Params, true));
        if (is_object($arguments[0]))
            $this->Params = $arguments[0];

        $this->Id = round(fmod(microtime(true) * 1000, 10000));
    }

    public function GetResult()
    {
        if (!is_null($this->Error))
        {
            return $this->GetErrorObject();
        }
        if (!is_null($this->Result))
        {
            return $this->Result;
        }
        return array();
    }

    public function GetEvent()
    {
//        IPS_LogMessage('GetEvent', print_r($this, true));
        return $this->Params->data;
    }

    private function GetErrorObject()
    {

        if (property_exists($this->Error, 'data'))
            if (property_exists($this->Error->data, 'stack'))
                if (property_exists($this->Error->data->stack, 'message'))
                    return new KodiRPCException((string) $this->Error->data->stack->message, (int) $this->Error->code);
                else
                    return new KodiRPCException((string) $this->Error->data->stack, (int) $this->Error->code);
            else
                return new KodiRPCException((string) $this->Error->data, (int) $this->Error->code);
        else
            return new KodiRPCException((string) $this->Error->message, (int) $this->Error->code);
    }

    public function GetDataFromJSONKodiObject($Data)
    {
        if (property_exists($Data, 'Error'))
            $this->Error = $Data->Error;
        if (property_exists($Data, 'Result'))
            $this->Result = $this->DecodeUTF8($Data->Result);
        if (property_exists($Data, 'Namespace'))
            $this->Namespace = $Data->Namespace;
        if (property_exists($Data, 'Method'))
            $this->Method = $Data->Method;
        if (property_exists($Data, 'Params'))
            $this->Params = $this->DecodeUTF8($Data->Params);
        if (property_exists($Data, 'Id'))
            $this->Id = $Data->Id;
    }

    public function ToKodiObjectJSONString($GUID)
    {
        $SendData = new stdClass();
        $SendData->DataID = $GUID;
        if (!is_null($this->Id))
            $SendData->Id = $this->Id;
        if (!is_null($this->Namespace))
            $SendData->Namespace = $this->Namespace;
        if (!is_null($this->Method))
            $SendData->Method = $this->Method;
        if (!is_null($this->Params))
            $SendData->Params = $this->EncodeUTF8($this->Params);
        if (!is_null($this->Error))
            $SendData->Error = $this->Error;
        if (!is_null($this->Result))
            $SendData->Result = $this->EncodeUTF8($this->Result);
        return json_encode($SendData);
    }

    public function GetDataFromJSONIPSObject($Data)
    {
        $Json = json_decode($Data);
        if (property_exists($Json, 'id'))
            $this->Id = $Json->id;
        else
            $this->Id = null;
        if (property_exists($Json, 'error'))
            $this->Error = $Json->error;
        if (property_exists($Json, 'method'))
        {
            $part = explode('.', $Json->method);
            $this->Namespace = $part[0];
            $this->Method = $part[1];
        }
        if (property_exists($Json, 'params'))
            $this->Params = $Json->params;
        if (property_exists($Json, 'result'))
            $this->Result = $Json->result;
    }

    public function ToIPSJSONString($GUID)
    {
        $RPC = new stdClass();
        $RPC->jsonrpc = "2.0";
        $RPC->method = $this->Namespace . '.' . $this->Method;
        if (!is_null($this->Params))
            $RPC->params = $this->Params;
        $RPC->id = $this->Id;
        $SendData = new stdClass;
        $SendData->DataID = $GUID;
        $SendData->Buffer = utf8_encode(json_encode($RPC));
        return json_encode($SendData);
    }

    private function DecodeUTF8($item)
    {
        if (is_string($item))
            $item = utf8_decode($item);
        else if (is_object($item))
        {
            //        $newObj = new stdClass();
            foreach ($item as $property => $value)
            {
                $item->{$property} = $this->DecodeUTF8($value);
            }
        }
        return $item;
    }

    private function EncodeUTF8($item)
    {
        if (is_string($item))
            $item = utf8_encode($item);
        else if (is_object($item))
        {
            //        $newObj = new stdClass();
            foreach ($item as $property => $value)
            {
                $item->{$property} = $this->EncodeUTF8($value);
            }
        }
        return $item;
    }

}

?>