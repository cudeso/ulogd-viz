<?php
/**
 *  ulogd visualizer
 *
 *    Page to generate graphs
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

if (array_key_exists("timeframe",$get)) {
  $timeframe = custom_filter_input(strtolower($get["timeframe"]));
}
else $timeframe = DEFAULT_TIMEFRAME;

$params_filter = convertRequestToParams($get, "ajax");

if (array_key_exists("isolateresult",$get)) {
  if ($get["isolateresult"] == "isolate_ip")  $params_filter .= ", isolate_ip: true ";
}

$params = " timeframe: '".$timeframe."' ";
$shortcut_name = $timeframe . convertRequestToParams($get, "shortcut_name");
$csv_url = "get.php?chart=csv&" . custom_filter_input($_SERVER["QUERY_STRING"]);
$table_url = "table.php?" . custom_filter_input($_SERVER["QUERY_STRING"]);

if (strlen($params_filter) > 0) {
  $params .= " , ".$params_filter;
  $legend = true;
  $chartArea = "largelegend";  
}
else {
  $legend = false;
  $chartArea = "large";
}

?>

<script type="text/javascript">
    google.load('visualization', '1', {'packages':['corechart']});
    google.load('visualization', '1', {'packages':['table']});

    // Set a callback to run when the Google Visualization API is loaded.
    google.setOnLoadCallback(drawChart); 

    function drawChart() {
      <?php         
        echo googlechart_getOptions(array(  "name" => "options_overview",
                                            "chartArea" => $chartArea,
                                            "height" => 320,
                                            "smoothLine" => false,
                                            "legend" => $legend ));

        echo googlechart_getOptions(array(  "name" => "options_overview_table",
                                    "chartArea" => "largelegend",
                                    "smoothLine" => false,
                                    "legend" => true));
        echo googlechart_getJson(array( "name" => "json_overview" , "data" => $params , "special" => true));
      ?>

      var chart_data = new google.visualization.LineChart(document.getElementById('chart_data'));
      var table_data = new google.visualization.Table(document.getElementById('chart_table'));      
      chart_data.draw(data_json_overview, options_overview);
      table_data.draw(data_json_overview, options_overview_table);      
    }
</script>

<div class="row">
    <div class="col-lg-12">
        <h3>Hits for <?php echo timeframeToHtml($timeframe);?>&nbsp;&nbsp;<small><?php echo convertRequestToParams($get, "label"); ?></small></h3>
    </div>
</div>

<div class="row">
    <div class="col-lg-12" style="height: 320px;">
        <div id="chart_data"></div>
    </div>
</div>

<div class="modal fade" id="shortcutModal" tabindex="-1" role="dialog" aria-labelledby="shortcutModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <form action="put.php" role="form" id="shortcut" method="get" class="form-inline">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Save query as a shortcut</h4>
      </div>
      <div class="modal-body">
        <div class="form-group input-group">
          <span class="input-group-addon"><i class="fa fa-bookmark"></i></span>
          <input disabled type="text" name="paramsdisplay" id="paramsdisplay" value="" class="form-control btn-xs"  />
          <input type="hidden" name="params" id="params" value="" />
          <input type="hidden" name="shortcuttype" value="graph" />
        </div>
        <input type="hidden" name="type" value="shortcut" />
        <input class="form-control" type="text" name="shortcut" size="20" maxlength="20" value="<?php echo $shortcut_name; ?>" />
      </div>
      <div class="modal-footer">
          <button type="submit" id="shortcutsubmit" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
      </form>
      </div>
    </div>
  </div>
  <script type="text/javascript">
    $("input#paramsdisplay").val(window.location.href.slice(window.location.href.indexOf('?') + 1));
    $("input#params").val(window.location.href.slice(window.location.href.indexOf('?') + 1));    
  </script>
</div>

<div class="row">
    <div class="col-lg-10">
        <a href="<?php echo APP_WEBROOT . $csv_url; ?>" class="btn btn-warning btn-sm"><i class="fa fa-save"></i> Export to CSV</a>
        <a href="<?php echo APP_WEBROOT . $table_url; ?>" class="btn btn-info btn-sm"><i class="fa fa-list"></i> List records</a>        
        <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#shortcutModal"><i class="fa fa-bookmark"></i> Save as shortcut</button>
    </div>
    <div class="col-lg-2">
        <a href="<?php echo APP_WEBROOT; ?>" class="btn btn-primary btn-sm"><i class="fa fa-dashboard"></i> Run another query</a>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() { 
  $('form#shortcut').ajaxForm();
  $('#shortcutsubmit').on('click', function(e){
    e.preventDefault();
    $('form#shortcut').submit();
    $('#shortcutModal').modal('hide');
    updateShortcut();
  });
});
</script>

<br />

<div class="row">
    <div class="col-lg-12">
        <div id="chart_table"></div>
    </div>
</div>


<?php
ulogd_printhtmlEnd();
?>
