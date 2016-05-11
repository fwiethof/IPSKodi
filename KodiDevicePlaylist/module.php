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
 * KodiDevicePlaylist Klasse für den Namespace Playlist der KODI-API.
 * Erweitert KodiBase.
 *
 */
class KodiDevicePlaylist extends KodiBase
{

    /**
     * PlaylistID für Audio
     * 
     * @access private
     * @static integer
     * @value 0
     */
    const Audio = 0;

    /**
     * PlaylistID für Video
     * 
     * @access private
     * @static integer
     * @value 1
     */
    const Video = 1;

    /**
     * PlaylistID für Bilder
     * 
     * @access private
     * @static integer
     * @value 2
     */
    const Pictures = 2;

    /**
     * RPC-Namespace
     * 
     * @access private
     *  @var string
     * @value 'Application'
     */
    static $Namespace = array('Playlist', 'Player');

    /**
     * Alle Properties des RPC-Namespace
     * 
     * @access private
     *  @var array 
     */
    /*
      static $Properties = array(
      "type",
      "partymode",
      "speed",
      "time",
      "percentage",
      "totaltime",
      "playlistid",
      "position",
      "repeat",
      "shuffled",
      "canseek",
      "canchangespeed",
      "canmove",
      "canzoom",
      "canrotate",
      "canshuffle",
      "canrepeat",
      "currentaudiostream",
      "audiostreams",
      "subtitleenabled",
      "currentsubtitle",
      "subtitles",
      "live"
      );
     */

    /**
     * Ein Teil der Properties des RPC-Namespace für Statusmeldungen
     * 
     * @access private
     *  @var array 
     */
    /*
      static $PartialProperties = array(
      "type",
      "partymode",
      "speed",
      "time",
      "percentage",
      "repeat",
      "shuffled",
      "currentaudiostream",
      "subtitleenabled",
      "currentsubtitle"
      );
     */

    /**
     * Alle Properties eines Item
     * 
     * @access private
     *  @var array 
     */
    static $ItemList = array(
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
        "director",
        "trailer",
        "tagline",
        "plot",
        "plotoutline",
        "originaltitle",
        "lastplayed",
        "writer",
        "studio",
        "mpaa",
        "cast",
        "country",
        "imdbnumber",
        "premiered",
        "productioncode",
        "runtime",
        "set",
        "showlink",
        "streamdetails",
        "top250",
        "votes",
        "firstaired",
        "season",
        "episode",
        "showtitle",
        "thumbnail",
        "file",
        "resume",
        "artistid",
        "albumid",
        "tvshowid",
        "setid",
        "watchedepisodes",
        "disc",
        "tag",
        "art",
        "genreid",
        "displayartist",
        "albumartistid",
        "description",
        "theme",
        "mood",
        "style",
        "albumlabel",
        "sorttitle",
        "episodeguide",
        "uniqueid",
        "dateadded",
        "channel",
        "channeltype",
        "hidden",
        "locked",
        "channelnumber",
        "starttime",
        "endtime");

    /**
     * Kleiner Teil der Properties eines Item
     * 
     * @access private
     *  @var array 
     */
    static $ItemListSmall = array(
        "title",
        "artist",
        "albumartist",
        "genre",
        "year",
        "album",
        "track",
        "duration",
        "plot",
        "runtime",
        "season",
        "episode",
        "showtitle",
        "thumbnail",
        "file",
        "disc",
        "albumlabel",
    );

    /**
     * Eigene PlaylistId
     * 
     * @access private
     *  @var integer Kodi-Playlist-ID dieser Instanz 
     */
    private $PlaylistId = null;

    /**
     * Interne Funktion des SDK.
     *
     * @access public
     */
    public function Create()
    {
        parent::Create();
        $this->RegisterPropertyInteger('PlaylistID', 0);
    }

