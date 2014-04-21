<?php
/**
 *  ulogd visualizer
 *
 *  Class to interact with ulog data and return JSON data
 *
 *  @author Koen Van Impe <koen.vanimpe@vanimpe.eu>
 *  @package  ulogdviz
 * 
 */


class ulogd_json {


  /**
  * Return the timestamp from which to clean up the entries
  *
  * returns       int     the time integer
  *
  */
  public function getCronCleanup() {
    return $this->convertTimeframeParam(DEFAULT_CLEANUPTIME);
  }



  /**
  * Convert a timeframe identifier to a timestamp
  *
  * timeframe       string      the timeframe identifier
  * returns       int     the time integer
  *
  */
  private function convertTimeframeParam($timeframe = "") {

    if (strlen($timeframe) > 0) {
      if ($timeframe == "lastday") $time =  mktime(date("H") , date("i"), date("s"), date("m") , date("d") - 1, date("Y"));
      elseif ($timeframe == "lasthour") $time =  mktime(date("H") - 1 , date("i"), date("s"), date("m") , date("d"), date("Y"));
      elseif ($timeframe == "last4hour") $time =  mktime(date("H") - 4 , date("i"), date("s"), date("m") , date("d"), date("Y"));
      elseif ($timeframe == "last3day") $time =  mktime(date("H") , date("i"), date("s"), date("m") , date("d") - 3, date("Y"));      
      elseif ($timeframe == "lastweek") $time =  mktime(date("H") , date("i"), date("s"), date("m") , date("d") - 7, date("Y"));
      elseif ($timeframe == "lastmonth") $time =  mktime(date("H") , date("i"), date("s"), date("m") - 1, date("d") , date("Y"));
      elseif ($timeframe == "last3month") $time =  mktime(date("H") , date("i"), date("s"), date("m") - 3, date("d") , date("Y"));
      elseif ($timeframe == "last12hour") $time =  mktime(date("H") - 12, date("i"), date("s"), date("m") - 3, date("d") , date("Y"));      
      elseif ($timeframe == "last15min") $time =  mktime(date("H") , date("i") - 15 , date("s"), date("m") , date("d"), date("Y"));
      elseif ($timeframe == "last30min") $time =  mktime(date("H") , date("i") - 30 , date("s"), date("m") , date("d"), date("Y"));      

      else $time = 0;      
    }
    else $time = 0;

    return $time;
  }


  /**
  * Return a full dataset
  *
  * request       array         array with all the filters, options, ...
  * returns       JSON encoded string
  *
  */
  public function buildFullDataset($request = array()) {
    if (!(array_key_exists("timeframe",$request))) $request["timeframe"] = DEFAULT_TIMEFRAME;
    $timeframe = strtolower($request["timeframe"]);
    if (!strlen($timeframe) > 0)  $timeframe = DEFAULT_TIMEFRAME;    
    $time = $this->convertTimeframeParam($timeframe);
    $con = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
    $sql = "  SELECT oob_time_sec,ip_protocol,ip_saddr, ip_daddr,ip_totlen,ip_ttl,udp_sport,udp_dport,tcp_sport,tcp_dport,icmp_type,icmp_code
               FROM " . DB_TABLE . "
               WHERE oob_time_sec > " . $time . "
               ORDER BY oob_time_sec DESC
               LIMIT " . DEFAULT_MAXRECORDS_DB;
    $result_db = mysqli_query( $con, $sql );

    while($row = mysqli_fetch_assoc($result_db)) {
      $timestamp = date(DEFAULT_DATEFORMAT_LONG, $row["oob_time_sec"]);
      $ip_saddr = long2ip($row["ip_saddr"]);
      $ip_daddr = long2ip($row["ip_daddr"]);
      $protocol = $row["ip_protocol"];
      $ip_ttl = $row["ip_ttl"];
      $ip_totlen = $row["ip_totlen"];
      if ($protocol == 17) {
          $sport = $row["udp_sport"];
          $dport = $row["udp_dport"];
          $strprotocol = "udp";
      }
      elseif ($protocol == 6) {
          $sport = $row["tcp_sport"];
          $dport = $row["tcp_dport"];                                                
          $strprotocol = "tcp";
      }
      elseif ($protocol == 1) {
          $sport = $row["icmp_type"] . "/" . $row["icmp_code"];
          $dport = "";
          $strprotocol = "icmp";
      }

      $result_container[] = array( $timestamp , $strprotocol , $ip_saddr, $sport , $ip_daddr, $dport, $ip_ttl, $ip_totlen);
    }
//    $result_container = array_reverse($result_container);        
    mysqli_close($con); 

    if (is_array($result_container)) {
      echo json_encode(array( "aaData" => $result_container));
    }
    else {
      echo json_encode(array( "aaData" => array( array( "no data", "-", "-", "-", "-", "-", "-", "-"))));
    }
    
  }



