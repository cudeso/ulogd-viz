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


ulogd_printhtmlHead(custom_filter_input($_SERVER["PHP_SELF"]));
ulogd_printhtmlBodyStart(custom_filter_input($_SERVER["PHP_SELF"]));


$ip2db = custom_filter_input($_GET["ip2db"], FILTER_VALIDATE_IP);
$orig_ip2db = $ip2db;
$db2ip = custom_filter_input($_GET["db2ip"], FILTER_VALIDATE_STRING);
$orig_db2ip = $db2ip;
$time2date = custom_filter_input($_GET["time2date"], FILTER_VALIDATE_FLOAT);
$orig_time2date = $time2date;
$date2time = custom_filter_input($_GET["date2time"], FILTER_VALIDATE_STRING);
$orig_date2time = $date2time;

if (isset($ip2db))      $ip2db = ip2db($ip2db);
if (isset($db2ip))      $db2ip = db2ip($db2ip);
if (isset($time2date))  $time2date = time2date($time2date);
if (isset($date2time))  $date2time = date2time($date2time);

?>

<div class="row">
    <div class="col-lg-12">
        <h3>Tools</h3>
    </div>
</div>

<form action="tools.php" role="form" id="dashboard-generate" method="get" class="form-inline">

<div class="row">
    <div class="col-lg-12">
        <div class="panel-body">
            <div class="form-group">
                <input class="input-mini form-control" type="text" id="ip2db" name="ip2db" size="32" maxlength="32" placeholder="IP -> DB">
                <br />
                IP -> DB : <?php echo $orig_ip2db . "->" . $ip2db; ?>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel-body">
            <div class="form-group">
                <input class="input-mini form-control" type="text" id="db2ip" name="db2ip" size="32" maxlength="32" placeholder="DB -> IP">
                <br />
                DB -> IP : <?php echo $orig_db2ip . "-> " . $db2ip; ?>                
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel-body">
            <div class="form-group">
                <input class="input-mini form-control" type="text" id="time2date" name="time2date" size="24" maxlength="20" placeholder="Time to Date">
                <br />
                Time to Date : <?php echo $orig_time2date . "->" . $time2date; ?>                
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel-body">
            <div class="form-group">
                <input class="input-mini form-control" type="text" id="date2time" name="date2time" size="24" maxlength="20" placeholder="Date to time (<?php echo DEFAULT_DATEFORMAT_LONG; ?>)">
                <br />
                Date to Time : <?php echo $orig_date2time . "->" . $date2time; ?>                
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel-body">
              <div class="col-lg-2">
                <input type="submit" class="btn btn-primary" value="Submit"  />
              </div>    
        </div>
    </div>
</div>

</form>

<?php            
ulogd_printhtmlEnd();
?>