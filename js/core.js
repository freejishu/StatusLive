var c = 0;
var t;

function set_page_info() {
    document.title = config_title + " - " + config_title_english;
    $("#title_big").html(config_title);
    $("#title_small").html(config_title_english);

}

function formatTime(second) {
        if(second == null || undefined== second){
            return '';
        }
        var dt = new Date(second);
        result = '';
        result += dt.getFullYear();
        result += '-';
        var month = dt.getMonth() + 1;
        result += month<10?"0"+month:month;
        result += '-';
        /* var day = dt.getDate() + 1; */
        var day = dt.getDate();
        result += day<10?"0"+day:day;
        result += ' ';
        var  hour= dt.getHours();
        result += hour<10?"0"+hour:hour;
        result += ':';
        var  min= dt.getMinutes();
        result += min<10?"0"+min:min;
        result += ':';
        var  sec= dt.getSeconds();
        result += sec<10?"0"+sec:sec;
        
        return  result;
    }

function timedCount() {

    $(".seconds").html((config_auto_refresh_seconds - c) + 's');
    c = c + 1;

    if (c == config_auto_refresh_seconds + 1) {
        load(1);
        c = 0;
        t = setTimeout("timedCount()", 1000);
    } else {
        t = setTimeout("timedCount()", 1000);
    }
}

function time_good_look(int) {
    if (int < 10) {
        return "0" + int;
    } else {
        return int;
    }
}

function event_list_statusStr(statusStr, if_or_not_start) {
    if (statusStr == "up") {
        if (if_or_not_start == 1) {
            return '<span class="success">开始监控</span>';
        } else {
            return '<span class="success">恢复正常</span>';
        }

    } else if (statusStr == "down") {
        return '<span class="danger">发生故障</span>';
    } else {
        return statusStr;
    }
}

function event_list_reasonStr(reasonStr, if_or_not_start, statusStr) {
    if (if_or_not_start == 1) {
        return "开始监控";
    } else if (statusStr == "up") {
        return "恢复正常";
    } else {
        return back_error(reasonStr);
    }
}

function event_list_durationStr(durationStr) {
    durationStr = durationStr.replace("hrs,", "小时");
    durationStr = durationStr.replace("mins", "分钟");
    return durationStr;
}

function index_latest_downtime(latestDownTimeStr) {
    latestDownTimeStr = latestDownTimeStr.replace("It was recorded (for the monitor", "监控点");
    latestDownTimeStr = latestDownTimeStr.replace(") on", " 于");
    latestDownTimeStr = latestDownTimeStr.replace("and the downtime lasted for", "发生故障，持续时间");
    latestDownTimeStr = latestDownTimeStr.replace("hrs,", "小时");
    latestDownTimeStr = latestDownTimeStr.replace("mins.", "分钟。");
    latestDownTimeStr = latestDownTimeStr.replace("No downtime recorded.", "还未发生故障。");
    
    return latestDownTimeStr;
}