    /**
     * Interne Funktion des SDK.
     * 
     * @access public
     */
    public function ApplyChanges()
    {
        $this->Init();
        switch ($this->PlaylistId)
        {
            case self::Audio:

                break;
            case self::Video:
                break;
            case self::Pictures:
                break;
        }
        parent::ApplyChanges();

        $this->RequestState('ALL');
    }

################## PRIVATE     

    /**
     * Setzt die Eigenschaften PlaylistId der Instanz
     * damit andere Funktionen Diese nutzen können
     * 
     * @access private
     */
    private function Init()
    {
        if (is_null($this->PlaylistId))
            $this->PlaylistId = $this->ReadPropertyInteger('PlaylistID');
    }

    /**
     * Werte der Eigenschaften anfragen.
     * 
     * @access protected
     * @param array $Params Enthält den Index "properties", in welchen alle anzufragenden Eigenschaften als Array enthalten sind.
     * @return boolean true bei erfolgreicher Ausführung und dekodierung, sonst false.
     */
    protected function RequestProperties(array $Params)
    {
        $this->Init();
        /*
          $Param = array_merge($Params, array("playerid" => $this->PlaylistId));
          //parent::RequestProperties($Params);
          if (!$this->isActive)
          return false;
          $KodiData = new Kodi_RPC_Data(static::$Namespace[0], 'GetProperties', $Param);
          $ret = $this->Send($KodiData);
          if (is_null($ret))
          return false;
          $this->Decode('GetProperties', $ret);
         */
        return true;
    }

    /**
     * Dekodiert die empfangenen Daten und führt die Statusvariablen nach.
     * 
     * @access protected
     * @param string $Method RPC-Funktion ohne Namespace
     * @param object $KodiPayload Der zu dekodierende Datensatz als Objekt.
     */
    protected function Decode($Method, $KodiPayload)
    {
        $this->Init();
        //prüfen ob Player oder Playlist
        //Player nur bei neuer Position
        $this->SendDebug($Method, $KodiPayload, 0);
        return;
        switch ($Method)
        {
            case 'GetProperties':
            case 'OnPropertyChanged':
                foreach ($KodiPayload as $param => $value)
                {
                    switch ($param)
                    {
                        
                    }
                }
                break;
            case 'OnStop':

                break;
            case 'OnPlay':
                break;
            case 'OnPause':
                break;
            case 'OnSeek':
                break;
            case 'OnSpeedChanged':
                break;
            default:
                $this->SendDebug($Method, $KodiPayload, 0);
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
            case "Status":
                break;
        }
    }

################## PUBLIC

    /**
     * IPS-Instanz-Funktion 'KODIPLAYER_Get'.
     * Holt sich die Daten des aktuellen wiedergegebenen Items, und gibt die Array zurück.
     * 
     * @access public
     * @return array|null Das Array mit den Eigenschaften des Item, im Fehlerfall null
     */
    public function Get()
    {
        $this->Init();
        $KodiData = new Kodi_RPC_Data(self::$Namespace[0], 'GetItems', array('playlistid' => $this->PlaylistId, 'properties' => self::$ItemListSmall));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return null;
        if ($ret->limits->total > 0)
            return json_decode(json_encode($ret->items), true);
        return array();
    }

    /**
     * IPS-Instanz-Funktion 'KODIPLAYER_GetItem'.
     * Holt sich die Daten des aktuellen wiedergegebenen Items, und gibt die Array zurück.
     * 
     * @access public
     * @return array|null Das Array mit den Eigenschaften des Item, im Fehlerfall null
     */
    private function Add(string $ItemTyp, string $ItemValue, $Ext = array())
    {

        $this->Init();
        $KodiData = new Kodi_RPC_Data(self::$Namespace[0], 'Add', array_merge(array('playlistid' => $this->PlaylistId, "item" => array($ItemTyp => $ItemValue)), $Ext));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        if ($ret === "OK")
            return true;
        trigger_error('Error on add ' . $ItemTyp . '.', E_USER_NOTICE);
        return false;
    }

