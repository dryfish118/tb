var pageCurrent = 1;
var pageCount = 10;
var order_name = 0;

function loadBrand() {
    document.title = "品牌";
    $.ajax({
        type: "POST",
        url: "./dxx/brand.php",
        cache: false,
        data: {
            "fuser": $.cookie("cookie_user"),
            "faction": "list",
            "fcurrent": pageCurrent,
            "fcount": pageCount,
            "forder": order_name
        },
        dataType: "text",
        success: function(rawData, textStatus) {
            var data = $.parseJSON(rawData);
            var st = new SmartTable();
            st.setPage(data.pages, data.current);
            st.setEdit();
            st.setHeader([document.title]);
            st.setOrder([order_name]);
            $.each(data.brand, function(i, item) {
                st.addRow(item.id, [item.name]);
            });

            var html = "<div><form id='editform'>" +
                "<input type='hidden' id='faction' value='add' />" +
                "<input type='hidden' id='fid' value='0' />" +
                "<label>" + document.title + "</label><input type='text' id='fname' />" +
                "<input type='submit' /><input type='reset' />" +
                "</form></div>" + st.getTable();
            $("#main").html(html);

            $(".order").click(function() {
                order_name = (order_name === 0) ? 1 : 0;
                loadBrand();
            });

            $(".page").click(function() {
                pageCurrent = onPage($(this).text(), data.pages);
                loadBrand();
            });

            $("#pagenum").bind("keypress", function(event) {
                if (event.keyCode == "13") {
                    pageCurrent = onPage($(this).val(), data.pages);
                    loadBrand();
                }
            });

            $(".mod").click(function() {
                var $tr = $(this).parent().parent();
                var fid = $tr.attr("value");
                var fname = $tr.children("td").eq(0).text();
                $("#faction").attr("value", "update");
                $("#fid").attr("value", fid);
                $("#fname").val(fname);
            });

            $(".del").click(function() {
                onDel("./dxx/brand.php", $(this).parent().parent().attr("value"), loadBrand);
            });

            $("#editform").submit(function() {
                var fuser = $.cookie("cookie_user");
                var faction = $("#faction").attr("value");
                var fid = $("#fid").attr("value");
                var fname = $("#fname").val();
                $("#faction").attr("value", "add");
                $.ajax({
                    type: "POST",
                    url: "./dxx/brand.php",
                    cache: false,
                    data: {
                        "fuser": fuser,
                        "faction": faction,
                        "fid": fid,
                        "fname": fname
                    },
                    dataType: "text",
                    success: function(data, textStatus) {
                        if (parseInt(data) == 1) {
                            loadBrand();
                        }
                    }
                });

                return false;
            });
        }
    });
}