  /**
  * Return the matching result for the blacklist
  *
  * request       array         array with all the filters, options, ...
  * returns       JSON encoded string
  *
  */
  public function blacklistHits($request = array()) {

    if (!(array_key_exists("blacklist",$request))) $request["blacklist"] = DEFAULT_MAXFILTERT_TIMEFRAME;
    $timeframe = strtolower($request["blacklist"]);
    $time = $this->convertTimeframeParam($timeframe);

    $where = "";
    $count = 0;
    $qt = 0;
    $ulogd_blacklist = new ulogd_blacklist();
    $blacklist = $ulogd_blacklist->get();

    if (is_array($blacklist) and count($blacklist) > 0) {
      foreach ($blacklist as $value) {
        $value = trim($value);
        $where .= " AND ip_saddr = " . ip2long($value);
      }

      $con = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
      $sql = "  SELECT COUNT(*) AS qt
                FROM " . DB_TABLE . "
                WHERE oob_time_sec > " . $time . $where . "
                ORDER BY qt DESC LIMIT 1";
      $result = mysqli_query( $con, $sql );
      $row = mysqli_fetch_row($result);
      $qt = (int) $row["qt"];

      $count = $ulogd_blacklist->count();

      mysqli_close($con);        
    }

    $result = array( "hits" => $qt , "count" => $count);
    echo json_encode( $result );

  }



  /**
  * Return the top IP in a JSON format
  *
  * request       array         array with all the filters, options, ...
  * returns       JSON encoded string
  *
  */
  public function topIp($request = array()) {

    if (!(array_key_exists("topIp",$request))) $request["topIp"] = DEFAULT_TIMEFRAME;
    $timeframe = strtolower($request["topIp"]);
    $time = $this->convertTimeframeParam($timeframe);

    if (!(array_key_exists("destination",$request))) $request["destination"] = "source";
    $destination = strtolower($request["destination"]);
    if ($destination == "source") $destination = "ip_saddr";
    else $destination = "ip_daddr";

    $con = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
    $sql = "  SELECT COUNT(*) AS qt, " . $destination . " AS ip
              FROM " . DB_TABLE . "
              WHERE oob_time_sec > " . $time . "
              GROUP BY " . $destination . "
              ORDER BY qt DESC LIMIT 1";
    $result = mysqli_query( $con, $sql );
    $row = mysqli_fetch_assoc($result);
    $qt = (int) $row["qt"];
    $ip = (int) $row["ip"];
    if ($ip > 0)  $ip = long2ip($ip);
    else $ip = APP_UNKNOWN;
    mysqli_close($con);  

    $result = array( "result" => $ip );
    echo json_encode( $result );
  }



  /**
  * Return the top ports in a JSON format
  *
  * request       array         array with all the filters, options, ...
  * returns       JSON encoded string
  *
  */
  public function topPort($request = array()) {

    if (!(array_key_exists("topPort",$request))) $request["topPort"] = DEFAULT_TIMEFRAME;
    $timeframe = strtolower($request["topPort"]);
    $time = $this->convertTimeframeParam($timeframe);

    $filters = array();
    $where = "";

    $where .= $this->filterToWhere($request);

    $limit = 1;
    if (array_key_exists("portcount", $request)) {
      $portcount = (int) $request["portcount"];
      if ($portcount > 0) {
        $limit = $portcount;
      }
    }

    $con = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
    $sql = "  SELECT COUNT(*) AS qt, ip_protocol, tcp_dport, udp_dport, icmp_type
              FROM " . DB_TABLE . "
              WHERE oob_time_sec > " . $time . $where . "
              GROUP BY ip_protocol, tcp_dport, udp_dport,icmp_type
              ORDER BY qt DESC LIMIT " . $limit . ";";
//echo $sql;
    $dbresult = mysqli_query( $con, $sql );
    $result = array();
    while($row = mysqli_fetch_assoc($dbresult)) {
      $qt = (int) $row["qt"];
      $protocol = (int) $row["ip_protocol"];
      if ($protocol == 6) {
        $protocol = "tcp";
        $port = $row["tcp_dport"];
      }
      elseif ($protocol == 17) {
        $protocol = "udp";
        $port = $row["udp_dport"];
      }
      elseif ($protocol == 1) {
        $protocol = "icmp";
        $port = $row["icmp_type"];
      }
      array_push($result, array(  "qt" => $qt, "protocol" => $protocol, "port" => $port));
    }  
    if (!count($result) > 0) {
      array_push($result, array(  "qt" => 0, "protocol" => "none", "port" => 0));
    }

    echo json_encode( $result );
  }



