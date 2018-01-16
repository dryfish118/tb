var pageCurrent = 1;
var pageCount = 10;
var orderType = 0;
var orderDir = [0, 0, 0, 0, 0, 0, -1];

function loadGoods() {
    document.title = "商品";
    var flogin = $.cookie("cookie_login");
    $.ajax({
        type: "POST",
        url: "./dxx/goods.php",
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
            st.setHeader(["编号", "品牌", "大类", "小类", "型号", "单价", "说明"]);
            st.setOrder(orderDir);
            $.each(data.goods, function(i, item) {
                st.addRow(item.id, [item.id, item.brand, item.cat1, item.cat2, item.type, item.price, item.remark]);
            });

            var html = "<div><form id='editform'>" +
                "<input type='hidden' id='faction' value='add' />" +
                "<input type='hidden' id='fid' value='0' />" +
                "<div><label>品牌</label><input type='text' id='fbrand' /></div>" +
                "<div><label>大类</label><input type='text' id='fcat1' /></div>" +
                "<div><label>小类</label><input type='text' id='fcat2' /></div>" +
                "<div><label>型号</label><input type='text' id='ftype' /></div>" +
                "<div><label>单价</label><input type='text' id='fprice' /></div>" +
                "<div><label>说明</label><input type='text' id='fremark' /></div>" +
                "<div><input type='submit' /><input type='reset' /></div>" +
                "</form></div>" + st.getTable();
            $("#main").html(html);

            $(".order").click(function() {
                orderType = $(this).parent().parent().find("th").index($(this).parent()[0]);
                orderDir[orderType] = (orderDir[orderType] === 0 ? 1 : 0);
                loadGoods();
            });

            $(".page").click(function() {
                pageCurrent = onPage($(this).text(), data.pages);
                loadGoods();
            });

            $("#pagenum").bind("keypress", function(event) {
                if (event.keyCode == "13") {
                    pageCurrent = onPage($(this).val(), data.pages);
                    loadGoods();
                }
            });

            $(".mod").click(function() {
                var $tr = $(this).parent().parent();
                var fid = $tr.attr("value");
                var fbrand = $td.eq(1).text();
                var fcat1 = $td.eq(2).text();
                var fcat2 = $td.eq(3).text();
                var ftype = $td.eq(4).text();
                var fprice = $td.eq(5).text();
                var fremark = $td.eq(6).text();
                $("#faction").attr("value", "update");
                $("#fid").attr("value", fid);
                $("#fbrand").val(fbrand);
                $("#fcat1").val(fcat1);
                $("#fcat2").val(fcat2);
                $("#ftype").val(ftype);
                $("#fprice").val(fprice);
                $("#fremark").val(fremark);
            });

            $(".del").click(function() {
                onDel("./dxx/goods.php", $(this).parent().parent().attr("value"), loadGoods);
            });

            $("#editform").submit(function() {
                var faction = $("#faction").attr("value");
                var fid = $("#fid").attr("value");
                var fbrand = $("#fbrand").val();
                var fcat1 = $("#fcat1").val();
                var fcat2 = $("#fcat2").val();
                var ftype = $("#ftype").val();
                var fprice = $("#fprice").val();
                var fremark = $("#fremark").val();
                $("#faction").attr("value", "add");
                $.ajax({
                    type: "POST",
                    url: "./dxx/goods.php",
                    cache: false,
                    data: {
                        "flogin": flogin,
                        "faction": faction,
                        "fid": fid,
                        "fbrand": fbrand,
                        "fcat1": fcat1,
                        "fcat2": fcat2,
                        "ftype": ftype,
                        "fprice": fprice,
                        "fremark": fremark
                    },
                    dataType: "text",
                    success: function(data, textStatus) {
                        if (parseInt(data) == 1) {
                            loadGoods();
                        }
                    }
                });

                return false;
            });
        }
    });
}