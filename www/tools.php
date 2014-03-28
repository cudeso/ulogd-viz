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
        <h3>Tools</h3>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">

        <div class="panel-body">
          <form action="tools.php" role="form" id="dashboard-generate" method="get" class="form-inline">
            <div class="form-group">
                <input class="input-mini form-control" type="text" id="ip2long" name="ip2long" size="20" maxlength="20" placeholder="IP to Long">
                <br />
                IP 2 Long : <?php echo $ip2long; ?>
            </div>
            <div class="form-group">
                <input class="input-mini form-control" type="text" id="long2ip" name="long2ip" size="20" maxlength="20" placeholder="Long to IP">
                <br />
                Long 2 IP : <?php echo $long2ip; ?>                
            </div>
            <div class="form-group">
                <input class="input-mini form-control" type="text" id="time2date" name="time2date" size="20" maxlength="20" placeholder="Time to Date">
                <br />
                Time to Date : <?php echo $time2date; ?>                
            </div>
            <div class="form-group">
                <input class="input-mini form-control" type="text" id="date2time" name="date2time" size="20" maxlength="20" placeholder="Date to time">
                <br />
                Date to Time : <?php echo $date2time; ?>                
            </div>                        
              <div class="col-lg-2">
                <input type="submit" class="btn btn-primary" value="Submit"  />
              </div>    

          </form>
        </div>
    </div>
</div>

<?php            
ulogd_printhtmlEnd();
?>