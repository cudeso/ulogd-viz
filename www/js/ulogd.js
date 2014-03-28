function updateShortcut() {
    $.getJSON('get.php', { shortcut: "get", rand: Math.floor((Math.random()*10000)+1) }, function(json) {
        html = "";
        basepage = "generate.php";
        $.each(json, function(key, i) {
          html = html + "<li><a href=\"" + basepage + "?" + $.trim(i.request) + "\"><div><i class=\"fa fa-bar-chart-o fa-fw\"></i> ";
          html = html + i.label + "<span class=\"pull-right text-muted small\">graph + table</span></div></a></li>";
        });                            
        $("#shortcut").empty().append( html );
    });    
}
