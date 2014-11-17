<?php
/**
 *  ulogd visualizer
 *
 *  JSON builder
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

$get = $_GET;

if (is_array($get)) {
  $ulogd_json = new ulogd_json();
  $ulogd_shortcut = new ulogd_shortcut();
  if (array_key_exists("numberOfEntries",$get)) {
    $ulogd_json->numberOfEntries($get);
  }
  elseif (array_key_exists("stats",$get)) {
    $ulogd_json->getStats($get);
  }  
  elseif (array_key_exists("topPort",$get)) {
    $ulogd_json->topPort($get);
  }
  elseif (array_key_exists("topIp",$get)) {
    $ulogd_json->topIp($get);
  }
  elseif (array_key_exists("blacklist",$get)) {
    $ulogd_json->blacklistHits($get);
  }
  elseif (array_key_exists("shortcut",$get)) {
    if ($get["shortcut"] == "get") echo json_encode($ulogd_shortcut->get());
  }  
  elseif (array_key_exists("chart",$get)) {
    if ($get["chart"] == "googlechart")  $ulogd_json->buildDataset($get);
    elseif ($get["chart"] == "csv") $ulogd_json->csv($get);
  }  
  elseif (array_key_exists("map",$get)) {
    $ulogd_json->buildCombinedDataset($get);
  }
  elseif (array_key_exists("table",$get)) {
    $ulogd_json->buildFullDataset($get);
  }

}
?>