  /**
  * Return the number of entries in a JSON format
  *
  * request       array         array with all the filters, options, ...
  * returns       JSON encoded string
  *
  */
  public function numberOfEntries($request = array()) {

    if (!(array_key_exists("numberOfEntries",$request))) $request["numberOfEntries"] = DEFAULT_TIMEFRAME;
    $timeframe = strtolower($request["numberOfEntries"]);
    $time = $this->convertTimeframeParam($timeframe);

    $where = "";
    if (array_key_exists("protocol",$request)) {
      $protocol = strtolower($request["protocol"]);
      if ($protocol == "tcp") $where = " AND ip_protocol = 6";
      elseif ($protocol == "udp") $where = " AND ip_protocol = 17";
      elseif ($protocol == "icmp") $where = " AND ip_protocol = 1";
    }

    $con = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
    $sql = "  SELECT COUNT(*) AS qt
              FROM " . DB_TABLE . "
              WHERE oob_time_sec > " . $time . $where;

    $result = mysqli_query( $con, $sql );
    $row = mysqli_fetch_assoc($result);
    $qt = (int) $row["qt"];
    mysqli_close($con);  

    $result = array( "result" => $qt);
    echo json_encode( $result );
  }




 /**
  * Return statistics
  *
  * request       array         array with all the filters, options, ...
  * returns       JSON encoded string
  *
  */
  public function getStats($request = array()) {

    if (array_key_exists("stats",$request)) {
      $stats = strtolower($request["stats"]);
      if ($stats == "first") $order = " ASC ";
      else $order = " DESC ";

      $con = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
      $sql = "  SELECT oob_time_sec
                FROM " . DB_TABLE . "
                ORDER BY oob_time_sec " . $order . " LIMIT 1";

      $result = mysqli_query( $con, $sql );
      $row = mysqli_fetch_assoc($result);
      $timestamp = date(DEFAULT_DATEFORMAT_LONG, $row["oob_time_sec"]);

      mysqli_close($con);  

      $result = array( "result" => $timestamp);
      echo json_encode( $result );

    }
    else return false;
  }



  /**
  * Return a combined dataset (mostly for maps)
  *
  * request       array         array with all the filters, options, ...
  * returns       JSON encoded string
  *
  */
  public function buildCombinedDataset($request = array()) {

    $filters = array();

    if (is_array($request)) {

      // Did we get a timeframe?
      if (!(array_key_exists("timeframe",$request))) $request["timeframe"] = DEFAULT_TIMEFRAME;
      $timeframe = strtolower($request["timeframe"]);
      if (!strlen($timeframe) > 0)  $timeframe = DEFAULT_TIMEFRAME;

      $groupbyip = "ip_saddr";

      $filters = convertRequestToParams($request);
      $time = $this->convertTimeframeParam($timeframe);

      $where = "";
      if (is_array($filters) and count($filters) > 0) {
        foreach($filters as $filter) {
          $where .= " ( 1=1 " .$this->filterToWhere($filter) . " ) OR ";
        }
        $where = rtrim($where, " OR ");
      }

      $con = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

      if (strlen($where) > 0) $where = " AND " . $where;

      // First grab total number of markers
      $sql = "SELECT COUNT(*) AS markercount FROM " . DB_TABLE . " WHERE ".
                " oob_time_sec > " . $time . " " .
                $where;
      $dbresult = mysqli_query( $con, $sql );
      $row = mysqli_fetch_assoc($dbresult);
      $markercount = (int) $row["markercount"];

      $sql = "SELECT COUNT(*) AS qt, " . $groupbyip . " AS ip FROM " . DB_TABLE . " WHERE ".
                " oob_time_sec > " . $time . " " .
                $where.
                " GROUP BY " . $groupbyip;
//echo $sql;
      $dbresult = mysqli_query( $con, $sql );

      $result = array();
      $markercountdb = 0;
      $markerrecount = 1;
      if ($markercount > DEFAULT_MAP_MAXMARKERS and DEFAULT_MAP_MAXMARKERS > 0) {
        $markerrecount = (int) $markercount / DEFAULT_MAP_MAXMARKERS;
      }

      while($row = mysqli_fetch_assoc($dbresult)) {
        $ip = googlemap_IpToLocation(long2ip($row["ip"]));
        $qt = (int) round($row["qt"] / $markerrecount);
        if ($qt > 0) {
          $markercountdb = $markercountdb + $qt;
          array_push($result, array(  "qt" => $qt, "ip" => long2ip($row["ip"]), "latitude" => $ip["latitude"], "longitude" => $ip["longitude"]));
        }
      }

      array_push($result, array( "counters" => array( "markerrecount" => $markerrecount, "count" => $markercount, "countdb" => $markercountdb )));

      if (array_key_exists("return", $request) and $request["return"] == "data") return $result;
      else echo json_encode( $result ); 
    }
    else return false;
  }




