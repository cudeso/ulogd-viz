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

$get = $_GET;
$server_querystring = $_SERVER["QUERY_STRING"];

if (array_key_exists("timeframe",$get)) {
  $timeframe = strtolower($get["timeframe"]);
}
else $timeframe = DEFAULT_TIMEFRAME_TABLE;

$tableheader = "
                <tr>
                    <th>Timestamp</th>
                    <th>Protocol</th>                                            
                    <th>Source IP</th>
                    <th>Source Port</th>
                    <th>Destination IP</th>
                    <th>Destination Port</th>
                    <th>TTL</th>
                    <th>Length</th>
                </tr>";
?>

<div class="row">
    <div class="col-lg-12">
        <h3>Table for <?php echo timeframeToHtml($timeframe);?> <small>(limited to <?php echo DEFAULT_MAXRECORDS_DB; ?> records)</small>&nbsp;&nbsp;<small><?php echo convertRequestToParams($get, "label"); ?></small></h3>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">

            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="json-table">
                        <thead><?php echo $tableheader;?></thead>
                        <tbody class="small"></tbody>
                        <tfoot><?php echo $tableheader; ?></tfoot>
                    </table>
                </div>   
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <a href="<?php echo APP_WEBROOT; ?>get.php" class="btn btn-warning btn-sm"><i class="fa fa-list"></i> Export to CSV</a>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#json-table').dataTable( {
            "pageLength": 50,
            "order": [[ 0, "desc" ]],
            "processing": true,
             "sAjaxSource": 'get.php?table=table&timeframe=<?php echo $timeframe;?>&<?php echo $server_querystring; ?>'
        });
    });
</script>

<?php            
ulogd_printhtmlEnd();
?>