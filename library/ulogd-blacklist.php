<?php
/**
 *  ulogd visualizer
 *
 *  Class to return blacklist data
 *
 *  @author Koen Van Impe <koen.vanimpe@vanimpe.eu>
 *  @package  ulogdviz
 * 
 */


class ulogd_blacklist {


    /**
     * Get the content of the blacklists
     *
     *  @return  array        the result
     */
    public function get() {
        $blacklist = array();
        $handle = fopen( DEFAULT_BLACKLIST , "r" );
        if ($handle) {
            while(!feof($handle)){
                $ip = fgets($handle);
                array_push($blacklist, $ip);
            }
        }
        fclose($handle);
        return $blacklist;
    }



    /**
     * Count the number of entries in the blacklist
     *
     *  @return  integer        the number of entries
     */
    public function count() {
        $blacklist = $this->get();
        if (is_array($blacklist) and count($blacklist) > 0) {
            return count($blacklist);
        }
        else return 0;
    }
}