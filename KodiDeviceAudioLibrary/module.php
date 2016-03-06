<?

require_once(__DIR__ . "/../KodiClass.php");  // diverse Klassen

class KodiDeviceAudioLibrary extends KodiBase
{

    static $Namespace = 'AudioLibrary';
    static $Properties = array(
    );
    static $AlbumItemList = array(
        "theme",
        "description",
        "type",
        "style",
        "playcount",
        "albumlabel",
        "mood",
        "displayartist",
        "artist",
        "genreid",
        "musicbrainzalbumartistid",
        "year",
        "rating",
        "artistid",
        "title",
        "musicbrainzalbumid",
        "genre",
        "fanart",
        "thumbnail"
    );
    static $AlbumItemListSmall = array(
        "playcount",
        "albumlabel",
        "displayartist",
        "year",
        "rating",
        "title",
        "fanart",
        "thumbnail"
    );
    static $ArtistItemList = array(
        "born",
        "formed",
        "died",
        "style",
        "yearsactive",
        "mood",
        "musicbrainzartistid",
        "disbanded",
        "description",
        "instrument",
        "genre",
        "fanart",
        "thumbnail"
    );
    static $GenreItemList = array(
        "thumbnail",
        "title",
        "genreid"
    );
    static $SongItemList = array(
        "title",
        "artist",
        "albumartist",
        "genre",
        "year",
        "rating",
        "album",
        "track",
        "duration",
        "comment",
        "lyrics",
        "musicbrainztrackid",
        "musicbrainzartistid",
        "musicbrainzalbumid",
        "musicbrainzalbumartistid",
        "playcount",
        "fanart",
        "thumbnail",
        "file",
        "albumid",
        "lastplayed",
        "disc",
        "genreid",
        "artistid",
        "displayartist",
        "albumartistid"
    );

    public function Create()
    {
        parent::Create();
        $this->RegisterPropertyBoolean("showDoScan", true);
        $this->RegisterPropertyBoolean("showDoClean", true);
        $this->RegisterPropertyBoolean("showScan", true);
        $this->RegisterPropertyBoolean("showClean", true);
    }

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
    /**
     * This function will be available automatically after the module is imported with the module control.
     * Using the custom prefix this function will be callable from PHP and JSON-RPC through:
     */

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

        $KodiData = new Kodi_RPC_Data(self::$Namespace);
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
        $KodiData = new Kodi_RPC_Data(self::$Namespace);
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

        $KodiData = new Kodi_RPC_Data(self::$Namespace);
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
        $KodiData = new Kodi_RPC_Data(self::$Namespace);
        $KodiData->GetArtists(array("properties" => static::$ArtistItemList));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;

        return json_decode(json_encode($ret->artists), true);
    }

    /**
     * IPS-Instanz-Funktion 'KODIAUDIOLIB_GetGenres'. Liest die Eigenschaften aller Genres aus.
     *
     * @access public
     * @return array | boolean Array mit den Daten oder false bei Fehlern.
     */
    public function GetGenres()
    {
        $KodiData = new Kodi_RPC_Data(self::$Namespace);
        $KodiData->GetGenres(array("properties" => static::$GenreItemList));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;

        return json_decode(json_encode($ret->genres), true);
    }

    /**
     * IPS-Instanz-Funktion 'KODIAUDIOLIB_GetRecentlyAddedAlbums'. Liest die Eigenschaften der zuletzt hinzugefügten Alben aus.
     *
     * @access public
     * @return array | boolean Array mit den Daten oder false bei Fehlern.
     */
    public function GetRecentlyAddedAlbums()
    {
        $KodiData = new Kodi_RPC_Data(self::$Namespace);
        $KodiData->GetRecentlyAddedAlbums(array("properties" => static::$AlbumItemList));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;

        return json_decode(json_encode($ret->albums), true);
    }

    /**
     * IPS-Instanz-Funktion 'KODIAUDIOLIB_GetRecentlyAddedSongs'. Liest die Eigenschaften der zuletzt hinzugefügten Songs aus.
     *
     * @access public
     * @return array | boolean Array mit den Daten oder false bei Fehlern.
     */
    public function GetRecentlyAddedSongs()
    {
        $KodiData = new Kodi_RPC_Data(self::$Namespace);
        $KodiData->GetRecentlyAddedSongs(array("properties" => static::$AlbumItemList));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;

        return json_decode(json_encode($ret->songs), true);
    }

    /**
     * IPS-Instanz-Funktion 'KODIAUDIOLIB_GetRecentlyPlayedAlbums'. Liest die Eigenschaften der zuletzt abgespielten Alben aus.
     *
     * @access public
     * @return array | boolean Array mit den Daten oder false bei Fehlern.
     */
    public function GetRecentlyPlayedAlbums()
    {
        $KodiData = new Kodi_RPC_Data(self::$Namespace);
        $KodiData->GetRecentlyPlayedAlbums(array("properties" => static::$AlbumItemList));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;

        return json_decode(json_encode($ret->albums), true);
    }

    /**
     * IPS-Instanz-Funktion 'KODIAUDIOLIB_GetRecentlyPlayedSongs'. Liest die Eigenschaften der zuletzt abgespielten Songs aus.
     *
     * @access public
     * @return array | boolean Array mit den Daten oder false bei Fehlern.
     */
    public function GetRecentlyPlayedSongs()
    {
        $KodiData = new Kodi_RPC_Data(self::$Namespace);
        $KodiData->GetRecentlyPlayedSongs(array("properties" => static::$SongItemList));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;

        return json_decode(json_encode($ret->songs), true);
    }

    /**
     * IPS-Instanz-Funktion 'KODIAUDIOLIB_GetSongDetails'. Liest die Eigenschaften eines Künstlers aus.
     *
     * @access public
     * @param  integer $SongID SongID des zu lesenden Songs.
     * @return array | boolean Array mit den Daten oder false bei Fehlern.
     */
    public function GetSongDetails(integer $SongID)
    {
        if (!is_int($SongID))
        {
            trigger_error('SongID must be integer', E_USER_NOTICE);
            return false;
        }

        $KodiData = new Kodi_RPC_Data(self::$Namespace);
        $KodiData->GetSongDetails(array("songid" => $SongID, "properties" => static::$SongItemList));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;

        return json_decode(json_encode($ret->songdetails), true);
    }

    /**
     * IPS-Instanz-Funktion 'KODIAUDIOLIB_GetSongs'. Liest die Eigenschaften aller Songs aus.
     *
     * @access public
     * @return array | boolean Array mit den Daten oder false bei Fehlern.
     */
    public function GetSongs()
    {
        $KodiData = new Kodi_RPC_Data(self::$Namespace);
        $KodiData->GetSongs(array("properties" => static::$SongItemList));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;

        return json_decode(json_encode($ret->songs), true);
    }

    /**
     * IPS-Instanz-Funktion 'KODIAUDIOLIB_Scan'. Startet das Scannen der Quellen für neue Einträge in der Datenbank.
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

    public function RequestState(string $Ident)
    {
        return parent::RequestState($Ident);
    }

}

?>