<?php
/**
 *  ulogd visualizer
 *
 *  Various helpers
 *
 *  @author Koen Van Impe <koen.vanimpe@vanimpe.eu>
 *  @package  ulogdviz
 * 
 */




/**
 * Convert a parameter GET request to something
 *
 *  request     array               the request array
 *  type        string              what type of result to return ( "dataset" , "ajax" , "label", "shortcut_name")
 *  returnset   string              return everything, only port filters or ip filters (port, ip, all)
 *  @return     string or array        the result
 */
function convertRequestToParams($request, $type = "dataset", $returnset = "all") {

    $str_port = "";
    $str_protocol = "";
    $str_label = "";
    $str_shortcut_name = "";
    $str_iphost = "";
    $str_ipflow = "";
    $str_ipinclude = "";
    $type = (string) $type;
    $filters_port = array();
    $filters_ip = array();

    // Filters defined?
    if ((array_key_exists("protocol",$request))) {
        $protocol = $request["protocol"];
        if ((array_key_exists("port",$request))) $port = $request["port"];
        else $port = array();

        if (is_array($protocol) and count($protocol) > 0) {
          foreach($protocol as $key => $proto) {
            if (count($filters_port) >= DEFAULT_MAXFILTER)  continue;

            $network_port = $port[$key];
            if (strlen($network_port) > 0) {
              $str_network_port = (int) $network_port;
            }
            else {
              $network_port = -1;
              $str_network_port = "";
            }
            
            $proto = strtolower(substr($proto, 0, 5));
            if ($proto != "none") {
              array_push($filters_port, array( "protocol" => (string) $proto, "port" => $network_port));
              $str_protocol .= " '" . $proto . "' , ";
              $str_port .= " '" . $port[$key] . "' , ";
              $str_label .= " " . $proto . "/" . $str_network_port."&nbsp;&nbsp;&nbsp;";
              $str_shortcut_name .= $proto . $str_network_port."_";
            }
          }
          if (strlen($str_protocol) > 0) {
              $str_protocol = "'protocol[]': [ " . rtrim($str_protocol, ", ") . " ] ";
              $str_port = "'port[]': [ " . rtrim($str_port, ", ") . " ] ";
          }
          else {
            $str_protocol = "";
            $str_port = "";
          }
          $str_shortcut_name = rtrim($str_shortcut_name, "_");
        }
        else {
          $proto = strtolower(substr($protocol, 0, 5));
          if ($proto != "none") {
            array_push($filters_port, array( "protocol" => (string) $proto, "port" => (int) $port));
            $str_protocol = "protocol: '".$proto."'";
            $str_port = "port: ".$port;
            $str_label = " " . $proto . "/" . $port."&nbsp;&nbsp;&nbsp;";
            $str_shortcut_name = $proto . $port;
          }
        }
    }

    if ((array_key_exists("ip",$request))) {
        $ip = $request["ip"];
        if ((array_key_exists("ipflow",$request))) $ipflow = $request["ipflow"];
        else $ipflow = array();
        if ((array_key_exists("ipinclude",$request))) $ipinclude = $request["ipinclude"];
        else $ipinclude = array();

        if (is_array($ip) and count($ip) > 0) {
            foreach($ip as $key => $host) {
                if (strlen($host) > 0) {

                    $flow = (string) strtolower(trim($ipflow[$key]));
                    $include = (string) strtolower(trim($ipinclude[$key]));

                    if ($include == "exclude") {
                        $str_label .= " NOT ";
                        $str_shortcut_name .= "not";
                        $str_ipinclude .= " 'exclude' , ";
                    }
                    else {
                        $str_ipinclude .= " 'include' , ";
                    }
                    if ($flow == "source")  $str_label .= " from ";
                    elseif ($flow == "dest") $str_label .= " to ";
                    elseif ($flow == "sourcedest") $str_label .= " from/to ";
                    $str_ipflow = " '" . $flow . "' , ";

                    array_push($filters_ip, array( "host" => trim($host), "flow" => $flow, "include" => $include));
                    $str_ip .= " '" . $host . "' , ";
                    $str_label .= " host <strong>" . trim($host) . "</strong> ";
                    $str_shortcut_name .= $flow.$host;
                }
                
            }
            $str_ipinclude = "'ipinclude[]': [" . rtrim($str_ipinclude, ", ") . "]";
            $str_ipflow = "'ipflow[]': [" . rtrim($str_ipflow, ", ") . "]";
            $str_ip = "'ip[]': [" . rtrim($str_ip, ", ") . "]";
        }
    }

    if ($type == "ajax") {  // Ajax disregards port or protocol
        if (strlen($str_port) > 0 and strlen($str_protocol) > 0) $r = $str_protocol." , ".$str_port;        
        else $r = "";
        if (strlen($str_ipinclude) > 0 and strlen($str_ipflow) > 0 and strlen($str_ip) > 0) {
            if (strlen($r) > 0) $r .= " , " . $str_ip." , ". $str_ipinclude . " , " . $str_ipflow;
            else $r = $str_ip." , ". $str_ipinclude . " , " . $str_ipflow;
        }
        return $r;
        //return $str_protocol." , " . $str_port;// . $str_ip;
    }
    elseif ($type == "label") {
        return $str_label;
    }
    elseif ($type == "shortcut_name") {
        return $str_shortcut_name;
    }
    else {
        if ($returnset == "port")  return $filters_port;
        elseif ($returnset == "ip")   return $filters_ip;
        else return array_merge($filters_ip, $filters_port);
    }
}



/**
 * Convert a timeframe to a text string
 *
 *  timeframe    string          the timeframe identifier
 *  @return  string         the text string
 */
function timeframeToHtml($timeframe) {
    if (strlen($timeframe) > 0) {
        if ($timeframe == "lastday")    $r = "Last day";
        elseif ($timeframe == "lasthour")   $r = "Last hour";
        elseif ($timeframe == "last4hour")   $r = "Last 4 hours";
        elseif ($timeframe == "last3day")   $r = "Last 3 days";        
        elseif ($timeframe == "lastweek")   $r = "Last week";
        elseif ($timeframe == "lastmonth")   $r = "Last month";
        elseif ($timeframe == "last3month")   $r = "Last 3 months";
        elseif ($timeframe == "last15min")   $r = "Last 15 minutes";
        elseif ($timeframe == "last30min")   $r = "Last 30 minutes";        
        else $r = APP_UNKNOWN;
    }
    else $r = APP_UNKNOWN;

    return $r;
}



/** 
 * Convert a value from a database field to an IP
 *
 *  value       string       the database value
 *  @return     string       a string containing the IP
 */
function db2ip($value) {
    if (strlen($value) > 0) {
        return long2ip(hexdec($value));
    }
    else return APP_UNKNOWN;
}



/** 
 * Custom debug
 *
 *  debug       array       print the content of this value
 *  @return     nothing
 */
function myprint_r($debug) {
    echo "<hr />";
    if (is_array($debug))   print_r($debug);
    else echo $debug;
    echo "<hr />";
}


?>