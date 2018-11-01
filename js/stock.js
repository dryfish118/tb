var pageCurrent = 1;
var pageCount = 10;
var orderType = 7;
var orderDir = [0, 0, 0, 0, 0, 0, 0, 0, 0, -1, -1, -1];

function loadStock() {
    document.title = "进货";
    var flogin = $.cookie("cookie_login");
    $.ajax({
        type: "POST",
        url: "./dxx/stock.php",
        cache: false,
        data: {
            "flogin": flogin,
            "faction": "list",
            "fcurrent": pageCurrent,
            "fcount": pageCount,
            "fordertype": orderType,
            "forderdir": orderDir[orderType]
        },
        dataType: "text",
        success: function(rawData, textStatus) {
            var data = $.parseJSON(rawData);
            var st = new SmartTable();
            st.setPage(data.pages, data.current);
            st.setEdit();
            st.setHeader(["编号", "品牌", "大类", "小类", "型号", "颜色", "尺寸", "时间", "人员", "数量", "链接", "说明"]);
            st.setOrder(orderDir);
            $.each(data.stock, function(i, item) {
                st.addRow(stock.id, [stock.id, stock.brand, stock.cat1, stock.cat2, stock.type, stock.color, stock.size, stock.time, stock.user, stock.amount, stock.link, stock.remark]);
            });

            var html = "<div><form id='editform'>" +
                "<input type='hidden' id='faction' value='add' />" +
                "<input type='hidden' id='fid' value='0' />" +
                "<div><label>品牌</label><input type='text' id='fbrand' /></div>" +
                "<div><label>大类</label><input type='text' id='fcat1' /></div>" +
                "<div><label>小类</label><input type='text' id='fcat2' /></div>" +
                "<div><label>型号</label><input type='text' id='ftype' /></div>" +
                "<div><label>颜色</label><input type='text' id='fcolor' /></div>" +
                "<div><label>尺寸</label><input type='text' id='fsize' /></div>" +
                "<div><label>时间</label><input type='text' id='ftime' /></div>" +
                "<div><label>人员</label><input type='text' id='fuser' /></div>" +
                "<div><label>数量</label><input type='text' id='famount' /></div>" +
                "<div><label>链接</label><input type='text' id='flink' /></div>" +
                "<div><label>说明</label><input type='text' id='fremark' /></div>" +
                "<div><input type='submit' /><input type='reset' /></div>" +
                "</form></div>" + st.getTable();
            $("#main").html(html);

            $(".order").click(function() {
                orderType = $(this).parent().parent().find("th").index($(this).parent()[0]);
                orderDir[orderType] = (orderDir[orderType] === 0 ? 1 : 0);
                loadStock();
            });

            $(".page").click(function() {
                pageCurrent = onPage($(this).text(), data.pages);
                loadStock();
            });

            $("#pagenum").bind("keypress", function(event) {
                if (event.keyCode == "13") {
                    pageCurrent = onPage($(this).val(), data.pages);
                    loadStock();
                }
            });

            $(".mod").click(function() {
                var $tr = $(this).parent().parent();
                var fid = $tr.attr("value");
                var fbrand = $td.eq(1).text();
                var fcat1 = $td.eq(2).text();
                var fcat2 = $td.eq(3).text();
                var ftype = $td.eq(4).text();
                var fcolor = $td.eq(5).text();
                var fsize = $td.eq(6).text();
                var ftime = $td.eq(7).text();
                var fuser = $td.eq(8).text();
                var famount = $td.eq(9).text();
                var flink = $td.eq(10).text();
                var fremark = $td.eq(11).text();
                $("#faction").attr("value", "update");
                $("#fid").attr("value", fid);
                $("#fbrand").val(fbrand);
                $("#fcat1").val(fcat1);
                $("#fcat2").val(fcat2);
                $("#ftype").val(ftype);
                $("#fcolor").val(fcolor);
                $("#fsize").val(fsize);
                $("#ftime").val(ftime);
                $("#fuser").val(fuser);
                $("#famount").val(famount);
                $("#flink").val(flink);
                $("#fremark").val(fremark);
            });

            $(".del").click(function() {
                onDel("./dxx/stock.php", $(this).parent().parent().attr("value"), loadStock);
            });

            $("#editform").submit(function() {
                var faction = $("#faction").attr("value");
                var fid = $("#fid").attr("value");
                var fbrand = $("#fbrand").val();
                var fcat1 = $("#fcat1").val();
                var fcat2 = $("#fcat2").val();
                var ftype = $("#ftype").val();
                var fcolor = $("#fcolor").val();
                var fsize = $("#fsize").val();
                var ftime = $("#ftime").val();
                var fuser = $("#fuser").val();
                var famount = $("#famount").val();
                var flink = $("#flink").val();
                var fremark = $("#fremark").val();
                $("#faction").attr("value", "add");
                $.ajax({
                    type: "POST",
                    url: "./dxx/stock.php",
                    cache: false,
                    data: {
                        "flogin": flogin,
                        "faction": faction,
                        "fid": fid,
                        "fbrand": fbrand,
                        "fcat1": fcat1,
                        "fcat2": fcat2,
                        "ftype": ftype,
                        "fcolor": fcolor,
                        "fsize": fsize,
                        "ftime": ftime,
                        "fuser": fuser,
                        "famount": famount,
                        "flink": flink,
                        "fremark": fremark
                    },
                    dataType: "text",
                    success: function(data, textStatus) {
                        if (parseInt(data) == 1) {
                            loadStock();
                        }
                    }
                });

                return false;
            });
        }
    });
}