function show_chart(monitors_id, i) {
    
    if(config_show_chart === false){
        return;
    }
    if(config_ajax_mode==1){
        var get_url = "core.php?t=1&id=" + monitors_id + "&key=" + config_status_key + "&r=" + Math.random();
    }else if (config_ajax_mode==2){
        var get_url = config_ajax_proxy_domain + "/api/getMonitor/"+config_status_key+"?m="+monitors_id + "&_=" + Math.random();
    }
    
    $.ajax({
        url: get_url,
        type: "GET",
        dataType: "json", 
        success: function(data) {
            $("#div_chart_"+i).html('<br /><br /><canvas id="lag_show_chart_' + i + '"></canvas><br />');
            
            var event_list='';
            
            for (var ii = 0; ii < data.monitor.logs.length; ii++) {
                    var if_or_not_start = 0;
                    if (ii == 0) {
                        if_or_not_start = 1;
                    }
                    event_list = "<tr><td>" + event_list_statusStr(data.monitor.logs[ii].label, if_or_not_start) + "</td><td>" + data.monitor.logs[ii].date +"</td><td>" + event_list_reasonStr(data.monitor.logs[ii].reason, if_or_not_start) + "</td><td>" + event_list_durationStr(data.monitor.logs[ii].duration) + "</td></tr>" + event_list;
                }
                
                $("#logs_"+data.monitor.monitorId).html(event_list);
                
            var datetime = new Array(0)
            var value = new Array(0)
            for (var ii = 0; ii < data.monitor.responseTimes.length; ii++) {
                datetime.push(data.monitor.responseTimes[ii].datetime);
                value.push(data.monitor.responseTimes[ii].value)
            }
            datetime.reverse();
            value.reverse();

            var ctxL = document.getElementById('lag_show_chart_' + i).getContext('2d');
            var myLineChart = new Chart(ctxL, {
                type: 'line',
                data: {
                    labels: datetime,
                    datasets: [{
                            label: "响应时间",
                            fillColor: "rgba(220,220,220,0.2)",
                            strokeColor: "rgba(220,220,220,1)",
                            pointColor: "rgba(220,220,220,1)",
                            pointStrokeColor: "#fff",
                            pointHighlightFill: "#fff",
                            pointHighlightStroke: "rgba(220,220,220,1)",
                            data: value
                        }
                    ]
                },
                options: {
                    responsive: true
                }
            });

        }

    });

}


