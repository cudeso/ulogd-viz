<?php
/**
 *  ulogd visualizer
 *
 *  JSON builder
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

    $type = (string) $get["type"];

    if ($type == "shortcut") {
        $ulogd_shortcut = new ulogd_shortcut();
        $ulogd_shortcut->save($get);
    }
}

?>