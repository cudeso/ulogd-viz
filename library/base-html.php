<?php
/**
 *  ulogd visualizer
 *
 *  Helper classes for the HTML output
 *
 *  @author Koen Van Impe <koen.vanimpe@vanimpe.eu>
 *  @package  ulogdviz
 * 
 */



/**
 * Print the HTML HEAD
 *
 *  page    string          the current page
 *  @return  nothing
 */
function ulogd_printhtmlHead($page = "") {
?>
<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo APP_TITLE; ?></title>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLEMAPS_API; ?>&sensor=true"></script>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
<!--    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>-->
    <script src="js/jquery-1.10.2.js"></script>
    <script src="js/jquery.form.js"></script>
    <script src="js/markerclusterer_packed.js" type="text/javascript"></script>
    <script src="js/ulogd.js"></script>
    <script src="js/plugins/dataTables/jquery.dataTables.js"></script>
    <script src="js/plugins/dataTables/dataTables.bootstrap.js"></script>
    <script src="js/plugins/dataTables/dataTables.tableTools.min.js"></script>

    <link href="css/bootstrap.css" rel="stylesheet">
    <!--<link href="css/bootstrap.min.css" rel="stylesheet">-->
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="css/sb-admin.css" rel="stylesheet">
    <link href="css/ulogd.css" rel="stylesheet">
    <link href="css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">

    
    <script src="tabletools/js/dataTables.tableTools.js" type="text/javascript"></script>
    <link href="tabletools/css/dataTables.tableTools.min.css" rel="stylesheet">

</head>
<?php
}




/**
 * Print the HTML top navigation menu
 *
 *  page    string          the current page
 *  @return  nothing
 */
function ulogd_printhtmlTopMenu($page = "") {
    $page = (string) trim(strtolower($page));
    $page = substr($page, strlen(APP_WEBROOT));
?>        
    <div class="sidebar-collapse">
        <ul class="nav" id="side-menu">               
            <li <?php if ($page == "index.php") echo "class=\"active\""; ?> >
                <a href="<?php echo APP_WEBROOT; ?>"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
            </li>
            <li <?php if ($page == "generate.php") echo "class=\"active\""; ?> >
                <a href="<?php echo APP_WEBROOT; ?>generate.php"><i class="fa fa-bar-chart-o fa-fw"></i> Charts<span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li><a href="<?php echo APP_WEBROOT; ?>generate.php?timeframe=lastday&amp;formoutput=output_chart">Last 24h</a></li>
                    <li><a href="<?php echo APP_WEBROOT; ?>generate.php?timeframe=last3day&amp;formoutput=output_chart">Last 3 days</a></li>
                    <li><a href="<?php echo APP_WEBROOT; ?>generate.php?timeframe=lastweek&amp;formoutput=output_chart">Last week</a></li>
                    <li><a href="<?php echo APP_WEBROOT; ?>generate.php?timeframe=lastmonth&amp;formoutput=output_chart">Last month</a></li> 
                </ul>
                <!-- /.nav-second-level -->
            </li>
            <li>
                <a href="tables.html"><i class="fa fa-globe fa-fw"></i> Maps<span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li>
                        <a href="<?php echo APP_WEBROOT; ?>map.php">Last 24h</a>
                    </li>
                </ul>                        
            </li>
            <li>
                <a href="<?php echo APP_WEBROOT; ?>table.php"><i class="fa fa-list fa-fw"></i> List records</a>
            </li>
            <li>
                <a href="<?php echo APP_WEBROOT; ?>tools.php"><i class="fa fa-gear fa-fw"></i> Tools</a>
            </li> 
            <li>
                <a href="<?php echo APP_WEBROOT; ?>statistics.php"><i class="fa fa-wrench fa-fw"></i> Statistics</a>
            </li>            
        </ul>
        <!-- /#side-menu -->
    </div>
    <!-- /.sidebar-collapse -->
<?php
}



/**
 * Print the HTML left menu
 *
 *  page    string          the current page 
 *  @return  nothing
 */
function ulogd_printhtmlLeftMenu($page = "") {
?>
            <ul class="nav navbar-top-links navbar-right">
                <!-- /.dropdown -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-tasks fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-alerts" id="shortcut">
                        <li>
                            <a href="#">
                                <div>
                                    <i class="fa fa-bar-chart-o fa-fw"></i> Last 24h
                                    <span class="pull-right text-muted small">graph + table</span>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <div>
                                    <i class="fa fa-bar-chart-o fa-fw"></i> Last week
                                    <span class="pull-right text-muted small">graph + table</span>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <i class="fa fa-globe fa-fw"></i> Last 24h
                                    <span class="pull-right text-muted small">map</span>
                                </div>
                            </a>
                        </li>
                    </ul>
                    <script type="text/javascript">
                        updateShortcut();
                    </script>
                </li>
                <!-- /.dropdown -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="#"><i class="fa fa-user fa-fw"></i> User Profile</a>
                        </li>
                        <li><a href="#"><i class="fa fa-gear fa-fw"></i> Settings</a>
                        </li>
                        <li class="divider"></li>
                        <li><a href="login.html"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                        </li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>
<?php    
}



/**
 * Print the HTML BODY start
 *
 *  page    string          the current page  
 *  @return  nothing
 */
function ulogd_printhtmlBodyStart($page = "") {
?>    
<body>

    <div id="wrapper">
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?php echo APP_WEBROOT; ?>"><?php echo APP_TITLE . " " . APP_VERSION; ?></a>
            </div>
            <?php ulogd_printhtmlLeftMenu($page); ?>

        </nav>

        <nav class="navbar-default navbar-static-side" role="navigation">
            <?php ulogd_printhtmlTopMenu($page); ?>            
        </nav>


        <div id="page-wrapper">
<?php
} 




/**
 * Print the HTML end
 *
 *  page    string          the current page 
 *  @return  nothing
 */
function ulogd_printhtmlEnd($page = "") {
?>      
        </div>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
        <script src="js/sb-admin.js"></script>
    </body>
</html>
<?php
}



?>