function load(clear_table) {
    $(".seconds").html("ing");
    $(".fa-refresh").addClass('refresh_animation');
    if(config_ajax_mode==1){
        var get_url = "core.php?key="+config_status_key + "&r=" + Math.random();
    }else if(config_ajax_mode==2){
        var get_url = config_ajax_proxy_domain + "/api/getMonitorList/"+config_status_key+"?page=1&_=" + Math.random();
    }
    $.ajax({
        url: get_url,
        type: "GET",
        dataType: "json", //指定服务器返回的数据类型
        success: function(data) {
            var date = new Date(),
                year = date.getFullYear(),
                month = date.getMonth() + 1,
                day = date.getDate(),
                hour = date.getHours(),
                minute = date.getMinutes(),
                second = date.getSeconds(),
                time_text = year + '年' + month + '月' + day + '日 ' + time_good_look(hour) + ':' + time_good_look(
                    minute) + ':' + time_good_look(second);
                    //console.log(data.psp.statistics)

            $("#report_time").html(time_text);
            $("#up_server").html(data.statistics.counts.up);
            $("#down_server").html(data.statistics.counts.down);
            $("#paused_server").html(data.statistics.counts.paused);
            if (clear_table == 1) {
                $("#website_list").html("");
                $("#datacenter_list").html("");

            }
            var data_table = "";
            for (var i = 0; i < data.days.length; i++) {
                data_table = data_table + '<td scope="col">' + data.days[i] + "</td>";
            }
            //console.log(data_table);
            $("#latest_downtime").html("最近一次故障：" + index_latest_downtime(data.statistics.latest_downtime));

            for (var i = 0; i < data.psp.totalMonitors; i++) {
                if (data.psp.monitors[i].type == "http(s)") {
                    var table_key = "website_list",
                        tag_website = 1;
                } else if (data.psp.monitors[i].type == "ping") {
                    var table_key = "datacenter_list",
                        tag_datacenter = 1;
                } else {
                    var table_key = "website_list",
                        tag_website = 1;
                }

                var last_seven_day_ratio = "",
                    last_seven_day_ratio_html = "";
                for (var ii = 0; ii < data.psp.monitors[i].dailyRatios.length; ii++) {
                    last_seven_day_ratio = last_seven_day_ratio + '<span class="table-status-item ' + data.psp.monitors[i].dailyRatios[ii].label + '-bg">' + data.psp.monitors[i].dailyRatios[ii].ratio +'</span>';
                    last_seven_day_ratio_html = last_seven_day_ratio_html + '<td><span class="table-status-item ' + data.psp.monitors[i].dailyRatios[ii].label + '-bg">' + data.psp.monitors[i].dailyRatios[ii].ratio + '</span></td>';
                }

                $("#" + table_key).html($("#" + table_key).html() +
                    '<tr><th scope="row"><span class="bullet ' +
                    data.psp.monitors[i].weeklyRatio.label + '-bg"></span></th><td class="td_new_font_size ' +data.psp.monitors[i].weeklyRatio.label + '">' + data.psp.monitors[i].weeklyRatio.ratio +
                    '%</td><td class="td_new_font_size show_more_information" data-toggle="modal" data-target="#more_information_' +
                    i + '" onclick="show_chart(' + data.psp.monitors[i].monitorId + ',' + i + ')"><a>' +
                    data.psp.monitors[i].name +
                    '</a></td><td style="min-width:600px">' + last_seven_day_ratio + '</td></tr>');


                if (clear_table == 0) {
                    $("#all_modal").html($("#all_modal").html() +
                        '<div class="modal fade" id="more_information_' + i +
                        '" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true"><div class="modal-dialog modal-lg" role="document"><div class="modal-content"><div class="modal-header"><h4 class="modal-title w-100" id="myModalLabel">' +
                        data.psp.monitors[i].name +
                        '</h4><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button></div><div class="modal-body" id="more_information_body_' +
                        i + '">Loading...</div></div></div></div>');
                }
                var event_list = "";
                $('#more_information_body_' + i).html("报告生成时间：" + time_text +
                    '&nbsp;&nbsp;<i class="fa fa-refresh" aria-hidden="true" style="font-size: 1rem;"></i>&nbsp;<span class="seconds">60s</span><div class="div_chart" id="div_chart_'+i+'"><div class="spinner" style="top:0px"><div class="double-bounce1"></div><div class="double-bounce2"></div></div></div><table class="table table-borderless table-sm" id="ratio_table"><thead><tr>' +
                    data_table +
                    '</tr></thead><tbody><tr>' + last_seven_day_ratio_html +
                    '</tr></tbody></table><table class="table table-borderless table-sm"><thead><tr><th scope="col">事件</th><th scope="col">时间</th><th scope="col">原因</th><th scope="col">持续时间</th></tr></thead><tbody id="logs_' + data.psp.monitors[i].monitorId + '"></tbody></table>');
                if ($('#more_information_' + i).css("display")!=="none" && config_show_chart === true){
                    show_chart(data.psp.monitors[i].id, i);
                }

            }

            if (!tag_website) {
                $("#card_website").hide();
            }
            if (!tag_datacenter) {
                $("#card_datacenter").hide();
            }
            for (i = 0; i < 3; i++) {
                if (i === 0) {
                    var time_key = 1,
                        json_key = "l1";
                } else if (i == 1) {
                    var time_key = 7,
                        json_key = "l7";
                } else if (i == 2) {
                    var time_key = 30,
                        json_key = "l30";
                }
                $("#pass_" + time_key + "d").html("<strong>" + data.statistics.uptime[json_key].ratio +
                    "</strong>");
                $("#pass_" + time_key + "d_td").addClass(data.statistics.uptime[json_key].label);

            }

            if (config_warning_flash == true) {
                $(".warning").addClass("animated flash");
                $(".warning-bg").addClass("animated flash");
                setTimeout('$(".warning").removeClass("animated flash")', 1000);
                setTimeout('$(".warning-bg").removeClass("animated flash")', 1000);
            }

            if(config_show_chart === false){
                $('.div_chart').hide();
            }
            $("#loading").hide();
            $("#all_card").slideDown(700);
            $(".seconds").html( config_auto_refresh_seconds + 's');
            $(".fa-refresh").removeClass('refresh_animation');
        }


    });
}