  /**
  * Construct the 'where' clause in SQL based on the filters
  *
  * filter       array         array with all the filters, options, ...
  * returns       string    containing the where clause
  *
  */
  private function filterToWhere($filter) {
    $where = "";
    if (array_key_exists("protocol",$filter)) {
      $protocol = strtolower($filter["protocol"]);
      if ($protocol == "tcp") $where .= " AND ip_protocol = 6 ";
      elseif ($protocol == "udp") $where .= " AND ip_protocol = 17 ";
      elseif ($protocol == "icmp") $where .= " AND ip_protocol = 1 ";
    }
    if (array_key_exists("port",$filter)) {
      $port = (int) $filter["port"];
      if ($port >= 0) {
        if ($protocol == "tcp") $where .= " AND tcp_dport = $port ";
        elseif ($protocol == "udp") $where .= " AND udp_dport = $port ";
        elseif ($protocol == "icmp") $where .= " AND icmp_type = $port ";
        elseif ($protocol == "any") $where .= " AND (tcp_dport = $port OR udp_dport = $port OR icmp_type = $port ) ";        
      }
    }
    if (array_key_exists("host", $filter)) {
      $ip = ip2long($filter["host"]);
      $flow = $filter["flow"];
      $include = $filter["include"];

      if ($flow == "source") {
        if ($include == "include") $where .= " AND ip_saddr = " . $ip;
        elseif ($include == "exclude") $where .= " AND ip_saddr != " . $ip;
      } 
      elseif ($flow == "dest") {
        if ($include == "include") $where .= " AND ip_daddr = " . $ip;
        elseif ($include == "exclude") $where .= " AND ip_daddr != " . $ip;
      }
      elseif ($flow == "sourcedest") {
        if ($include == "include") $where .= " AND ( ip_saddr = " . $ip . " OR ip_daddr = " . $ip . " ) ";
        elseif ($include == "exclude") $where .= " AND !( ip_saddr = " . $ip . " OR ip_daddr = " . $ip . " ) ";
      }
    }
    return $where;
  }



  /**
  * Build a JSON string from the ulogd data
  *
  * request       array         array with all the filters, options, ...
  * returns       JSON encoded string
  *
  */
  public function buildDataset($request = array()) {

    $json = array();
    $filters = array();

    if (is_array($request)) {
      
      // Did we get a timeframe?
      if (!(array_key_exists("timeframe",$request))) $request["timeframe"] = DEFAULT_TIMEFRAME;
      $timeframe = strtolower($request["timeframe"]);
      if (!strlen($timeframe) > 0)  $timeframe = DEFAULT_TIMEFRAME;

      $extrawhere = "";
      $filters_ip = convertRequestToParams($request, "dataset", "ip");
      if (is_array($filters_ip) and count($filters_ip) > 0) {
        foreach($filters_ip as $filter) {
          if (isset($filter["host"])) {
            $extrawhere .= $this->filterToWhere($filter);
          }
        }
      }

      $filters = convertRequestToParams($request, "dataset", "port");
      $json['cols'][] = array('type' => 'string');

      // Multi source graph or not
      if (count($filters) > 0) {
        $container = array();
        foreach($filters as $filter) {
          $key = "";
          $filter["extrawhere"] = $extrawhere;
          if (isset($filter["port"]) and $filter["port"] != -1 ) $key = $filter["protocol"] . " / " . $filter["port"];
          elseif (isset($filter["protocol"])) $key = $filter["protocol"];

          if (strlen($key) > 0)  $container[$key] = $this->getData($timeframe , $filter);
        }

        // Build the columns
        foreach($container as $key => $cont) {
          $json["cols"][] = array( "label" => $key. " ", "type" => "number");          
        }
        $first = reset($container);
        foreach($first as $key => $value) {
          $t = array();
          $t[] = array( "v" => $key);
          foreach($container as $cont) {
            $t[] = array( "v" => $cont[$key]);
          }
          $json["rows"][] = array( "c" => $t);
        }
      }
      else {
        $container = $this->getData($timeframe, array( "extrawhere" => $extrawhere));
        $json['cols'][] = array('type' => 'number', 'label' => 'hits');
        foreach($container as $key => $value) {
          $json['rows'][]['c'] = array(
              array('v' => $key),
              array('v' => $value)
          );        
        }
      }

      if (array_key_exists("return", $request) and $request["return"] == "data") return $json;
      else echo json_encode( $json );
    }
    else return false;
  }



