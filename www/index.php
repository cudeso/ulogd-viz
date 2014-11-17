<?php
/**
 *  ulogd visualizer
 *
 *    Main page
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

?>

<script type="text/javascript">
    google.load('visualization', '1', {'packages':['corechart']});
    google.load('visualization', '1', {'packages':['table']});

    // Set a callback to run when the Google Visualization API is loaded.
    google.setOnLoadCallback(drawChart); 

    function drawChart() {
      <?php 
        echo googlechart_getOptions( array( "name" => "options_day", "chartArea" => "default", "legend" => false));
        echo googlechart_getJson(array( "name" => "json_day"));
        echo googlechart_getJson(array( "name" => "json_week",  "data" => "timeframe: 'lastweek' "));
        echo googlechart_getJson(array( "name" => "json_month", "data" => "timeframe: 'lastmonth' "));        
      ?>

      var chart_day = new google.visualization.LineChart(document.getElementById('chart_day'));
      chart_day.draw(data_json_day, options_day);
      var chart_week = new google.visualization.LineChart(document.getElementById('chart_week'));
      chart_week.draw(data_json_week, options_day);
      var chart_month = new google.visualization.LineChart(document.getElementById('chart_month'));
      chart_month.draw(data_json_month, options_day);
    }
</script>

            <div class="row">
                <h3>Dashboard
                    <small>Shortcuts to everywhere</small>
                </h3>
            </div>
            <div class="row margin-top">
              <div class="col-lg-12">
                <div class="col-lg-12">
                  <div class="panel panel-default">
                    <div class="panel-heading">
                      Search the logs
                    </div>
                    <div class="panel-body">
                      <form action="generate.php" role="form" id="dashboard-generate" method="get" class="form-inline">

                        <div class="row">
                          <div class="col-lg-3">
                            <div class="form-group">
                                <label>Timeframe</label>
                                <select class="form-control" id="timeframe" name="timeframe" >
                                    <option value="last15min">Last 15 minutes</option>
                                    <option value="last30min">Last 30 minutes</option>
                                    <option value="lasthour">Last hour</option>
                                    <option value="last4hour">Last 4 hours</option>
                                    <option value="last12hour">Last 12 hours</option>
                                    <option value="lastday" selected >Last 24 hours</option>
                                    <option value="last2day">Last 2 days</option>
                                    <option value="last3day">Last 3 days</option>
                                    <option value="lastweek">Last week</option>
                                    <option value="lastmonth">Last month</option>
                                    <option value="last3month">Last 3 months</option>
                                </select>
                            </div>
                          </div>
                          <div class="col-lg-3">
                              <label>Filter on ports</label>
                              <div id="port-list">
                              </div>

                            <div id="controls" class="text-right margin-top">
                              <a id="add-ports" href="#" class="btn btn-default btn-success btn-sm"><span class="glyphicon glyphicon-plus-sign"></span> Add port</a>
                            </div>                            
                          </div>

                          <div class="col-lg-4">
                            <div class="form-group">
                                <label>Output</label><br />
                                <label class="radio-inline"><i class="fa fa-bar-chart-o fa-fw"></i> 
                                    <input type="radio" name="formoutput" id="output_chart" value="output_chart" checked>Charts and tables
                                </label><br />
                                <label class="radio-inline"><i class="fa fa-globe fa-fw"></i> 
                                    <input type="radio" name="formoutput" id="output_map" value="output_map">Maps
                                </label><br />
                                <label class="radio-inline"><i class="fa fa-table fa-fw"></i> 
                                    <input type="radio" name="formoutput" id="output_table" value="output_table">Table
                                </label>                                
                            </div>
                          </div>

                          <div class="col-lg-2">
                            <button type="submit" class="btn btn-primary">Generate</button>
                          </div>
                        </div>
                        <div class="row">
                           <div class="col-lg-6">
                              <label>Filter on IPs</label>
                                <div id="ip-list"> </div>
                            <div id="controls" class="text-right margin-top">
                              <a id="add-ip" href="#" class="btn btn-default btn-success btn-sm"><span class="glyphicon glyphicon-plus-sign"></span> Add IP</a>
                            </div>                                  
                          </div>

                          <div class="col-lg-4">
                            <div class="form-group">
                                <label>Isolate results</label><br />
                                <label class="radio-inline">
                                    <input type="radio" name="isolateresult" id="isolate_port" value="isolate_port" checked>Isolate based on ports
                                </label><br />
                                <label class="radio-inline">
                                    <input type="radio" name="isolateresult" id="isolate_ip" value="isolate_ip">Isolate based on IPs
                                </label><br />
                            </div>
                          </div>

                        </div>
                      </form>
                    </div>
                    <div class="panel-footer">
                      <p>Leave the port field blank if you want to filter on <strong>protocol</strong> only. <br />
                        Put the <strong>ICMP</strong> code in the port field if you want to filter on specific ICMP codes.</p>
                    </div>
                  </div>
              </div>
              </div>
            </div> 

            <div class="row">
               <div class="col-lg-12">
                    <div class="col-lg-6 dashboard-portlet" id="top_src_port">
                      <div class="alert alert-success">
                        <h4>Trending source ports <br /><small>Last 24 hours against previous 24 hours</small></h4>
                        <h3 class="text-right" id="trending_src_ports"></h3>
                      </div>
                    </div>
                    <div class="col-lg-6 dashboard-portlet" id="top_dst_port">
                      <div class="alert alert-success">
                        <h4>Trending destination ports <br /><small>Last 24 hours against previous 24 hours</small></h4>
                        <h3 class="text-right" id="trending_dst_ports"></h3>
                      </div>
                    </div>
                </div>
            </div>
            <div class="row">
               <div class="col-lg-12">
                    <div class="col-lg-3">
                      <div class="alert alert-success">
                        <h4>Number of entries <br /><small>all time</small></h4>
                        <h3 class="text-right" id="numberofentries_alltime"></h3>
                      </div>
                    </div>

                    <div class="col-lg-3">
                      <div class="alert alert-success">
                        <h4>Number of entries <br /><small>last 24 hours</small></h4>
                        <h3 class="text-right" id="numberofentries_alltime_today"></h3>
                      </div>
                    </div>

                    <div class="col-lg-3 dashboard-portlet" id="top_port">
                      <div class="alert alert-success">
                        <h4>Top port <br /><small>last 24 hours</small></h4>
                        <h3 class="text-right" id="topPort_today"></h3>
                      </div>
                    </div>

                    <div class="col-lg-3 dashboard-portlet" id="top_ip">
                      <div class="alert alert-success">
                        <h4>Top IP <br /><small>last 24 hours</small></h4>
                        <h3 class="text-right" id="topIp_today"></h3>
                      </div>
                    </div>                    
                </div>
            </div>


            <div class="row">
                <div class="col-lg-12">
                    <div class="col-lg-4">
                       <div class="panel panel-info">
                            <div class="panel-heading">
                                Hits for Last 24 Hours
                            </div>
                            <div class="panel-body" id="chart_day">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                       <div class="panel panel-info">
                            <div class="panel-heading">
                                Hits for Last week
                            </div>
                            <div class="panel-body" id="chart_week">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                       <div class="panel panel-info">
                            <div class="panel-heading">
                                Hits for Last month
                            </div>
                            <div class="panel-body" id="chart_month">
                            </div>
                        </div>
                    </div>                    
                </div>
            </div>

            <div class="row">
              <div class="col-lg-12">
                <div class="col-lg-6 dashboard-portlet" id="top_port-5">
                  <div class="alert alert-success">
                    <h4>Top 5 Source Ports<br /><small>24 hours</small></h4>
                    <h3 class="text-right" id="topPort_today_5"></h3>
                  </div>
                </div>

                <div class="col-lg-6 dashboard-portlet" id="top_ip-5">
                  <div class="alert alert-success">
                    <h4>Top 5 Source IPs<br /><small>24 hours</small></h4>
                    <h3 class="text-right" id="topIp_today_5"></h3>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
               <div class="col-lg-12">
                    <div class="col-lg-3">
                      <div class="alert alert-danger">
                        <h4>Blacklist <small>(<span id="blacklist_count"></span>)</small> hits<br /><small>24 hours</small></h4>
                        <h3 class="text-right" id="blacklist_today"></h3>
                      </div>
                    </div>

                    <div class="col-lg-3 dashboard-portlet" id="ssh_warning">
                      <div class="alert alert-warning">
                        <h4>SSH (tcp/22) <br /><small>last 24 hours</small></h4>
                        <h3 class="text-right" id="topPort_ssh_today"></h3>
                      </div>
                    </div>

                    <div class="col-lg-3 dashboard-portlet" id="icmp_today">
                      <div class="alert alert-info">
                        <h4>ICMP echo (type 8) <br /><small>last 24 hours</small></h4>
                        <h3 class="text-right" id="topPort_icmp_today"></h3>
                      </div>
                    </div>

                    <div class="col-lg-3 dashboard-portlet" id="dns_today">
                      <div class="alert alert-danger">
                        <h4>DNS (udp/53)<br /><small>last 24 hours</small></h4>
                        <h3 class="text-right" id="topPort_dns_today"></h3>
                      </div>
                    </div>                    
                </div>
            </div>

        </div>
    <script>

        /* ***
            Trending boxes 
           ***
        */
        $.getJSON('get.php', { topPort: "lastday", portcount: 5, trending: true, direction: "dport"}, function(json) {
            url_topPort_dst = "generate.php?timeframe=lastday&formoutput=output_chart&";
            html = "";
            if (json[0].noresults == true)  {
              html = "<small>No results</small>";
            }
            else {            
              $.each(json, function(key, i) {
                html = html + i.protocol + "/" + i.port + "<div class=\"small\" style=\"width:150px;\"><small>" + i.increase + " % (" + i.qt_from + "->" + i.qt_to + " pkts)</small></div> <br /> ";
                url_topPort_dst = url_topPort_dst + "protocol[]=" + i.protocol + "&port[]=" + i.port + "&";
              });

              $(document).ready(function() { 
                $('#top_dst_port').on('click', function(e){
                  document.location.href=url_topPort_dst;
                });
              });
            }
            $("#trending_dst_ports").empty().append( html );
        });

        $.getJSON('get.php', { topPort: "lastday", portcount: 5, trending: true, direction: "sport"}, function(json) {
            url_topPort_src = "generate.php?timeframe=lastday&formoutput=output_chart&";
            html = "";
            if (json[0].noresults == true)  {
              html = "<small>No results</small>";
            }
            else {
              $.each(json, function(key, i) {
                html = html + i.protocol + "/" + i.port + "<div class=\"small\" style=\"width:150px;\"><small>" + i.increase + " % (" + i.qt_from + "->" + i.qt_to + " pkts)</small></div> <br /> ";
                url_topPort_src = url_topPort_src + "protocol[]=" + i.protocol + "&port[]=" + i.port + "&";
              });              

              $(document).ready(function() { 
                $('#top_src_port').on('click', function(e){
                  document.location.href=url_topPort_src;
                });
              });              
            }
            $("#trending_src_ports").empty().append( html );
        });

        /* ***
            Number of entries
           ***
        */
        $.getJSON('get.php', { numberOfEntries: "all"}, function(json) {
            $("#numberofentries_alltime").empty().append( json.result );
        });

        $.getJSON('get.php', { numberOfEntries: "lastday"}, function(json) {
            $("#numberofentries_alltime_today").empty().append( json.result );
        });

        /* ***
            Top Port, Top 5-Port , Top IP and Top 5-IP
           ***
        */
        $.getJSON('get.php', { topPort: "lastday"}, function(json) {
          html = json[0].protocol + "/" + json[0].port + " <br /><small>" + json[0].qt + "</small>";
          url_topPort_today = "generate.php?timeframe=lastday&protocol[]=" + json[0].protocol + "&port[]=" + json[0].port + "&formoutput=output_chart";
          $("#topPort_today").empty().append( html);
          $(document).ready(function() { 
            $('#top_port').on('click', function(e){
              document.location.href=url_topPort_today;
            });
          });
        });   

        $.getJSON('get.php', { topPort: "lastday", portcount: 5}, function(json) {
            url_topPort_today_5 = "generate.php?timeframe=lastday&formoutput=output_chart&";
            html = "";
            $.each(json, function(key, i) {
              html = html + i.protocol + "/" + i.port + " <div class=\"small\"><small>" + i.qt + "</small></div> <br />";
              url_topPort_today_5 = url_topPort_today_5 + "protocol[]=" + i.protocol + "&port[]=" + i.port + "&";
            });
            $("#topPort_today_5").empty().append( html );

            $(document).ready(function() { 
              $('#top_port-5').on('click', function(e){
                document.location.href=url_topPort_today_5;
              });
            });
        });

        $.getJSON('get.php', { topIp: "lastday"}, function(json) {
            html = json[0][0].ip + " <br /><small>" + json[0][0].qt + "</small>";
            url_topIp_today = "generate.php?timeframe=lastday&ip[]=" + json[0][0].ip + "&ipflow[]=source&formoutput=output_chart";
            $("#topIp_today").empty().append( html);
            $(document).ready(function() { 
              $('#top_ip').on('click', function(e){
                document.location.href=url_topIp_today;
              });
            });
        });

        $.getJSON('get.php', { topIp: "lastday", ipcount: 5}, function(json) {
            url_topIp_today_5 = "generate.php?timeframe=lastday&formoutput=output_chart&isolateresult=isolate_ip&";
            html = "";
            $.each(json[0], function(key, i) {
              html = html + i.ip + " <div class=\"small\"><small>" + i.qt + "</small></div> <br />";
              url_topIp_today_5 = url_topIp_today_5 + "ipinclude[]=include&ipflow[]=source&ip[]=" + i.ip + "&";
            });
            $("#topIp_today_5").empty().append( html );

            $(document).ready(function() { 
              $('#top_ip-5').on('click', function(e){
                document.location.href=url_topIp_today_5;
              });
            });  
        });

        /* ***
            Specific ports
           ***
        */
        $.getJSON('get.php', { topPort: "lastday", port: 22, protocol: "tcp" }, function(json) {
          $("#topPort_ssh_today").empty().append( json[0].qt );
          $(document).ready(function() { 
            $('#ssh_warning').on('click', function(e){
              url = "generate.php?timeframe=lastday&protocol[]=tcp&port[]=22&formoutput=output_chart";
              document.location.href=url;
            });
          });
        });

        $.getJSON('get.php', { topPort: "lastday", protocol: "icmp", port: 8 }, function(json) {
            $("#topPort_icmp_today").empty().append( json[0].qt );
            $(document).ready(function() { 
              $('#icmp_today').on('click', function(e){
                url = "generate.php?timeframe=lastday&protocol[]=icmp&port[]=8&formoutput=output_chart";
                document.location.href=url;
              });
            });            
        });

        $.getJSON('get.php', { topPort: "lastday", port: 53, protocol: "udp" }, function(json) {
          $("#topPort_dns_today").empty().append( json[0].qt );
          $(document).ready(function() { 
            $('#dns_today').on('click', function(e){
              url = "generate.php?timeframe=lastday&protocol[]=udp&port[]=53&formoutput=output_chart";
              alert(url);
              document.location.href=url;
            });
          });            
        });

        $.getJSON('get.php', { blacklist: "lastday" }, function(json) {
            $("#blacklist_today").empty().append( json.hits );
            $("#blacklist_count").empty().append( json.count + " entries" );
        });
        
        // Initialize the page
        $("#add-ports").click( function(){ $("#port-list").append( getAddPort() ); });
        $("#add-ip").click( function(){ $("#ip-list").append( getAddIp() ); });        
        $("#output_chart").click( function(){ $("#dashboard-generate").attr("action", "generate.php")});
        $("#output_map").click( function(){ $("#dashboard-generate").attr("action", "map.php")}); 
        $("#output_table").click( function(){ $("#dashboard-generate").attr("action", "table.php")});                
        //$("#port-list").append( getAddPort() ).append( getAddPort() );
        //$("#ip-list").append( getAddIp() ).append( getAddIp() );

        // Return the HTML string to add a port
        function getAddPort() {
          s = "<div class='form-group'>" +
                "<select class='input-mini form-control' id='protocol' name='protocol[]'>" + 
                  "<option value='none'> </option><option value='tcp' selected>TCP</option><option value='udp'>UDP</option><option value='icmp'>ICMP</option><option value='any'>ANY</option>" + 
                "</select>" + 
                "<input class='input-mini form-control' type='text' id='port' name='port[]' size='5' maxlength='5' placeholder='Port'>" + 
              "</div>";
          return s;
        }

        // Return the HTML string to add an IP
        function getAddIp() {
          s = "<div class='form-group'>" +
                  "<select class='input-mini form-control' id='ipinclude' name='ipinclude[]'><option value='include' selected>Include</option><option value='exclude'>Except</option></select>" +           
                  "<input class='input-mini form-control' id='ip' name='ip[]' type='text' size='15' maxlength='15' placeholder='A single IP' /> " +
                  "<select class='input-mini form-control' id='ipflow' name='ipflow[]'><option value='source' selected>Source</option><option value='dest'>Destination</option><option value='sourcedest'>Source + Destination</option></select>" + 
              "</div>";
          return s;
        }        
    </script>
<?php
ulogd_printhtmlEnd();
?>
