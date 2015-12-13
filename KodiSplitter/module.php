<?

require_once(__DIR__ . "/../KodiClass.php");  // diverse Klassen

class KodiSplitter extends IPSModule
{

    public function Create()
    {
        parent::Create();
        $this->RequireParent("{3CFF0FD9-E306-41DB-9B5A-9D06D38576C3}", "Kodi JSONRPC TCP-Socket");
        $this->RegisterPropertyString("Host", "");
        $this->RegisterPropertyBoolean("Open", false);
        $this->RegisterPropertyInteger("Port", 9090);
        $this->RegisterPropertyInteger("Webport", 80);
//        $ID = $this->RegisterScript('PlaylistDesign', 'Playlist Config', $this->CreatePlaylistConfigScript(), -7);
//        IPS_SetHidden($ID, true);
//        $this->RegisterPropertyInteger("Playlistconfig", $ID);
    }

    public function ApplyChanges()
    {
        parent::ApplyChanges();

        // Zwangskonfiguration des ClientSocket
        $ChangeParentSetting = false;
        $Open = $this->ReadPropertyBoolean('Open');
        $NewState = IS_ACTIVE;

        if (!$Open)
            $NewState = IS_INACTIVE;

        if ($this->ReadPropertyString('Host') == '')
        {
            if ($Open)
            {
                $NewState = IS_EBASE + 2;
                $Open = false;
            }
        }

        if ($this->ReadPropertyString('Port') == '')
        {
            if ($Open)
            {
                $NewState = IS_EBASE + 2;
                $Open = false;
            }
        }
        $ParentID = $this->GetParent();

        if ($ParentID > 0)
        {
            if (IPS_GetProperty($ParentID, 'Host') <> $this->ReadPropertyString('Host'))
            {
                IPS_SetProperty($ParentID, 'Host', $this->ReadPropertyString('Host'));
                $ChangeParentSetting = true;
            }
            if (IPS_GetProperty($ParentID, 'Port') <> $this->ReadPropertyInteger('Port'))
            {
                IPS_SetProperty($ParentID, 'Port', $this->ReadPropertyInteger('Port'));
                $ChangeParentSetting = true;
            }
            // Keine Verbindung erzwingen wenn Host leer ist, sonst folgt später Exception.

            if (IPS_GetProperty($ParentID, 'Open') <> $Open)
            {
                IPS_SetProperty($ParentID, 'Open', $Open);
                $ChangeParentSetting = true;
            }
            if ($ChangeParentSetting)
                @IPS_ApplyChanges($ParentID);
        }
        // Eigene Profile
        $this->RegisterVariableString("BufferIN", "BufferIN", "", -1);
        IPS_SetHidden($this->GetIDForIdent('BufferIN'), true);
//        $this->RegisterVariableString("Nodes", "Nodes", "", -5);
//        $this->RegisterVariableString("BufferIN", "BufferIN", "", -4);
//        $this->RegisterVariableString("CommandOut", "CommandOut", "", -3);
//        IPS_SetHidden($this->GetIDForIdent('Nodes'), true);
//        IPS_SetHidden($this->GetIDForIdent('CommandOut'), true);
//        IPS_SetHidden($this->GetIDForIdent('BufferIN'), true);
//        $this->RegisterTimer('KeepAlive', 3600, 'ISCP_KeepAlive($_IPS[\'TARGET\']);');
        // Wenn wir verbunden sind,  mit Kodi, dann anmelden für Events
        if (($Open)
                and ( $this->HasActiveParent($ParentID)))
        {
            switch (IPS_GetKernelRunlevel())
            {
                case KR_READY:
                    $this->SetStatus($NewState);
                    if ($NewState == IS_ACTIVE)
                    {

                        /*                        $Data = new LMSData("listen", "1");
                          try
                          {
                          $this->SendLMSData($Data);
                          $this->RefreshPlayerList();
                          $Data = new LMSData("rescan", "?", false);
                          $this->SendLMSData($Data);
                          }
                          catch (Exception $exc)
                          {
                          trigger_error($exc->getMessage(), $exc->getCode());
                          return false;
                          }

                          $DevicesIDs = IPS_GetInstanceListByModuleID("{118189F9-DC7E-4DF4-80E1-9A4DF0882DD7}");
                          foreach ($DevicesIDs as $Device)
                          {
                          if (IPS_GetInstance($Device)['ConnectionID'] == $this->InstanceID)
                          {
                          @IPS_ApplyChanges($Device);
                          }
                          } */

                        $InstanceIDs = IPS_GetInstanceList();
                        foreach ($InstanceIDs as $IID)
                            if (IPS_GetInstance($IID)['ConnectionID'] == $this->InstanceID)
                                @IPS_ApplyChanges($IID);
                    }
                    break;
                case KR_INIT:
                    if ($NewState == IS_ACTIVE)
                        $this->SetStatus(203);
                    else
                        $this->SetStatus($NewState);
                    break;
            }
        } else
            $this->SetStatus($NewState);
    }
################## PRIVATE     

