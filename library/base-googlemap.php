<?php
/**
 *  ulogd visualizer
 *
 *  Helper classes for the Google Map generation
 *
 *  @author Koen Van Impe <koen.vanimpe@vanimpe.eu>
 *  @package  ulogdviz
 * 
 */



/**
 * Convert an IP to a set of latitude and longitude
 *
 *  ip  string          the IP
 *  @return  array   array with latitude and longitude
 */
function googlemap_IpToLocation($ip) {
    $result = array();
    if (strlen($ip) > 0) {
        $geoip = Net_GeoIP::getInstance(GEOIP_DATABASE);
        $location = $geoip->lookupLocation($ip);
        $result["latitude"] = $location->latitude;
        $result["longitude"] = $location->longitude;
        if (!(strlen($result["latitude"]) > 0)) $result["latitude"] = GEOIP_DEFAULT_LATITUDE;
        if (!(strlen($result["longitude"]) > 0)) $result["longitude"] = GEOIP_DEFAULT_LONGITUDE;
    }

    return $result;
}

?>