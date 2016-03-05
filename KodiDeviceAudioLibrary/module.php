<?

require_once(__DIR__ . "/../KodiClass.php");  // diverse Klassen

class KodiDeviceAudioLibrary extends KodiBase
{

    static $Namespace = 'AudioLibrary';
    static $Properties = array(
    );
    static $AlbumItemList = array("title",
        "description",
        "artist",
        "genre",
        "theme",
        "mood",
        "style",
        "type",
        "albumlabel",
        "rating",
        "year",
        "musicbrainzalbumid",
        "musicbrainzalbumartistid",
        "fanart",
        "thumbnail",
        "playcount",
        "genreid",
        "artistid",
        "displayartist");
    static $AlbumItemListSmall = array(
        "title",
        "artist",
        "displayartist",
        "description",
        "genre",
        "type",
        "albumlabel",
        "year",
        "thumbnail"
    );
    static $ArtistItemList = array(
      "instrument",
      "style",
      "mood",
      "born",
      "formed",
      "description",
      "genre",
      "died",
      "disbanded",
      "yearsactive",
      "musicbrainzartistid",
      "fanart",
      "thumbnail");

    public function Create()
    {
        parent::Create();
    }

    public function ApplyChanges()
    {
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

        parent::ApplyChanges();
    }

################## PRIVATE     

    protected function Decode($Method, $KodiPayload)
    {
        foreach ($KodiPayload as $param => $value)
        {
            switch ($param)
            {
                /*                case "mute":
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
                 */
            }
        }
    }

################## ActionHandler

    public function RequestAction($Ident, $Value)
    {
        switch ($Ident)
        {
            /*
              case "mute":
              return $this->Mute($Value);
              case "volume":
              return $this->Volume($Value);
              case "quit":
              return $this->Quit();
              default:
              return trigger_error('Invalid Ident.', E_USER_NOTICE);
             * 
             */
        }
    }

################## PUBLIC
    /**
     * This function will be available automatically after the module is imported with the module control.
     * Using the custom prefix this function will be callable from PHP and JSON-RPC through:
     */
    /*    public function RawSend(string $Namespace, string $Method, $Params)
      {
      return parent::RawSend($Namespace, $Method, $Params);
      } */

    /**
     * IPS-Instanz-Funktion 'KODIAUDIOLIB_Clean'. Startet das bereinigen der Datenbank
     *
     * @access public
     * @return boolean true bei erfolgreicher Ausführung, sonst false.
     */
    public function Clean()
    {
        $KodiData = new Kodi_RPC_Data(self::$Namespace);
        $KodiData->Clean();
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        return $ret === "OK";
    }

    /**
     * IPS-Instanz-Funktion 'KODIAUDIOLIB_Export'. Exportiert die Audio Datenbank.
     *
     * @access public
     * @param  string $Path Ziel-Verzeichnis für den Export.
     * @param boolean $Overwrite Vorhandene Daten überschreiben.
     * @param boolean $includeImages Bilder mit exportieren.
     * @return boolean true bei erfolgreicher Ausführung, sonst false.
     */
    public function Export(string $Path, boolean $Overwrite, boolean $includeImages)
    {
        if (!is_string($Path) or ( strlen($Path) < 2))
        {
            trigger_error('Path is invalid', E_USER_NOTICE);
            return false;
        }
        if (!is_bool($Overwrite))
        {
            trigger_error('Overwrite must be boolean', E_USER_NOTICE);
            return false;
        }
        if (!is_bool($includeImages))
        {
            trigger_error('includeImages must be boolean', E_USER_NOTICE);
            return false;
        }
        $KodiData = new Kodi_RPC_Data(self::$Namespace);
        $KodiData->Export(array("options" => array("path" => $Path, "overwrite" => $Overwrite, "images" => $includeImages)));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        return $ret === "OK";
    }

    /**
     * IPS-Instanz-Funktion 'KODIAUDIOLIB_GetAlbumDetails'. Liest die Eigenschaften eines Album aus.
     *
     * @access public
     * @param  integer $AlbumID AlbumID des zu lesenden Alben.
     * @return array | boolean Array mit den Daten oder false bei Fehlern.
     */
    public function GetAlbumDetails(integer $AlbumID)
    {
        if (!is_int($AlbumID))
        {
            trigger_error('AlbumID must be integer', E_USER_NOTICE);
            return false;
        }

        $KodiData = new Kodi_RPC_Data(self::$Namespace); //, 'GetFileDetails', array("file" => $File, "media" => $Media, "properties" => static::$ItemListFull));
        $KodiData->GetAlbumDetails(array("albumid" => $AlbumID, "properties" => static::$AlbumItemList));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;

        return json_decode(json_encode($ret->albumdetails), true);
    }