function back_error(text) {
    if (text = "Connection Timeout") {
        return "连接超时";
    } else if (text = "No Response") {
        return "无响应";
    } else if (text == "Continue") {
        return "100 Continue";
    } else if (text == "Switching Protocols") {
        return "101 Switching Protocols";
    } else if (text == "Processing") {
        return "102 Processing";
    } else if (text == "OK") {
        return "200 OK";
    } else if (text == "Created") {
        return "201 Created";
    } else if (text == "Accepted") {
        return "202 Accepted";
    } else if (text == "Non-Authoritative Information") {
        return "203 Non-Authoritative Information";
    } else if (text == "No Content") {
        return "204 No Content";
    } else if (text == "Reset Content") {
        return "205 Reset Content";
    } else if (text == "Partial Content") {
        return "206 Partial Content";
    } else if (text == "Multi-Status") {
        return "207 Multi-Status";
    } else if (text == "Multiple Choices") {
        return "300 Multiple Choices";
    } else if (text == "Moved Permanently") {
        return "301 Moved Permanently";
    } else if (text == "Move temporarily") {
        return "302 Move temporarily";
    } else if (text == "See Other") {
        return "303 See Other";
    } else if (text == "Not Modelse ified") {
        return "304 Not Modelse ified";
    } else if (text == "Use Proxy") {
        return "305 Use Proxy";
    } else if (text == "Switch Proxy") {
        return "306 Switch Proxy";
    } else if (text == "Temporary Redirect") {
        return "307 Temporary Redirect";
    } else if (text == "Bad Request") {
        return "400 Bad Request";
    } else if (text == "Unauthorized") {
        return "401 Unauthorized";
    } else if (text == "Payment Required") {
        return "402 Payment Required";
    } else if (text == "Forbidden") {
        return "403 Forbidden";
    } else if (text == "Not Found") {
        return "404 Not Found";
    } else if (text == "Method Not Allowed") {
        return "405 Method Not Allowed";
    } else if (text == "Not Acceptable") {
        return "406 Not Acceptable";
    } else if (text == "Proxy Authentication Required") {
        return "407 Proxy Authentication Required";
    } else if (text == "Request Timeout") {
        return "408 Request Timeout";
    } else if (text == "Conflict") {
        return "409 Conflict";
    } else if (text == "Gone") {
        return "410 Gone";
    } else if (text == "Length Required") {
        return "411 Length Required";
    } else if (text == "Precondition Failed") {
        return "412 Precondition Failed";
    } else if (text == "Request Entity Too Large") {
        return "413 Request Entity Too Large";
    } else if (text == "Request-URI Too Long") {
        return "414 Request-URI Too Long";
    } else if (text == "Unsupported Media Type") {
        return "415 Unsupported Media Type";
    } else if (text == "Requested Range Not Satisfiable") {
        return "416 Requested Range Not Satisfiable";
    } else if (text == "Expectation Failed") {
        return "417 Expectation Failed";
    } else if (text == "There are too many connections from your internet address") {
        return "421 There are too many connections from your internet address";
    } else if (text == "Unprocessable Entity") {
        return "422 Unprocessable Entity";
    } else if (text == "Locked") {
        return "423 Locked";
    } else if (text == "Failed Dependency") {
        return "424 Failed Dependency";
    } else if (text == "Unordered Collection") {
        return "425 Unordered Collection";
    } else if (text == "Upgrade Required") {
        return "426 Upgrade Required";
    } else if (text == "Retry With") {
        return "449 Retry With";
    } else if (text == "Internal Server Error") {
        return "500 Internal Server Error";
    } else if (text == "Not Implemented") {
        return "501 Not Implemented";
    } else if (text == "Bad Gateway") {
        return "502 Bad Gateway";
    } else if (text == "Service Unavailable") {
        return "503 Service Unavailable";
    } else if (text == "Gateway Timeout") {
        return "504 Gateway Timeout";
    } else if (text == "HTTP Version Not Supported") {
        return "505 HTTP Version Not Supported";
    } else if (text == "Variant Also Negotiates") {
        return "506 Variant Also Negotiates";
    } else if (text == "Insufficient Storage") {
        return "507 Insufficient Storage";
    } else if (text == "Bandwidth Limit Exceeded") {
        return "509 Bandwidth Limit Exceeded";
    } else if (text == "Not Extended") {
        return "510 Not Extended";
    } else if (text == "Unparseable Response Headers") {
        return "600 Unparseable Response Headers";
    } else {
        return text;
    }
}