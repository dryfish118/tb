var pageCurrent = 1;
var pageCount = 10;
var orderType = 0;
var orderDir = [0, 0, 0, 0, 0, 0, 0];

function loadClient() {
    document.title = "客户";
    var flogin = $.cookie("cookie_login");
    $.ajax({
        type: "POST",
        url: "./dxx/client.php",
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
            st.setHeader(["编号", document.title, "淘宝名", "手机", "座机", "地址", "邮编"]);
            st.setOrder(orderDir);
            $.each(data.client, function(i, item) {
                st.addRow(item.id, [item.id, item.name, item.taobao, item.tel, item.tel2, item.addr, item.code]);
            });

            var html = "<div><form id='editform'" +
                "<input type='hidden' id='faction' value='add' />" +
                "<input type='hidden' id='fid' value='0' />" +
                "<div><label>" + document.title + "</label><input type='text' id='fname' /></div>" +
                "<div><label>淘宝名</label><input type='text' id='ftaobao' /></div>" +
                "<div><label>手机</label><input type='text' id='ftel' /></div>" +
                "<div><label>座机</label><input type='text' id='ftel2' /></div>" +
                "<div><label>地址</label><input type='text' id='faddr' /></div>" +
                "<div><label>邮编</label><input type='text' id='fcode' /></div>" +
                "<div><input type='submit' /><input type='reset' /></div>" +
                "</form></div>" + st.getTable();
            $("#main").html(html);

            $(".order").click(function() {
                orderType = $(this).parent().parent().find("th").index($(this).parent()[0]);
                orderDir[orderType] = (orderDir[orderType] === 0 ? 1 : 0);
                loadClient();
            });

            $(".page").click(function() {
                pageCurrent = onPage($(this).text(), data.pages);
                loadClient();
            });

            $("#pagenum").bind("keypress", function(event) {
                if (event.keyCode == "13") {
                    pageCurrent = onPage($(this).val(), data.pages);
                    loadClient();
                }
            });

            $(".mod").click(function() {
                var $tr = $(this).parent().parent();
                var fid = $tr.attr("value");
                var $td = $tr.children("td");
                var fname = $td.eq(1).text();
                var ftaobao = $td.eq(2).text();
                var ftel = $td.eq(3).text();
                var ftel2 = $td.eq(4).text();
                var faddr = $td.eq(5).text();
                var fcode = $td.eq(6).text();
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
                onDel("./dxx/client.php", loadClient);
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
                    url: "./dxx/client.php",
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
                            loadClient();
                        }
                    }
                });

                return false;
            });
        }
    });
}