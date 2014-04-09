<?php
/**
 *  ulogd visualizer
 *
 *  Class to handle shortcuts
 *
 *  @author Koen Van Impe <koen.vanimpe@vanimpe.eu>
 *  @package  ulogdviz
 * 
 */


class ulogd_shortcut {



    /**
     * Save a shortcut
     *
     *  get      array      array with all the request data
     *  @return  array        the result
     */
    public function save($get) {
        $ulogd_user = new ulogd_user();
        $params = (string) substr($get["params"], 0, 250);
        $shortcut = (string) substr($get["shortcut"], 0, 20);
        $shortcuttype = (string) substr($get["shortcuttype"], 0, 20);
        if (!($shortcuttype == "graph")) $shortcuttype = "graph";
        $user = $ulogd_user->current();
        $con = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        $prep = mysqli_prepare($con, "INSERT INTO shortcut (params, shortcut, type, user) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($prep, "sssi", $params, $shortcut, $shortcuttype, $user);
        mysqli_stmt_execute($prep);
        mysqli_stmt_close($prep);
        $result = array( "result" => true );
        echo json_encode( $result );
    }



    /**
     * List the existing shortcuts
     *
     *  type   string       what type of shortcuts to retrieve
     *  @return  array        the result
     */
    public function get( $type = "") {
        if ($type == "") {
            $sql = "SELECT shortcut, params FROM shortcut ORDER BY shortcut DESC";
        }
        $con = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        $result = mysqli_query( $con, $sql );
        if (is_array($result)) {
            $result_shortcut = array();
            while ($row = mysqli_fetch_assoc($result)) {
                array_push($result_shortcut, array( "request" => (string) stripslashes($row["params"]), "label" => (string) $row["shortcut"]));
            }

            return $result_shortcut;            
        }
        else {
            return array( "request" => false) ;
        }
    }    
}