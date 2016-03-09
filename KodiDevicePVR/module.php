<?

require_once(__DIR__ . "/../KodiClass.php");  // diverse Klassen
/*
 * @addtogroup kodi
 * @{
 *
 * @package       Kodi
 * @file          module.php
 * @author        Michael Tröger
 *
 */

/**
 * KodiDevicePVR Klasse für den Namespace PVR der KODI-API.
 * Erweitert KodiBase.
 *
 */
class KodiDevicePVR extends KodiBase
{

    /**
     * RPC-Namespace
     * 
     * @access private
     *  @var string
     * @value 'PVR'
     */
    static $Namespace = 'PVR';

    /**
     * Alle Properties des RPC-Namespace
     * 
     * @access private
     *  @var array 
     */
    static $Properties = array(
        "available",
        "recording",
        "scanning"
    );

    /**
     * Alle Eigenschaften von Kanal-Items.
     * 
     * @access private
     *  @var array 
     */
    static $ItemList = array(
        "thumbnail",
        "channeltype",
        "hidden",
        "locked",
        "channel",
        "lastplayed"
    );

    /**
     * Interne Funktion des SDK.
     *
     * @access public
     */
    public function Create()
    {
        parent::Create();
        $this->RegisterPropertyBoolean("showIsAvailable", true);
        $this->RegisterPropertyBoolean("showIsRecording", true);
        $this->RegisterPropertyBoolean("showDoRecording", true);
        $this->RegisterPropertyBoolean("showIsScanning", true);
        $this->RegisterPropertyBoolean("showDoScanning", true);
    }

    /**
     * Interne Funktion des SDK.
     * 
     * @access public
     */
    public function ApplyChanges()
    {
        $this->RegisterProfileIntegerEx("Action.Kodi", "", "", "", Array(
            Array(0, "Ausführen", "", -1)
        ));
        if ($this->ReadPropertyBoolean('showIsAvailable'))
            $this->RegisterVariableBoolean("available", "Verfügbar", "", 1);
        else
            $this->UnregisterVariable("available");

        if ($this->ReadPropertyBoolean('showIsRecording'))
            $this->RegisterVariableBoolean("recording", "Aufnahame läuft", "", 3);
        else
            $this->UnregisterVariable("recording");

        if ($this->ReadPropertyBoolean('showDoRecording'))
        {
            $this->RegisterVariableBoolean("record", "Aufnahame aktueller Kanal", "~Switch", 4);
            $this->EnableAction("record");
        }
        else
            $this->UnregisterVariable("record");

        if ($this->ReadPropertyBoolean('showIsScanning'))
            $this->RegisterVariableBoolean("scanning", "Kanalsuche aktiv", "", 5);
        else
            $this->UnregisterVariable("scanning");

        if ($this->ReadPropertyBoolean('showDoScanning'))
        {
            $this->RegisterVariableInteger("scan", "Kanalsuche starten", "Action.Kodi", 6);
            $this->EnableAction("scan");
        }
        else
            $this->UnregisterVariable("scan");
        parent::ApplyChanges();
    }

################## PRIVATE     

    /**
     * Dekodiert die empfangenen Events und Anworten auf 'GetProperties'.
     *
     * @param string $Method RPC-Funktion ohne Namespace
     * @param object $KodiPayload Der zu dekodierende Datensatz als Objekt.
     */
    protected function Decode($Method, $KodiPayload)
    {
        switch ($Method)
        {
            case "GetProperties":
                foreach ($KodiPayload as $param => $value)
                {
                    $this->SetValueBoolean($param, $value);
                }
                break;
            default:
                ob_start();
                var_dump($KodiPayload);
                $dump = ob_get_clean();
                IPS_LogMessage('KODI_Event:' . $Method, $dump);

                break;
        }
    }

################## ActionHandler

    /**
     * Actionhandler der Statusvariablen. Interne SDK-Funktion.
     * 
     * @access public
     * @param string $Ident Der Ident der Statusvariable.
     * @param boolean|float|integer|string $Value Der angeforderte neue Wert.
     */
    public function RequestAction($Ident, $Value)
    {
        switch ($Ident)
        {
            case "scan":
                if ($this->Scan() === false)
                    trigger_error('Error start scan', E_USER_NOTICE);
                break;
            case "record":
                if ($this->Record($Value, "current") === false)
                    trigger_error('Error start recording', E_USER_NOTICE);
                break;
            default:
                trigger_error('Invalid Ident.', E_USER_NOTICE);
        }
    }

################## PUBLIC

    /**
     * IPS-Instanz-Funktion 'KODIPVR_Scan'. Startet einen Suchlauf.
     *
     * @access public
     * @return boolean true bei erfolgreicher Ausführung, sonst false.
     */
    public function Scan()
    {
        $KodiData = new Kodi_RPC_Data(self::$Namespace);
        $KodiData->Scan();
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        return $ret === "OK";
    }