    private function RequestKodiState()
    {
        //if fKernelRunlevel <> KR_READY then exit;
        // TODO
    }

    private function DecodeData($Frame)
    {
        
    }

################## PUBLIC
    /**
     * This function will be available automatically after the module is imported with the module control.
     * Using the custom prefix this function will be callable from PHP and JSON-RPC through:
     */
    /*
      public function RequestState()
      {

      }

      public function Play()
      {
      $Params = new StdClass();
      $Params->playerid = 1;
      $KodiData = new Kodi_RPC_Data('Player');
      $KodiData->PlayPause($Params);
      $this->SendDataToParent($KodiData);
      }

      public function Pause()
      {
      $KodiData = new Kodi_RPC_Data('Player');
      $KodiData->PlayPause(array('playerid' => 1));
      $this->SendDataToParent($KodiData);
      }
     */


################## DATAPOINT RECEIVE FROM CHILD

    public function ForwardData($JSONString)
    {
        $Data = json_decode($JSONString);
        if ($Data->DataID <> "{0222A902-A6FA-4E94-94D3-D54AA4666321}")
            return false;
        $KodiData = new Kodi_RPC_Data();
        $KodiData->GetDataFromJSONKodiObject($Data);
        try
        {
            $this->ForwardDataFromDevice($KodiData);
        } catch (Exception $ex)
        {
            trigger_error($ex->getMessage(), $ex->getCode());
            return false;
        }
        return true;
    }

################## DATAPOINTS DEVICE

    private function ForwardDataFromDevice(Kodi_RPC_Data $KodiData)
    {

        try
        {
            $this->SendDataToParent($KodiData);
        } catch (Exception $ex)
        {
            throw new Exception($ex->getMessage(), $ex->getCode());
        }
    }

    private function SendDataToDevice(Kodi_RPC_Data $KodiData)
    {
//        IPS_LogMessage('SendDataToZone',print_r($APIData,true));
        $Data = $KodiData->ToKodiObjectJSONString('{73249F91-710A-4D24-B1F1-A72F216C2BDC}');
        IPS_SendDataToChildren($this->InstanceID, $Data);
    }

################## DATAPOINTS PARENT

