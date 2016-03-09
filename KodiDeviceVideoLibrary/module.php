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
 * KodiDeviceVideoLibrary Klasse für den Namespace VideoLibrary der KODI-API.
 * Erweitert KodiBase.
 *
 */
class KodiDeviceVideoLibrary extends KodiBase
{

    /**
     * RPC-Namespace
     * 
     * @access private
     *  @var string
     * @value 'VideoLibrary'
     */
    static $Namespace = 'VideoLibrary';

    /**
     * Alle Properties des RPC-Namespace
     * 
     * @access private
     *  @var array 
     */
    static $Properties = array(
    );

    /**
     * Alle Eigenschaften eines .....
     * 
     * @access private
     *  @var array 
     */
    static $ItemList = array(
    );

    /**
     * Interne Funktion des SDK.
     *
     * @access public
     */
    public function Create()
    {
        parent::Create();
        $this->RegisterPropertyBoolean("showDoScan", true);
        $this->RegisterPropertyBoolean("showDoClean", true);
        $this->RegisterPropertyBoolean("showScan", true);
        $this->RegisterPropertyBoolean("showClean", true);
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

        if ($this->ReadPropertyBoolean('showDoScan'))
        {
            $this->RegisterVariableInteger("doscan", "Suche nach neuen / veränderten Inhalten", "Action.Kodi", 1);
            $this->EnableAction("doscan");
        }
        else
            $this->UnregisterVariable("doscan");

        if ($this->ReadPropertyBoolean('showScan'))
            $this->RegisterVariableBoolean("scan", "Datenbanksuche läuft", "~Switch", 2);
        else
            $this->UnregisterVariable("scan");

        if ($this->ReadPropertyBoolean('showDoClean'))
        {
            $this->RegisterVariableInteger("doclean", "Bereinigen der Datenbank", "Action.Kodi", 3);
            $this->EnableAction("doclean");
        }
        else
            $this->UnregisterVariable("doclean");

        if ($this->ReadPropertyBoolean('showClean'))
            $this->RegisterVariableBoolean("clean", "Bereinigung der Datenbank läuft", "~Switch", 4);
        else
            $this->UnregisterVariable("clean");

        parent::ApplyChanges();
    }

################## PRIVATE     

    protected function Decode($Method, $KodiPayload)
    {
        switch ($Method)
        {
            case "OnScanStarted":
                $this->SetValueBoolean("scan", true);
                break;
            case "OnScanFinished":
                $this->SetValueBoolean("scan", false);
                break;
            case "OnCleanStarted":
                $this->SetValueBoolean("clean", true);
                break;
            case "OnCleanFinished":
                $this->SetValueBoolean("clean", false);
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
            case "doclean":
                if ($this->Clean() === false)
                    trigger_error('Error start cleaning', E_USER_NOTICE);
                break;
            case "doscan":
                if ($this->Scan() === false)
                    trigger_error('Error start scanning', E_USER_NOTICE);
                break;

            default:
                trigger_error('Invalid Ident.', E_USER_NOTICE);
        }
    }

################## PUBLIC
}

/** @} */
?>