  /**
  * Return variables to build the query string
  *
  * timeframe     string         what is the timeframe
  * returns       array with the query options
  *
  */
  private function getDataVariables($timeframe) {

    if ($timeframe == "lasthour") {
      $sql_timeframe = "%Y-%m-%d %H:%i";
      $result = array(
              "starttime" =>  mktime(date("H") - 1 , date("i"), date("s"), date("m") , date("d") , date("Y")),
              "for_x" => 60,
              "str_to_time" => "minute",
              "date_str" => DEFAULT_DATEFORMAT_SHORT,
              "sql_timeframe" =>  $sql_timeframe,
              "sql_from" => "from_unixtime(oob_time_sec, '$sql_timeframe')"
        );
    }
    elseif ($timeframe == "last15min") {
      $sql_timeframe = "%Y-%m-%d %H:%i";
      $result = array(
              "starttime" =>  mktime(date("H") , date("i"), date("s"), date("m") - 15 , date("d") , date("Y")),
              "for_x" => 15,
              "str_to_time" => "minute",
              "date_str" => DEFAULT_DATEFORMAT_SHORT,
              "sql_timeframe" =>  $sql_timeframe,
              "sql_from" => "from_unixtime(oob_time_sec, '$sql_timeframe')"
        );
    }
    elseif ($timeframe == "last30min") {
      $sql_timeframe = "%Y-%m-%d %H:%i";
      $result = array(
              "starttime" =>  mktime(date("H") , date("i"), date("s"), date("m") - 30, date("d") , date("Y")),
              "for_x" => 30,
              "str_to_time" => "minute",
              "date_str" => DEFAULT_DATEFORMAT_SHORT,
              "sql_timeframe" =>  $sql_timeframe,
              "sql_from" => "from_unixtime(oob_time_sec, '$sql_timeframe')"
        );
    }    
    elseif ($timeframe == "lastweek") {
      $sql_timeframe = "%Y-%m-%d";
      $result = array(
              "starttime" =>  mktime(date("H") , date("i"), date("s"), date("m") , date("d") - 7, date("Y")),
              "for_x" => 7,
              "str_to_time" => "day",
              "date_str" => "Y-m-d",
              "sql_timeframe" =>  $sql_timeframe,
              "sql_from" => "from_unixtime(oob_time_sec, '$sql_timeframe')"
        );
    }
    elseif ($timeframe == "last4hour") {
      $sql_timeframe = "%d/%m %H:00";
      $result = array(
              "starttime" =>  mktime(date("H") - 4 , date("i"), date("s"), date("m") , date("d"), date("Y")),
              "for_x" => 4,
              "str_to_time" => "hour",
              "date_str" => "d/m H:00",
              "sql_timeframe" =>  $sql_timeframe,
              "sql_from" => "from_unixtime(oob_time_sec, '$sql_timeframe')"
        );      
    }    
    elseif ($timeframe == "last12hour") {
      $sql_timeframe = "%d/%m %H:00";
      $result = array(
              "starttime" =>  mktime(date("H") - 12 , date("i"), date("s"), date("m") , date("d"), date("Y")),
              "for_x" => 12,
              "str_to_time" => "hour",
              "date_str" => "d/m H:00",
              "sql_timeframe" =>  $sql_timeframe,
              "sql_from" => "from_unixtime(oob_time_sec, '$sql_timeframe')"
        );      
    }
    elseif ($timeframe == "lastday") {
      $sql_timeframe = "%d/%m %H:00";
      $result = array(
              "starttime" =>  mktime(date("H") , date("i"), date("s"), date("m") , date("d") - 1, date("Y")),
              "for_x" => 24,
              "str_to_time" => "hour",
              "date_str" => "d/m H:00",
              "sql_timeframe" =>  $sql_timeframe,
              "sql_from" => "from_unixtime(oob_time_sec, '$sql_timeframe')"
        );      
    }
    elseif ($timeframe == "last3day") {
      $sql_timeframe = "%d/%m %H:00";
      $result = array(
              "starttime" =>  mktime(date("H") , date("i"), date("s"), date("m") , date("d") - 3, date("Y")),
              "for_x" => 76,
              "str_to_time" => "hour",
              "date_str" => "d/m H:00",
              "sql_timeframe" =>  $sql_timeframe,
              "sql_from" => "from_unixtime(oob_time_sec, '$sql_timeframe')"
        );      
    }    
    elseif ($timeframe = "lastmonth") {
      $sql_timeframe = "%d/%m";
      $result = array(
              "starttime" =>  mktime(date("H") , date("i"), date("s"), date("m") - 1, date("d") , date("Y")),
              "for_x" => 31,
              "str_to_time" => "day",
              "date_str" => "d/m",
              "sql_timeframe" =>  $sql_timeframe,
              "sql_from" => "from_unixtime(oob_time_sec, '$sql_timeframe')"
        );
    }
    elseif ($timeframe = "last3month") {
      $sql_timeframe = "%d/%m";
      $result = array(
              "starttime" =>  mktime(date("H") , date("i"), date("s"), date("m") - 3, date("d") , date("Y")),
              "for_x" => 93,
              "str_to_time" => "day",
              "date_str" => "d/m",
              "sql_timeframe" =>  $sql_timeframe,
              "sql_from" => "from_unixtime(oob_time_sec, '$sql_timeframe')"
        );
    }

    return $result;
  }



