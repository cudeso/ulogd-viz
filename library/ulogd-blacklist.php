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
        $s = array( "127.0.0.1" 
                ); 
        return $s;
    }



    /**
     * Count the number of entries in the blacklist
     *
     *  @return  integer        the number of entries
     */
    public function count() {
        return 1;
    }
}