    public function ReceiveData($JSONString)
    {
        $data = json_decode($JSONString);
        $bufferID = $this->GetIDForIdent("BufferIN");

        // Empfangs Lock setzen
        if (!$this->lock("bufferin"))
        {
            trigger_error("ReceiveBuffer is locked", E_USER_WARNING);
            return false;
        }

        // Datenstream zusammenfügen
        $head = GetValueString($bufferID);
        SetValueString($bufferID, '');

        $KodiData = new Kodi_RPC_Data();
        $Data = $head . utf8_decode($data->Buffer);

        // Stream in einzelne Pakete schneiden
        $Data = str_replace('}{', '}---{', $Data, $Count);
        $JSONLine = explode('---', $Data);

        if (is_null(json_decode($JSONLine[$Count])))
        {
            // Rest vom Stream wieder in den Empfangsbuffer schieben
            $tail = array_pop($JSONLine);
            SetValueString($bufferID, $tail);
        } else
            SetValueString($bufferID, '');

        // Empfangs Lock aufheben
        $this->unlock("bufferin");

        // Pakete verarbeiten
        foreach ($JSONLine as $JSON)
        {
            $KodiData->GetDataFromJSONIPSObject($JSON);
//            IPS_LogMessage("Kodi_rec", print_r($KodiData, true));
            $this->SendDataToDevice($KodiData);
        }

        /*
          //IPS_LogMessage('ReceiveDataFrom???:'.$this->InstanceID,  print_r($data,1));
          $bufferID = $this->GetIDForIdent("BufferIN");
          // Empfangs Lock setzen
          if (!$this->lock("ReceiveLock"))
          {
          trigger_error("ReceiveBuffer is locked", E_USER_NOTICE);
          return false;

          //            throw new Exception("ReceiveBuffer is locked",E_USER_NOTICE);
          }

          // Datenstream zusammenfügen
          $head = GetValueString($bufferID);
          SetValueString($bufferID, '');
          // Stream in einzelne Pakete schneiden
          $stream = $head . utf8_decode($data->Buffer);
          if ($this->Mode == ISCPSplitter::LAN)
          {
          $minTail = 24;

          $start = strpos($stream, 'ISCP');
          if ($start === false)
          {
          IPS_LogMessage('ISCP Gateway', 'LANFrame without ISCP');
          $stream = '';
          }
          elseif ($start > 0)
          {
          IPS_LogMessage('ISCP Gateway', 'LANFrame start not with ISCP');
          $stream = substr($stream, $start);
          }
          //Paket suchen
          if (strlen($stream) < $minTail)
          {
          IPS_LogMessage('ISCP Gateway', 'LANFrame to short');
          SetValueString($bufferID, $stream);
          $this->unlock("ReceiveLock");
          return;
          }
          $header_len = ord($stream[6]) * 256 + ord($stream[7]);
          $frame_len = ord($stream[10]) * 256 + ord($stream[11]);
          //             IPS_LogMessage('ISCP Gateway', 'LANFrame info ' . $header_len. '+'. $frame_len . ' Bytes.');
          if (strlen($stream) < $header_len + $frame_len)
          {
          IPS_LogMessage('ISCP Gateway', 'LANFrame must have ' . $header_len . '+' . $frame_len . ' Bytes. ' . strlen($stream) . ' Bytes given.');
          SetValueString($bufferID, $stream);
          $this->unlock("ReceiveLock");
          return;
          }
          $header = substr($stream, 0, $header_len);
          $frame = substr($stream, $header_len, $frame_len);
          //EOT wegschneiden von reschts, aber nur wenn es einer der letzten drei zeichen ist
          $end = strrpos($frame, chr(0x1A));
          if ($end >= $frame_len - 3)
          $frame = substr($frame, 0, $end);
          //EOT wegschneiden von reschts, aber nur wenn es einer der letzten drei zeichen ist
          $end = strrpos($frame, chr(0x0D));
          if ($end >= $frame_len - 3)
          $frame = substr($frame, 0, $end);
          //EOT wegschneiden von reschts, aber nur wenn es einer der letzten drei zeichen ist
          $end = strrpos($frame, chr(0x0A));
          if ($end >= $frame_len - 3)
          $frame = substr($frame, 0, $end);
          //                IPS_LogMessage('ISCP Gateway', 'LAN $header:' . $header);
          //                IPS_LogMessage('ISCP Gateway', 'LAN $frame:' . $frame);
          // 49 53 43 50  // ISCP
          // 00 00 00 10  // HEADERLEN
          // 00 00 00 0B  // DATALEN
          // 01 00 00 00  // Version
          // 21 31 4E 4C  // !1NL
          // 53 43 2D 50  // SC-P
          // 1A 0D 0A     // EOT CR LF
          $tail = substr($stream, $header_len + $frame_len);
          if ($this->eISCPVersion <> ord($header[12]))
          {
          $frame = false;
          trigger_error("Wrong eISCP Version", E_USER_NOTICE);
          }
          }
          else
          {
          $minTail = 6;
          $start = strpos($stream, '!');
          if ($start === false)
          {
          IPS_LogMessage('ISCP Gateway', 'eISCP Frame without !');
          $stream = '';
          }
          elseif ($start > 0)
          {
          IPS_LogMessage('ISCP Gateway', 'eISCP Frame do not start with !');
          $stream = substr($stream, $start);
          }
          //Paket suchen
          $end = strpos($stream, chr(0x1A));
          if (($end === false) or ( strlen($stream) < $minTail)) // Kein EOT oder zu klein
          {
          IPS_LogMessage('ISCP Gateway', 'eISCP Frame to short');
          SetValueString($bufferID, $stream);
          $this->unlock("ReceiveLock");
          return;
          }
          $frame = substr($stream, $start, $end - $start);
          // Ende wieder in den Buffer werfen
          $tail = ltrim(substr($stream, $end));
          }
          if ($tail === false)
          $tail = '';
          SetValueString($bufferID, $tail);
          $this->unlock("ReceiveLock");
          if ($frame !== false)
          $this->DecodeData($frame);
          // Ende war länger als 6 / 23 ? Dann nochmal Packet suchen.
          if (strlen($tail) >= $minTail)
          $this->ReceiveData(json_encode(array('Buffer' => '')));
         * 
         */
        return true;
    }