    public function AddAlbum(integer $AlbumId)
    {
        if (!is_int($AlbumId))
        {
            trigger_error('AlbumId must be integer', E_USER_NOTICE);
            return false;
        }
        return $this->Add("albumid", $AlbumId);
    }

    public function AddArtist(integer $ArtistId)
    {
        if (!is_int($ArtistId))
        {
            trigger_error('ArtistId must be integer', E_USER_NOTICE);
            return false;
        }
        return $this->Add("artistid", $ArtistId);
    }

    public function AddDirectory(integer $Directory)
    {
        if (!is_string($Directory))
        {
            trigger_error('Directory must be string', E_USER_NOTICE);
            return false;
        }
        return $this->Add("directory", $Directory);
    }

    public function AddDirectoryRecursive(integer $Directory)
    {
        if (!is_int($Directory))
        {
            trigger_error('Directory must be string', E_USER_NOTICE);
            return false;
        }
        return $this->Add("Directory", $Directory, array("recursive" => true));
    }

    public function AddEpisode(integer $EpisodeId)
    {
        if (!is_int($EpisodeId))
        {
            trigger_error('EpisodeId must be integer', E_USER_NOTICE);
            return false;
        }
        return $this->Add("episodeid", $EpisodeId);
    }

    public function AddFile(integer $File)
    {
        if (!is_string($File))
        {
            trigger_error('File must be string', E_USER_NOTICE);
            return false;
        }
        return $this->Add("file", $File);
    }

    public function AddGenre(integer $GenreId)
    {
        if (!is_int($GenreId))
        {
            trigger_error('GenreId must be integer', E_USER_NOTICE);
            return false;
        }
        return $this->Add("genreid", $GenreId);
    }

    public function AddMovie(integer $MovieId)
    {
        if (!is_int($MovieId))
        {
            trigger_error('MovieId must be integer', E_USER_NOTICE);
            return false;
        }
        return $this->Add("movieid", $MovieId);
    }

    public function AddMusicVideo(integer $MusicvideoId)
    {
        if (!is_int($MusicvideoId))
        {
            trigger_error('MusicvideoId must be integer', E_USER_NOTICE);
            return false;
        }
        return $this->Add("musicvideoid", $MusicvideoId);
    }

    public function AddSong(integer $SongId)
    {
        if (!is_int($SongId))
        {
            trigger_error('SongId must be integer', E_USER_NOTICE);
            return false;
        }
        return $this->Add("songid", $SongId);
    }

    public function Clear()
    {

        $this->Init();
        $KodiData = new Kodi_RPC_Data(self::$Namespace[0], 'Clear', array('playlistid' => $this->PlaylistId));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        if ($ret === "OK")
            return true;
        trigger_error('Error on clear playlist.', E_USER_NOTICE);
        return false;
    }

    private function Insert(integer $Position, string $ItemTyp, string $ItemValue, $Ext = array())
    {
        if (!is_int($Position))
        {
            trigger_error('Position must be integer', E_USER_NOTICE);
            return false;
        }
        $this->Init();
        $KodiData = new Kodi_RPC_Data(self::$Namespace[0], 'Insert', array_merge(array('playlistid' => $this->PlaylistId, 'position' => $Position, 'item' => array($ItemTyp => $ItemValue)), $Ext));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        if ($ret === "OK")
            return true;
        trigger_error('Error on insert ' . $ItemTyp . '.', E_USER_NOTICE);
        return false;
    }

    public function InsertAlbum(integer $AlbumId, integer $Position)
    {
        if (!is_int($AlbumId))
        {
            trigger_error('AlbumId must be integer', E_USER_NOTICE);
            return false;
        }
        return $this->Insert($Position, "albumid", $AlbumId);
    }

    public function InsertArtist(integer $ArtistId, integer $Position)
    {
        if (!is_int($ArtistId))
        {
            trigger_error('ArtistId must be integer', E_USER_NOTICE);
            return false;
        }
        return $this->Insert($Position, "artistid", $ArtistId);
    }

