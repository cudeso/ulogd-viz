<?php
/**
 *  ulogd visualizer
 *
 *  Configuration loader
 *
 *  @author Koen Van Impe <koen.vanimpe@vanimpe.eu>
 *  @package  ulogdviz
 * 
 */

if (isset($_SERVER["DOCUMENT_ROOT"]) and strlen($_SERVER["DOCUMENT_ROOT"]) > 0) {
    define('__ROOT__', $_SERVER["DOCUMENT_ROOT"] . "/ulogd-viz/");
}
else {
    define('__ROOT__', $_SERVER["PWD"] . "/../");    
}

try {
  foreach( glob( __ROOT__ . "library/*.php") as $filename) { require_once $filename; }
  require_once "Net/GeoIP.php";

  $configuration = parse_ini_file("ulogd.ini", true);

}
catch (Exception $e) {  
    throwException($e);
}

define('APP_TITLE', $configuration["application"]["title"]);
define('APP_VERSION', $configuration["application"]["version"]);

define('APP_WEBROOT', $configuration["application"]["webroot"]);

define('DEFAULT_TIMEFRAME', $configuration["defaults"]["timeframe"]);
define('DEFAULT_TIMEFRAME_TABLE', $configuration["defaults"]["timeframe_table"]);
define('DEFAULT_MAXFILTER', $configuration["defaults"]["maxfilters"]);
define('DEFAULT_MAXRECORDS_DB', $configuration["defaults"]["maxrecords_db"]);
define('DEFAULT_CHART_OPTIONSNAME', $configuration["defaults"]["chart_optionsname"]);
define('DEFAULT_MAP_MAXMARKERS', $configuration["defaults"]["map_maxmarkers"]);
define('DEFAULT_JSON_OPTIONSNAME', $configuration["defaults"]["json_optionsname"]);
define('DEFAULT_CSVEXPORT', $configuration["defaults"]["csvexport"]);
define('DEFAULT_BLACKLIST', $configuration["defaults"]["blacklist"]);
define('DEFAULT_CLEANUPTIME', $configuration["defaults"]["cleanuptime"]);
define('DEFAULT_DATEFORMAT_SHORT', $configuration["defaults"]["dateformat_short"]);
define('DEFAULT_DATEFORMAT_LONG', $configuration["defaults"]["dateformat_long"]);


define('GEOIP_DATABASE', $configuration["geoip"]["database"]);
define('GOOGLEMAPS_API', $configuration["geoip"]["googlemaps"]);
define('GEOIP_DEFAULT_LATITUDE', $configuration["geoip"]["home_latitude"]);
define('GEOIP_DEFAULT_LONGITUDE', $configuration["geoip"]["home_longitude"]);

define('DB_USERNAME', $configuration["database"]["username"]);
define('DB_PASSWORD', $configuration["database"]["password"]);
define('DB_DATABASE', $configuration["database"]["database"]);
define('DB_HOST', $configuration["database"]["host"]);
define('DB_TABLE', $configuration["database"]["ulogtable"]);


define('APP_UNKNOWN', 'Unknown');
?>