    protected function SendDataToParent($Data)
    {
//        IPS_LogMessage('SendDataToSerialPort:'.$this->InstanceID,$Data);
        //Parent ok ?
        if (!$this->HasActiveParent())
            throw new Exception("Instance has no active Parent.", E_USER_NOTICE);

//        IPS_LogMessage('Kodi_send', print_r($Data, true));
        $JsonString = $Data->ToIPSJSONString('{79827379-F36E-4ADA-8A95-5F8D1DC92FA9}');
        $ret = IPS_SendDataToParent($this->InstanceID, $JsonString);
//        IPS_LogMessage('Kodi_ret', print_r($ret, true));
        // Frame bauen
        /*        if ($this->Mode == ISCPSplitter::LAN)
          {
          $eISCPlen = chr(0x00) . chr(0x00) . chr(floor(strlen($Data) / 256)) . chr(strlen($Data) % 256);
          $Frame = $eISCPlen . chr($this->eISCPVersion) . chr(0x00) . chr(0x00) . chr(0x00);
          $Len = strlen($Frame) + 8;
          $eISCPHeaderlen = chr(0x00) . chr(0x00) . chr(floor($Len / 256)) . chr($Len % 256);
          $Frame = "ISCP" . $eISCPHeaderlen . $Frame . $Data;
          }
          elseif ($this->Mode == ISCPSplitter::COM)
          {
          $Frame = $Data;
          }
          else
          {
          throw new Exception("Wrong IO-Parent.", E_USER_WARNING);
          }


          //Semaphore setzen
          if (!$this->lock("ToParent"))
          {
          throw new Exception("Can not send to Parent", E_USER_NOTICE);
          }
          // Daten senden
          try
          {
          IPS_SendDataToParent($this->InstanceID, json_encode(Array("DataID" => "{79827379-F36E-4ADA-8A95-5F8D1DC92FA9}", "Buffer" => utf8_encode($Frame))));
          }
          catch (Exception $exc)
          {
          // Senden fehlgeschlagen
          $this->unlock("ToParent");
          throw new Exception($exc);
          }
          $this->unlock("ToParent");
         * 
         */
        return true;
    }

################## DUMMYS / WOARKAROUNDS - protected

    protected function GetParent()
    {
        $instance = IPS_GetInstance($this->InstanceID);
        return ($instance['ConnectionID'] > 0) ? $instance['ConnectionID'] : false;
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

    protected function RequireParent($ModuleID, $Name = '')
    {

        $instance = IPS_GetInstance($this->InstanceID);
        if ($instance['ConnectionID'] == 0)
        {

            $parentID = IPS_CreateInstance($ModuleID);
            $instance = IPS_GetInstance($parentID);
            if ($Name == '')
                IPS_SetName($parentID, $instance['ModuleInfo']['ModuleName']);
            else
                IPS_SetName($parentID, $Name);
            IPS_ConnectInstance($this->InstanceID, $parentID);
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
            } else
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
            if (!IPS_EventExists($id))
                throw new Exception('Timer not present', E_USER_WARNING);
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

    protected function SetStatus($InstanceStatus)
    {
        if ($InstanceStatus <> IPS_GetInstance($this->InstanceID)['InstanceStatus'])
            parent::SetStatus($InstanceStatus);
    }

    protected function SetSummary($data)
    {
//        IPS_LogMessage(__CLASS__, __FUNCTION__ . "Data:" . $data); //                   
    }

################## SEMAPHOREN Helper  - private  

    private function lock($ident)
    {
        for ($i = 0; $i < 100; $i++)
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