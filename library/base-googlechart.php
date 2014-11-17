<?php
/**
 *  ulogd visualizer
 *
 *  Helper classes for the Google Chart generation
 *
 *  @author Koen Van Impe <koen.vanimpe@vanimpe.eu>
 *  @package  ulogdviz
 * 
 */



/**
 * Print the Javascript options string for the Google Chart
 *
 *  options    array          array containing the options
 *  @return  string   the javascript options string
 */
function googlechart_getOptions($options = array()) {

  if (!(is_array($options)))  $options = array();

  if (array_key_exists("chartArea", $options)) $chartArea = (string) $options["chartArea"];
  else $chartArea = "default";

  if (array_key_exists("legend", $options)) $legend = (boolean) $options["legend"];
  else $legend = false;

  if (array_key_exists("name", $options)) $name = (string) $options["name"];
  else $name = DEFAULT_CHART_OPTIONSNAME;

  if (array_key_exists("height", $options)) $height = (int) $options["height"];
  else $height = "100%";

  if (array_key_exists("width", $options)) $width = (int) $options["width"];
  else $width = "100%";

  if (array_key_exists("smoothLine", $options)) $smoothLine = (boolean) $options["smoothLine"];
  else $smoothLine = true;


  if ($smoothLine) $printsmoothLine = "true";
  else $printsmoothLine = "false";

  if ($legend) $printlegend = "{position: 'top'}";
  else $printlegend = "{position: 'none'}";

  if ($chartArea == "large") {
    $printchartArea = "{'width': '90%'}";
  }
  elseif ($chartArea == "largelegend") {
    $printchartArea = "{'width': '72%', 'height': '60%'}";
  }
  else {
    $printchartArea = "{'width': '78%', 'height': '85%'}";
  }

  $s = " var " . $name . " = {
              legend: $printlegend ,
              height: '$height',
              width: '$width',
              chartArea: $printchartArea,
              vAxis: { viewWindowMode:'explicit', 'minValue': 0, format: 0, viewWindow:{ min:-5 } },
              smoothLine: $printsmoothLine
          };";
  return $s;
}




/**
 * Print the Javascript JSON request string
 *
 *  options    array          array containing the options
 *  @return  string   the javascript json request string
 */
function googlechart_getJson($options = array()) {

  if (!(is_array($options)))  $options = array();

  if (array_key_exists("name", $options)) $name = (string) $options["name"];
  else $name = DEFAULT_CHART_OPTIONSNAME;

  if (array_key_exists("data", $options)) $data = $options["data"];
  else $data = "timeframe: \"lastday\"";

  if (array_key_exists("special", $options) and $options["special"] == true) $url = "get.php";
  else $url = "get.php"; 

  if (strlen($data) > 0) {
    $data = "chart: \"googlechart\" ," . $data;
  }
  $s = "var " . $name . " = $.ajax({
            url: \"" . $url . "\",
            dataType:\"json\",
            data: { " .  $data . " },
            async: false
            }).responseText;
        var data_" . $name. " = new google.visualization.DataTable(" . $name . ");
          ";
  return $s;
}
