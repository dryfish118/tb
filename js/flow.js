var pageCurrent = 1;
var pageCount = 10;
var orderType = 2;
var orderDir = [0, 0, 0, 0, -1];

function loadFlow() {
    document.title = "流水";
    var flogin = $.cookie("cookie_login");
    $.ajax({
        type: "POST",
        url: "./dxx/flow.php",
        cache: false,
        data: {
            "flogin": flogin,
            "faction": "list",
            "fcurrent": pageCurrent,
            "fcount": pageCount,
            "fordertype": orderType,
            "forderdir": orderDir[orderType],
        },
        dataType: "text",
        success: function(rawData, textStatus) {
            var data = $.parseJSON(rawData);
            var st = new SmartTable();
            st.setPage(data.pages, data.current);
            st.setEdit();
            st.setHeader(["条目", "人员", "时间", "金额", "说明"]);
            st.setOrder(orderDir);
            $.each(data.flow, function(i, item) {
                st.addRow(item.id, [item.issue, item.user, item.time, item.money, item.remark]);
            });

            var html = "<div><form id='editform'" +
                "<input type='hidden' id='faction' value='add' />" +
                "<input type='hidden' id='fid' value='0' />" +
                "<div><label>条目</label><input type='text' id='fissue' /></div>" +
                "<div><label>人员</label><input type='text' id='fuser' /></div>" +
                "<div><label>手机</label><input type='text' id='ftel' /></div>" +
                "<div><label>时间</label><input type='text' id='ftime' /></div>" +
                "<div><label>金额</label><input type='text' id='fmoney' /></div>" +
                "<div><label>说明</label><input type='text' id='fremark' /></div>" +
                "<div><input type='submit' /><input type='reset' /></div>" +
                "</form></div>" + st.getTable();
            $("#main").html(html);

            $(".order").click(function() {
                orderType = $(this).parent().parent().find("th").index($(this).parent()[0]);
                orderDir[orderType] = (orderDir[orderType] === 0 ? 1 : 0);
                loadFlow();
            });

            $(".page").click(function() {
                pageCurrent = onPage($(this).text(), data.pages);
                loadFlow();
            });

            $("#pagenum").bind("keypress", function(event) {
                if (event.keyCode == "13") {
                    pageCurrent = onPage($(this).val(), data.pages);
                    loadFlow();
                }
            });

            $(".mod").click(function() {
                var $tr = $(this).parent().parent();
                var fid = $tr.attr("value");
                var $td = $tr.children("td");
                var fname = $td.eq(0).text();
                var ftaobao = $td.eq(1).text();
                var ftel = $td.eq(2).text();
                var ftel2 = $td.eq(3).text();
                var faddr = $td.eq(4).text();
                var fcode = $td.eq(5).text();
                $("#faction").attr("value", "update");
                $("#fid").attr("value", fid);
                $("#fname").val(fname);
                $("#ftaobao").val(ftaobao);
                $("#ftel").val(ftel);
                $("#ftel2").val(ftel2);
                $("#faddr").val(faddr);
                $("#fcode").val(fcode);
            });

            $(".del").click(function() {
                onDel("./dxx/flow.php", loadFlow);
            });

            $("#editform").submit(function() {
                var faction = $("#faction").attr("value");
                var fid = $("#fid").attr("value");
                var fname = $("#fname").val();
                var ftaobao = $("#ftaobao").val();
                var ftel = $("#ftel").val();
                var ftel2 = $("#ftel2").val();
                var faddr = $("#faddr").val();
                var fcode = $("#fcode").val();
                $("#faction").attr("value", "add");
                $.ajax({
                    type: "POST",
                    url: "./dxx/flow.php",
                    cache: false,
                    data: {
                        "flogin": flogin,
                        "faction": faction,
                        "fid": fid,
                        "fname": fname,
                        "ftaobao": ftaobao,
                        "ftel": ftel,
                        "ftel2": ftel2,
                        "faddr": faddr,
                        "fcode": fcode
                    },
                    dataType: "text",
                    success: function(data, textStatus) {
                        if (parseInt(data) == 1) {
                            loadFlow();
                        }
                    }
                });

                return false;
            });
        }
    });
}