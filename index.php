<!DOCTYPE html>
<html class="full-height">
    
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>StatusLive</title>
        <link rel="stylesheet" href="css/font-awesome-4.7.0/css/font-awesome.css">
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link href="css/mdb.min.css" rel="stylesheet">
        <style>
        body {
            background: #efefef;
        }
        .flex-1 {
            flex: 1;
        }
        .spinner {
            width: 60px;
            height: 60px;
            position: relative;
            margin: 100px auto;
            top: 150px;
        }
        .double-bounce1, .double-bounce2 {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background-color: #67CF22;
            opacity: 0.6;
            position: absolute;
            top: 0;
            left: 0;
            -webkit-animation: bounce 2.0s infinite ease-in-out;
            animation: bounce 2.0s infinite ease-in-out;
        }
        .double-bounce2 {
            -webkit-animation-delay: -1.0s;
            animation-delay: -1.0s;
        }
        @-webkit-keyframes bounce {
            0%, 100% {
                -webkit-transform: scale(0.0)
            }
            50% {
                -webkit-transform: scale(1.0)
            }
        }
        @keyframes bounce {
            0%, 100% {
                transform: scale(0.0);
                -webkit-transform: scale(0.0);
            }
            50% {
                transform: scale(1.0);
                -webkit-transform: scale(1.0);
            }
        }
        #ratio_table {
            overflow-x:auto;
            overflow-y:hidden;
            white-space: nowrap;
        }
        .card {
            overflow-x:auto;
            overflow-y:hidden;
            white-space: nowrap;
            margin-bottom:20px;
        }
        .table-status-item {
            display: block;
            text-align: center;
            white-space: nowrap;
            padding: 0 5px;
            line-height: 25px;
            font-size: 12px;
            font-weight: 600;
            width: 70px;
            float:left;
            margin-left: 2px;
            margin-right: 2px;
        }
        .bullet {
            display: inline-block;
            width: 26px;
            height: 26px;
            border-radius: 50%;
        }
        .success {
            color: #80BA27;
        }
        .danger {
            color: #ff0000;
        }
        .warning {
            color: #f7921e;
        }
        .paused, .info {
            color: #17252e;
        }
        .success-bg {
            color: #fff;
            background-color: #80BA27;
        }
        .danger-bg {
            color: #fff;
            background-color: #ff0000;
        }
        .warning-bg {
            color: #fff;
            background-color: #f7921e;
        }
        .paused-bg, .info-bg, .black-bg {
            color: #fff;
            background-color: #17252e;
        }
        .empty-bg {
            color: #fff;
            background: #9c9b9b;
        }
        .td_new_font_size {
            font-size: 1.2rem;
        }
        .table {
            margin-bottom:0;
        }
        .show_more_information:hover {
            text-decoration:underline;
        }
        #seconds {
            font-size: 1rem;
        }
        </style>
    </head>
    
    <body>
        <main>
            <div class="container" style="margin-top:3%">
                 <h2 class="h1 mb-4">状态监控<span style="font-size:1.7rem">&nbsp;&nbsp;StatusLive</span></h2>

                <!--<div></div>-->
                <div class="spinner">
                    <div class="double-bounce1"></div>
                    <div class="double-bounce2"></div>
                </div>
                <div class="" id="all_card" style="display:none;">
                     <h5 id="texts">报告生成时间：<span id="report_time">2XXX年XX月XX日 XX:XX:XX</span>&nbsp;&nbsp;&nbsp;<span id="seconds">&nbsp;60&nbsp;秒后刷新</span></h5>

                    <br />
                    <div class="card hoverable" id="card_website">
                        <div class="card-body">
                             <h4 class="card-title">实时总览<span style="font-size:1rem">&nbsp;Ontime</span></h4>

                            <table class="table table-borderless table-sm">
                                <tbody>
                                    <tr>
                                        <td><span class="bullet success-bg"></span>

                                        </td>
                                        <td class="td_new_font_size"><span class="success">正常运转</span>&nbsp;<span class="success" id="up_server">?</span>&nbsp;<span class="success">个</span>

                                        </td>
                                        <td><span class="bullet danger-bg"></span>

                                        </td>
                                        <td class="td_new_font_size"><span class="danger">出现问题</span>&nbsp;<span class="danger" id="down_server">?</span>&nbsp;<span class="danger">个</span>

                                        </td>
                                        <td><span class="bullet paused-bg"></span>

                                        </td>
                                        <td class="td_new_font_size"><span class="paused">暂停监控</span>&nbsp;<span class="paused" id="paused_server">?</span>&nbsp;<span class="paused">个</span>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="td_new_font_size" colspan="2" id="pass_1d_td">过去24小时：<span id="pass_1d">???.??</span>%</td>
                                        <td class="td_new_font_size" colspan="2" id="pass_7d_td">过去7天：<span id="pass_7d">???.??</span>%</td>
                                        <td class="td_new_font_size" colspan="2" id="pass_30d_td">过去30天：<span id="pass_30d">???.??</span>%</td>
                                    </tr>
                                    <tr>
                                        <td class="td_new_font_size" colspan="6" id="latest_downtime">最近一次故障：数据获取中...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Ping 监控 -->
                    <div class="card hoverable" id="card_datacenter">
                        <div class="card-body">
                             <h4 class="card-title">数据中心<span style="font-size:1rem">&nbsp;DataCenter</span></h4>

                            <table class="table table-borderless table-sm">
                                <thead>
                                    <tr>
                                        <th scope="col">状态</th>
                                        <th scope="col">可用率</th>
                                        <th scope="col">名称</th>
                                        <th scope="col">详细可用率（过去7天）</th>
                                    </tr>
                                </thead>
                                <tbody id="datacenter_list"></tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /Ping 监控 -->
                    <!-- HTTP 监控 -->
                    <div class="card hoverable">
                        <div class="card-body">
                             <h4 class="card-title">网站<span style="font-size:1rem">&nbsp;WebSite</span></h4>

                            <table class="table table-borderless table-sm">
                                <thead>
                                    <tr>
                                        <th scope="col">状态</th>
                                        <th scope="col">可用率</th>
                                        <th scope="col">名称</th>
                                        <th scope="col">详细可用率（过去7天）</th>
                                    </tr>
                                </thead>
                                <tbody id="website_list"></tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /HTTP 监控 -->
                    <!-- 详情框架 -->
                    <div id="all_modal"></div>
                    <!-- /详情框架 -->
                    <!-- 页底版权 -->
                    <footer id="main-footer" style="text-align:right;">Powered by <a href="http://www.freejishu.com">freejishu</a>&nbsp;&nbsp;数据来源：

                        <a href="https://uptimerobot.com">
                            <img src="./img/uptime-logo.png" alt="Uptime Robot Logo">
                        </a>
                    </footer>
                    <br /><br /><br />
                    <!-- /页底版权 -->
                </div>
            </div>
        </main>
        <script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
        <script type="text/javascript" src="js/popper.min.js"></script>
        <script type="text/javascript" src="js/bootstrap.min.js"></script>
        <script type="text/javascript" src="js/mdb.min.js"></script>
        <script>
        wow = new WOW().init();

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

        var c = 0;
        var t;

        function timedCount() {

            $("#seconds").html('&nbsp;' + (60 - c) + '&nbsp;秒后刷新');
            c = c + 1;

            if (c == 61) {
                load(1);
                c = 0;
                t = setTimeout("timedCount()", 1000);
            } else {
                t = setTimeout("timedCount()", 1000);
            }
        }
        load(0);
        t = setTimeout("timedCount()", 1000);

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
            durationStr = durationStr.replace("mins.", "分钟");
            return durationStr;
        }

        function index_latest_downtime(latestDownTimeStr) {
            //It was recorded (for the monitor 主博客-深圳阿里云) on 2018-07-17 20:19:13 and the downtime lasted for 0 hrs, 1 mins.
            latestDownTimeStr = latestDownTimeStr.replace("It was recorded (for the monitor", "节点");
            latestDownTimeStr = latestDownTimeStr.replace(") on", " 于");
            latestDownTimeStr = latestDownTimeStr.replace("and the downtime lasted for", "发生故障，持续时间");
            latestDownTimeStr = latestDownTimeStr.replace("hrs,", "小时");
            latestDownTimeStr = latestDownTimeStr.replace("mins.", "分钟。");
            latestDownTimeStr = latestDownTimeStr.replace("No downtime recorded.", "还未发生故障。");
            return latestDownTimeStr;
        }

        function load(clear_table) {
            $("#seconds").html("正在刷新，请稍后...");
            $.ajax({
                url: "core.php",
                type: "GET",
                dataType: "jsonp", //指定服务器返回的数据类型
                success: function (data) {
                    var date = new Date(),
                        year = date.getFullYear(),
                        month = date.getMonth() + 1,
                        day = date.getDate(),
                        hour = date.getHours(),
                        minute = date.getMinutes(),
                        second = date.getSeconds(),
                        time_text = year + '年' + month + '月' + day + '日 ' + time_good_look(hour) + ':' + time_good_look(
                            minute) + ':' + time_good_look(second);

                    $("#report_time").html(time_text);
                    $("#up_server").html(data.psp.pspStats.counts.up);
                    $("#down_server").html(data.psp.pspStats.counts.down);
                    $("#paused_server").html(data.psp.pspStats.counts.paused);
                    if (clear_table == 1) {
                        $("#website_list").html("");
                        $("#datacenter_list").html("");

                    }
                    var data_table = "";
                    for (var i = 0; i < data.days.length; i++) {
                        data_table = data_table + '<td scope="col">' + data.days[i] + "</td>";
                    }
                    console.log(data_table);
                    $("#latest_downtime").html("最近一次故障：" + index_latest_downtime(data.psp.latestDownTimeStr));

                    for (var i = 0; i < data.psp.monitorCount; i++) {
                        if (data.psp.monitors[i].typeStr == "http") {
                            var table_key = "website_list",
                                tag_website = 1;
                        } else if (data.psp.monitors[i].typeStr == "ping") {
                            var table_key = "datacenter_list",
                                tag_datacenter = 1;
                        } else {
                            var table_key = "website_list",
                                tag_website = 1;
                        }

                        var last_seven_day_ratio = "",
                            last_seven_day_ratio_html = "";
                        for (var ii = 0; ii < data.psp.monitors[i].customuptimeranges.length; ii++) {
                            last_seven_day_ratio = last_seven_day_ratio + '<span class="table-status-item ' + data.psp
                                .monitors[
                                i].customuptimeranges[ii].label + '-bg">' + data.psp.monitors[i].customuptimeranges[
                                ii]
                                .ratio +
                                '</span>';
                            last_seven_day_ratio_html = last_seven_day_ratio_html +
                                '<td><span class="table-status-item ' + data.psp
                                .monitors[
                                i].customuptimeranges[ii].label + '-bg">' + data.psp.monitors[i].customuptimeranges[
                                ii]
                                .ratio +
                                '</span></td>';
                        }

                        $("#" + table_key).html($("#" + table_key).html() +
                            '<tr><th scope="row"><span class="bullet ' +
                            data.psp.monitors[i].statusLabel + '-bg"></span></th><td class="td_new_font_size ' +
                            data.psp
                            .monitors[
                            i].oneWeekRange.label + '">' + data.psp.monitors[i].oneWeekRange.ratio +
                            '%</td><td class="td_new_font_size show_more_information" data-toggle="modal" data-target="#more_information_' +
                            i + '"><a>' +
                            data.psp.monitors[i].friendly_name +
                            '</a></td><td style="min-width:600px">' + last_seven_day_ratio + '</td></tr>');


                        if (clear_table == 0) {
                            $("#all_modal").html($("#all_modal").html() +
                                '<div class="modal fade" id="more_information_' + i +
                                '" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true"><div class="modal-dialog modal-lg" role="document"><div class="modal-content"><div class="modal-header"><h4 class="modal-title w-100" id="myModalLabel">' +
                                data.psp.monitors[i].friendly_name +
                                '</h4><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button></div><div class="modal-body" id="more_information_body_' +
                                i + '">Loading...</div></div></div></div>');
                        }
                        var event_list = "";
                        for (var ii = 0; ii < data.psp.monitors[i].allLogs.length; ii++) {
                            var if_or_not_start = 0;
                            if (ii == 0) {
                                if_or_not_start = 1;
                            }
                            event_list = "<tr><td>" + event_list_statusStr(data.psp.monitors[i].allLogs[ii].statusStr,
                                if_or_not_start) + "</td><td>" + data.psp.monitors[i].allLogs[ii].dateTimeStr +
                                "</td><td>" + event_list_reasonStr(data.psp.monitors[i].allLogs[ii].reasonStr,
                                if_or_not_start, data.psp.monitors[i].allLogs[ii].statusStr) + "</td><td>" +
                                event_list_durationStr(data.psp.monitors[
                                i].allLogs[ii].durationStr) + "</td></tr>" + event_list;
                        }
                        $('#more_information_body_' + i).html("报告生成时间：" + time_text +
                            '<br /><br /><table class="table table-borderless table-sm" id="ratio_table"><thead><tr>' +
                            data_table +
                            '</tr></thead><tbody><tr>' + last_seven_day_ratio_html +
                            '</tr></tbody></table><table class="table table-borderless table-sm"><thead><tr><th scope="col">事件</th><th scope="col">时间</th><th scope="col">原因</th><th scope="col">持续时间</th></tr></thead><tbody>' +
                            event_list + '</tbody></table>');



                    }

                    if (!tag_website) {
                        $("#card_website").hide();
                    }
                    if (!tag_datacenter) {
                        $("#card_datacenter").hide();
                    }
                    for (var i = 0; i < 3; i++) {
                        if (i == 0) {
                            var time_key = 1,
                                json_key = "l1";
                        } else if (i == 1) {
                            var time_key = 7,
                                json_key = "l7";
                        } else if (i == 2) {
                            var time_key = 30,
                                json_key = "l30";
                        }
                        $("#pass_" + time_key + "d").html("<strong>" + data.psp.pspStats.ratios[json_key].ratio +
                            "</strong>");
                        $("#pass_" + time_key + "d_td").addClass(data.psp.pspStats.ratios[json_key].label);

                    }

                    $(".warning").addClass("animated flash");
                    $(".warning-bg").addClass("animated flash");
                    setTimeout('$(".warning").removeClass("animated flash")', 1000);
                    setTimeout('$(".warning-bg").removeClass("animated flash")', 1000);

                    $(".spinner").hide();
                    $("#all_card").slideDown(700);
                    $("#seconds").html('&nbsp;60&nbsp;秒后刷新');
                }


            });
        }
        </script>
    </body>

</html>