    public function InsertDirectory(integer $Directory, integer $Position)
    {
        if (!is_string($Directory))
        {
            trigger_error('Directory must be string', E_USER_NOTICE);
            return false;
        }
        return $this->Insert($Position, "directory", $Directory);
    }

    public function InsertDirectoryRecursive(integer $Directory, integer $Position)
    {
        if (!is_int($Directory))
        {
            trigger_error('Directory must be string', E_USER_NOTICE);
            return false;
        }
        return $this->Insert($Position, "Directory", $Directory, array("recursive" => true));
    }

    public function InsertEpisode(integer $EpisodeId, integer $Position)
    {
        if (!is_int($EpisodeId))
        {
            trigger_error('EpisodeId must be integer', E_USER_NOTICE);
            return false;
        }
        return $this->Insert($Position, "episodeid", $EpisodeId);
    }

    public function InsertFile(integer $File, integer $Position)
    {
        if (!is_string($File))
        {
            trigger_error('File must be string', E_USER_NOTICE);
            return false;
        }
        return $this->Insert($Position, "file", $File);
    }

    public function InsertGenre(integer $GenreId, integer $Position)
    {
        if (!is_int($GenreId))
        {
            trigger_error('GenreId must be integer', E_USER_NOTICE);
            return false;
        }
        return $this->Insert($Position, "genreid", $GenreId);
    }

    public function InsertMovie(integer $MovieId, integer $Position)
    {
        if (!is_int($MovieId))
        {
            trigger_error('MovieId must be integer', E_USER_NOTICE);
            return false;
        }
        return $this->Insert($Position, "movieid", $MovieId);
    }

    public function InsertMusicVideo(integer $MusicvideoId, integer $Position)
    {
        if (!is_int($MusicvideoId))
        {
            trigger_error('MusicvideoId must be integer', E_USER_NOTICE);
            return false;
        }
        return $this->Insert($Position, "musicvideoid", $MusicvideoId);
    }

    public function InsertSong(integer $SongId, integer $Position)
    {
        if (!is_int($SongId))
        {
            trigger_error('SongId must be integer', E_USER_NOTICE);
            return false;
        }
        return $this->Insert($Position, "songid", $SongId);
    }

    public function Remove(integer $Position)
    {
        if (!is_int($Position))
        {
            trigger_error('Position must be integer', E_USER_NOTICE);
            return false;
        }
        $this->Init();
        $KodiData = new Kodi_RPC_Data(self::$Namespace[0], 'Remove', array('playlistid' => $this->PlaylistId, 'position' => $Position));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        if ($ret === "OK")
            return true;
        trigger_error('Error on remove item.', E_USER_NOTICE);
        return false;
    }

    public function Swap(integer $Position1, integer $Position2)
    {
        if (!is_int($Position1))
        {
            trigger_error('Position1 must be integer', E_USER_NOTICE);
            return false;
        }
        if (!is_int($Position2))
        {
            trigger_error('Position2 must be integer', E_USER_NOTICE);
            return false;
        }
        $this->Init();
        $KodiData = new Kodi_RPC_Data(self::$Namespace[0], 'Swap', array('playlistid' => $this->PlaylistId, 'position1' => $Position1, 'position2' => $Position2));
        $ret = $this->Send($KodiData);
        if (is_null($ret))
            return false;
        if ($ret === "OK")
            return true;
        trigger_error('Error on swap items.', E_USER_NOTICE);
        return false;
    }

    /**
     * IPS-Instanz-Funktion 'KODIPLAYER_RequestState'. Frage eine oder mehrere Properties ab.
     *
     * @access public
     * @param string $Ident Enthält den Names des "properties" welches angefordert werden soll.
     * @return boolean true bei erfolgreicher Ausführung, sonst false.
     */
    /*
      public function RequestState(string $Ident)
      {
      return parent::RequestState($Ident);
      }
     */
}

/** @} */
?>