  /**
  * Returns a result set of the queried data
  *
  * timeframe     string        what is the timeframe
  * filters       array         list of options
  * returns       array with the query results
  *
  */
  private function getData($timeframe, $filters = array()) {

    if ($timeframe) {
      $data_variables = $this->getDataVariables($timeframe);

      $where = $this->filterToWhere($filters);

      $result_container = array();
      for($x = 0 ; $x <= $data_variables["for_x"] ; $x = $x + 1) {
        $t = strtotime("-$x " . $data_variables["str_to_time"]);
        $i = date($data_variables["date_str"],$t);
        $result_container[$i] = 0;
      }

      if (array_key_exists("extrawhere", $filters)) $extrawhere = $filters["extrawhere"];
      else $extrawhere = "";

      $con = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
      $sql = "  SELECT COUNT(*) AS qt, " .
                  $data_variables["sql_from"] . " AS t
                FROM " . DB_TABLE . "
                WHERE oob_time_sec > " . $data_variables["starttime"] . 
                  $where . " " . $extrawhere . " GROUP BY " . $data_variables["sql_from"];
//echo $sql;
      $result = mysqli_query( $con, $sql );

      while($row = mysqli_fetch_assoc($result)) {
        if (array_key_exists($row["t"], $result_container)) $result_container[$row["t"]] = $row["qt"];        
      }
      $result_container = array_reverse($result_container);        

      mysqli_close($con);  

      return $result_container;
    }
    return false;
  }




  /**
  * Return a CSV file based on the given query
  *
  * get       array         list of options
  * returns       string    containing the CSV data
  *
  */
  public function csv($get) {
    header('Content-type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . DEFAULT_CSVEXPORT );

    $get["return"] = "data";
    $dataset = $this->buildDataset($get);

    $cols = $dataset["cols"];
    $data = $dataset["rows"];

    $row_print = "";
    foreach($dataset["cols"] as $c) {
      $row_print .= "\"" . trim($c["label"]) . "\",";
    }
    $row_print = rtrim($row_print, ","). "\n";
    echo $row_print ;

    foreach($dataset["rows"] as $row) {
      $set = $row["c"];

      $row_print = "";
      foreach($set as $el) {
        $row_print .= "\"" . $el["v"] . "\",";
      }
      $row_print = rtrim($row_print,","). "\n";
      echo $row_print ;
    }
  }
}

?>