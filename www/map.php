<?php
/**
 *  ulogd visualizer
 *
 *    Page to generate maps
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
  $timeframe = strtolower($get["timeframe"]);
}
else $timeframe = DEFAULT_TIMEFRAME;

$params_filter = convertRequestToParams($get, "ajax");
if (strlen($params_filter) > 0) {
    $params = " timeframe: '".$timeframe."',  " . $params_filter;
}
else $params = " timeframe: '".$timeframe."' ";
$shortcut_name = $timeframe . convertRequestToParams($get, "shortcut_name");

?>


<script type="text/javascript">
//$.getJSON('get.php', { map: "map", <?php echo $params; ?> }, function (json) { });

  function initialize() {
    $.getJSON('get.php', { map: "map", <?php echo $params; ?> }, function (json) { 
        var mapOptions = {
          center: new google.maps.LatLng(<?php echo GEOIP_DEFAULT_LATITUDE; ?>, <?php echo GEOIP_DEFAULT_LONGITUDE; ?>),
          zoom: 4,

        };

        var map = new google.maps.Map(document.getElementById("map_canvas"),mapOptions);
        var markers = [];

        if (json != null) {
            var n;
            for(n=0; n<json.length - 1; n++) {
                name = json[n].ip;
                qt = json[n].qt;
                var q;
                for (q=0; q<qt; q++) {
                    var myLatLng = new google.maps.LatLng(json[n].latitude,json[n].longitude);
                    var marker = new google.maps.Marker({
                        position: myLatLng,
                        title: name
                    });
                    markers.push(marker);
                }
            }
            var counters;
            counters = json[json.length - 1].counters;
            if (counters.markerrecount != 1) {
                $("span#markerrecount").empty().append(" (number of markers divided by " + counters.markerrecount + ")");
            }
        }
        var mc = new MarkerClusterer(map, markers);

    });
  }
  google.maps.event.addDomListener(window, 'load', initialize);

</script>

<div class="row">
    <div class="col-lg-12">
        <h3>Hits for <?php echo timeframeToHtml($timeframe);?>&nbsp;&nbsp;<small><?php echo convertRequestToParams($get, "label"); ?><span id="markerrecount"></span></small></h3>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div id="map_canvas" />
    </div>
</div>




<?php
ulogd_printhtmlEnd();
?>