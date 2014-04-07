<?php
/**
 *  ulogd visualizer
 *
 *    Page to generate table
 *
 *  @author Koen Van Impe <koen.vanimpe@vanimpe.eu>
 *  @package  ulogdviz
 * 
 */

set_include_path(implode(PATH_SEPARATOR, array(
    realpath(dirname(__FILE__) . '/../library'),
    get_include_path(),
)));
require_once "../config/ulogd.php";


ulogd_printhtmlHead($_SERVER["PHP_SELF"]);
ulogd_printhtmlBodyStart($_SERVER["PHP_SELF"]);

$get = $_GET;

$ip2long = $get["ip2long"];
$long2ip = $get["long2ip"];
$time2date = $get["time2date"];
$date2time = $get["date2time"];

if (isset($ip2long))    $ip2long = ip2long($ip2long);
if (isset($long2ip))    $long2ip = long2ip($long2ip);
if (isset($time2date) and strlen($time2date) > 0)    $time2date = date("D-m-Y H:i:s" , $time2date);

?>

<div class="row">
    <div class="col-lg-12">
        <h3>Statistics</h3>
    </div>
</div>

<div class="row">
   <div class="col-lg-12">
        <div class="col-lg-3">
          <div class="alert alert-success">
            <h4>Number of entries <br /><small>all time</small></h4>
            <h3 class="text-right" id="numberofentries_alltime"></h3>
          </div>
        </div>

        <div class="col-lg-3">
          <div class="alert alert-success">
            <h4>Number of entries <br /><small>last 24 hours</small></h4>
            <h3 class="text-right" id="numberofentries_alltime_today"></h3>
          </div>
        </div>

        <div class="col-lg-3 dashboard-portlet" id="top_port">
          <div class="alert alert-success">
            <h4>Top port <br /><small>last 24 hours</small></h4>
            <h3 class="text-right" id="topPort_today"></h3>
          </div>
        </div>

        <div class="col-lg-3">
          <div class="alert alert-success">
            <h4>Top IP <br /><small>last 24 hours</small></h4>
            <h3 class="text-right" id="topIp_today"></h3>
          </div>
        </div>                    
    </div>
</div>

<div class="row">
   <div class="col-lg-12">
        <div class="col-lg-3">
          <div class="alert alert-success">
            <h4>TCP <br /><small>all time</small></h4>
            <h3 class="text-right" id="tcp_alltime"></h3>
          </div>
        </div>

        <div class="col-lg-3">
          <div class="alert alert-success">
            <h4>TCP <br /><small>last 24 hours</small></h4>
            <h3 class="text-right" id="tcp_today"></h3>
          </div>
        </div>

        <div class="col-lg-3 dashboard-portlet" id="top_port">
          <div class="alert alert-success">
            <h4>UDP <br /><small>all time</small></h4>
            <h3 class="text-right" id="udp_alltime"></h3>
          </div>
        </div>

        <div class="col-lg-3">
          <div class="alert alert-success">
            <h4>UDP <br /><small>last 24 hours</small></h4>
            <h3 class="text-right" id="udp_today"></h3>
          </div>
        </div>                    
    </div>
</div>

<div class="row">
   <div class="col-lg-12">
        <div class="col-lg-3">
          <div class="alert alert-success">
            <h4>ICMP <br /><small>all time</small></h4>
            <h3 class="text-right" id="icmp_alltime"></h3>
          </div>
        </div>

        <div class="col-lg-3">
          <div class="alert alert-success">
            <h4>ICMP <br /><small>last 24 hours</small></h4>
            <h3 class="text-right" id="icmp_today"></h3>
          </div>
        </div>

        <div class="col-lg-3 dashboard-portlet" id="top_port">
          <div class="alert alert-success">
            <h4>First entry</h4>
            <h3 class="text-right" id="firstentry"></h3>
          </div>
        </div>

        <div class="col-lg-3">
          <div class="alert alert-success">
            <h4>Last entry</h4>
            <h3 class="text-right" id="lastentry"></h3>
          </div>
        </div>                    
    </div>
</div>
    <script>

        $.getJSON('get.php', { numberOfEntries: "all"}, function(json) {
            $("#numberofentries_alltime").empty().append( json.result );
        });

        $.getJSON('get.php', { numberOfEntries: "lastday"}, function(json) {
            $("#numberofentries_alltime_today").empty().append( json.result );
        });

        $.getJSON('get.php', { numberOfEntries: "all", protocol: "tcp"}, function(json) {
            $("#tcp_alltime").empty().append( json.result );
        });

        $.getJSON('get.php', { numberOfEntries: "lastday", protocol: "tcp"}, function(json) {
            $("#tcp_today").empty().append( json.result );
        });

        $.getJSON('get.php', { numberOfEntries: "all", protocol: "udp"}, function(json) {
            $("#udp_alltime").empty().append( json.result );
        });

        $.getJSON('get.php', { numberOfEntries: "lastday", protocol: "udp"}, function(json) {
            $("#udp_today").empty().append( json.result );
        });

        $.getJSON('get.php', { numberOfEntries: "all", protocol: "icmp"}, function(json) {
            $("#icmp_alltime").empty().append( json.result );
        });

        $.getJSON('get.php', { numberOfEntries: "lastday", protocol: "icmp"}, function(json) {
            $("#icmp_today").empty().append( json.result );
        });

        $.getJSON('get.php', { stats: "first" }, function(json) {
            $("#firstentry").empty().append( json.result );
        });

        $.getJSON('get.php', { stats: "last" }, function(json) {
            $("#lastentry").empty().append( json.result );
        });

        $.getJSON('get.php', { topPort: "lastday"}, function(json) {
          html = json[0].protocol + "/" + json[0].port + " <small>" + json[0].qt + "</small>";
          url_topPort_today = "generate.php?timeframe=lastday&protocol[]=" + json[0].protocol + "&port[]=" + json[0].port + "&formoutput=output_chart";
          $("#topPort_today").empty().append( html);
          $(document).ready(function() { 
            $('#top_port').on('click', function(e){
              document.location.href=url_topPort_today;
            });
          });
        });   

        $.getJSON('get.php', { topIp: "lastday"}, function(json) {
            $("#topIp_today").empty().append( json.result );
        });
    </script>

<?php            
ulogd_printhtmlEnd();
?>