    /**
     * IPS-Instanz-Funktion 'KODIAPP_Record'. Startet/Beendet eine Aufnahme.
     *
     * @access public
     * @param boolean $Record True für starten, false zum stoppen.
     * @param string $Channel Kanalname.
     * @return boolean true bei erfolgreicher Ausführung, sonst false.
     */
    public function Record(boolean $Record, string $Channel)
    {
        $KodiData = new Kodi_RPC_Data(self::$Namespace);
        $KodiData->Record(array("record" => $Record, "channel" => $Channel));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        return $ret === "OK";
    }

    /**
     * IPS-Instanz-Funktion 'KODIAPP_GetChannels'. Liest die Kanalliste
     *
     * @access public
     * @param string $ChannelTyp [enum "tv", "radio"] Kanaltyp welcher gelesen werden soll.
     * @return array|boolean Ein Array mit den Daten oder FALSE bei Fehler.
     */
    public function GetChannels(string $ChannelTyp)
    {
        if (!in_array($ChannelTyp, array("radio", "tv")))
        {
            trigger_error("ChannelTyp must 'tv' or 'radio'.", E_USER_NOTICE);
            return false;
        }

        $KodiData = new Kodi_RPC_Data(self::$Namespace);
        $KodiData->GetChannels(array("channelgroupid" => "all" . $ChannelTyp, "properties" => static::$ItemList));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        if ($ret->limits->total > 0)
            return json_decode(json_encode($ret->channels), true);
        return array();
    }

    /**
     * IPS-Instanz-Funktion 'KODIAPP_GetChannelDetails'. Liefert die Eigenschaften eines Kanals.
     *
     * @access public
     * @param integer $ChannelId Kanal welcher gelesen werden soll.
     * @return array|boolean Ein Array mit den Daten oder FALSE bei Fehler.
     */
    public function GetChannelDetails(integer $ChannelId)
    {
        if (!is_int($ChannelId))
        {
            trigger_error("ChannelId must be integer.", E_USER_NOTICE);
            return false;
        }

        $KodiData = new Kodi_RPC_Data(self::$Namespace);
        $KodiData->GetChannelDetails(array("channelid" => $ChannelId, "properties" => static::$ItemList));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        if ($ret->limits->total > 0)
            return json_decode(json_encode($ret->channeldetails), true);
        return array();
    }

    /**
     * IPS-Instanz-Funktion 'KODIAPP_GetChannelGroups'. Liest alle Kanalgruppen.
     *
     * @access public
     * @param string $ChannelTyp [enum "tv", "radio"] Kanaltyp welcher gelesen werden soll.
     * @return array|boolean Ein Array mit den Daten oder FALSE bei Fehler.
     */
    public function GetChannelGroups(string $ChannelTyp)
    {
        if (!in_array($ChannelTyp, array("radio", "tv")))
        {
            trigger_error("ChannelTyp must 'tv' or 'radio'.", E_USER_NOTICE);
            return false;
        }

        $KodiData = new Kodi_RPC_Data(self::$Namespace);
        $KodiData->GetChannelGroups(array("channeltype" => "all" . $ChannelTyp)); //, "properties" => static::$ItemList));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        if ($ret->limits->total > 0)
            return json_decode(json_encode($ret->channelgroups), true);
        return array();
    }

    /**
     * IPS-Instanz-Funktion 'KODIAPP_GetChannelGroupDetails'. Liefert die Eigenschaften einer Kanalgruppe.
     *
     * @access public
     * @param integer $ChannelGroupdId Kanal welcher gelesen werden soll.
     * @return array|boolean Ein Array mit den Daten oder FALSE bei Fehler.
     */
    public function GetChannelGroupDetails(integer $ChannelGroupdId)
    {
        if (!is_int($ChannelId))
        {
            trigger_error("ChannelId must be integer.", E_USER_NOTICE);
            return false;
        }

        $KodiData = new Kodi_RPC_Data(self::$Namespace);
        $KodiData->GetChannelGroupDetails(array("channelgroupid" => $ChannelGroupdId, "properties" => static::$ItemList));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        if ($ret->limits->total > 0)
            return json_decode(json_encode($ret->channelgroupdetails), true);
        return array();
    }

    // 

    /**
     * IPS-Instanz-Funktion 'KODIPVR_RequestState'. Frage eine oder mehrere Properties eines Namespace ab.
     *
     * @access public
     * @param string $Ident Enthält den Names des "properties" welches angefordert werden soll.
     * @return boolean true bei erfolgreicher Ausführung, sonst false.
     */
    public function RequestState(string $Ident)
    {
        return parent::RequestState($Ident);
    }

}

/** @} */
?>