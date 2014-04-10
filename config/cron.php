<?php
/**
 *  ulogd visualizer
 *
 *    Cron jobs
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

$logd_json = new ulogd_json();
$getCronCleanup = $logd_json->getCronCleanup();

if ($getCronCleanup > 0) {
    $con = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
    $sql = "DELETE FROM " . DB_TABLE . " WHERE oob_time_sec < " . $getCronCleanup; 
    $result_db = mysqli_query( $con, $sql );
}


?>