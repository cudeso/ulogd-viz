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


    </div>
</div>

<?php            
ulogd_printhtmlEnd();
?>