    /**
     * IPS-Instanz-Funktion 'KODIAUDIOLIB_GetAlbums'. Liest die Eigenschaften aller Alben aus.
     *
     * @access public
     * @return array | boolean Array mit den Daten oder false bei Fehlern.
     */
    public function GetAlbums()
    {
        $KodiData = new Kodi_RPC_Data(self::$Namespace); //, 'GetFileDetails', array("file" => $File, "media" => $Media, "properties" => static::$ItemListFull));
        $KodiData->GetAlbums(array("properties" => static::$AlbumItemListSmall));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;

        return json_decode(json_encode($ret->albums), true);
    }

    /**
     * IPS-Instanz-Funktion 'KODIAUDIOLIB_GetArtistDetails'. Liest die Eigenschaften eines Künstlers aus.
     *
     * @access public
     * @param  integer $ArtistID ArtistID des zu lesenden Künstlers.
     * @return array | boolean Array mit den Daten oder false bei Fehlern.
     */
    public function GetArtistDetails(integer $ArtistID)
    {
        if (!is_int($ArtistID))
        {
            trigger_error('AlbumID must be integer', E_USER_NOTICE);
            return false;
        }

        $KodiData = new Kodi_RPC_Data(self::$Namespace); //, 'GetFileDetails', array("file" => $File, "media" => $Media, "properties" => static::$ItemListFull));
        $KodiData->GetArtistDetails(array("artistid" => $ArtistID, "properties" => static::$ArtistItemList));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;

        return json_decode(json_encode($ret->artistdetails), true);
    }

    /**
     * IPS-Instanz-Funktion 'KODIAUDIOLIB_GetArtists'. Liest die Eigenschaften aller Künstler aus.
     *
     * @access public
     * @return array | boolean Array mit den Daten oder false bei Fehlern.
     */
    public function GetArtists()
    {
        $KodiData = new Kodi_RPC_Data(self::$Namespace); //, 'GetFileDetails', array("file" => $File, "media" => $Media, "properties" => static::$ItemListFull));
        $KodiData->GetArtists(array("properties" => static::$ArtistItemList));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;

        return json_decode(json_encode($ret->artists), true);
    }

        /**
     * IPS-Instanz-Funktion 'KODIAUDIOLIB_GetRecentlyAddedAlbums'. Liest die Eigenschaften der zuletzt hinzugefügten Alben aus.
     *
     * @access public
     * @return array | boolean Array mit den Daten oder false bei Fehlern.
     */
    public function GetRecentlyAddedAlbums()
    {
        $KodiData = new Kodi_RPC_Data(self::$Namespace); //, 'GetFileDetails', array("file" => $File, "media" => $Media, "properties" => static::$ItemListFull));
        $KodiData->GetRecentlyAddedAlbums(array("properties" => static::$AlbumItemListSmall));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;

        return json_decode(json_encode($ret->albums), true);
    }
    
// GetRecentlyAddedSongs
    
        /**
     * IPS-Instanz-Funktion 'KODIAUDIOLIB_GetRecentlyPlayedAlbums'. Liest die Eigenschaften der zuletzt abgespielten Alben aus.
     *
     * @access public
     * @return array | boolean Array mit den Daten oder false bei Fehlern.
     */
    public function GetRecentlyPlayedAlbums()
    {
        $KodiData = new Kodi_RPC_Data(self::$Namespace); //, 'GetFileDetails', array("file" => $File, "media" => $Media, "properties" => static::$ItemListFull));
        $KodiData->GetRecentlyPlayedAlbums(array("properties" => static::$AlbumItemListSmall));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;

        return json_decode(json_encode($ret->albums), true);
    } 
    
    // GetRecentlyPlayedSongs
    
    // GetSongDetails
    
    // GetSongs
    /*
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
     */

    public function RequestState(string $Ident)
    {
        return parent::RequestState($Ident);
    }

################## Datapoints

    /* public function ReceiveData($JSONString)
      {
      return parent::ReceiveData($JSONString);
      } */
    /*
      protected function Send(Kodi_RPC_Data $KodiData)
      {
      return parent::Send($KodiData);
      }

      protected function SendDataToParent($Data)
      {
      return parent::SendDataToParent($Data);
      } */
}

?>