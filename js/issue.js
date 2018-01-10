var order_type = 0;
var order_name = 0;
var order_out = 0;

function loadIssue() {
    document.title = "条目";
    $.ajax({
        type: "POST",
        url: "./dxx/issue.php",
        cache: false,
        data: {
            "fuser": $.cookie("cookie_user"),
            "faction": "list",
            "fordertype": order_type,
            "forder": (order_type === 0 ? order_name : order_out)
        },
        dataType: "text",
        success: function(rawData, textStatus) {
            var data = $.parseJSON(rawData);
            var st = new SmartTable();
            st.setEdit();
            st.setHeader([document.title, "收支"]);
            st.setOrder([order_name, order_out]);
            $.each(data.issue, function(i, item) {
                st.addRow(item.id, [item.name, item.out]);
            });

            var html = "<div><form id='editform'>" +
                "<input type='hidden' id='faction' value='add' />" +
                "<input type='hidden' id='fid' value='0' />" +
                "<label>" + document.title + "</label><input type='text' id='fname' />" +
                "<label>支出：</label><input type='checkbox' id='fout' CHECKED />" +
                "<input type='submit' /><input type='reset' />" +
                "</form></div>" + st.getTable();
            $("#main").html(html);

            $(".order").click(function() {
                var col = $(this).parent().parent().find("th").index($(this).parent()[0]);
                if (col === 0) {
                    order_name = (order_name === 0) ? 1 : 0;
                    order_type = 0;
                } else {
                    order_out = (order_out === 0) ? 1 : 0;
                    order_type = 1;
                }
                loadIssue();
            });

            $(".mod").click(function() {
                var $tr = $(this).parent().parent();
                var fid = $tr.attr("value");
                var fname = $tr.children("td").eq(0).text();
                var fout = $tr.children("td").eq(1).text();
                $("#faction").attr("value", "update");
                $("#fid").attr("value", fid);
                $("#fname").val(fname);
                $("#fout").prop("checked", fout == "1" ? true : false);
            });

            $(".del").click(function() {
                onDel("./dxx/issue.php", $(this).parent().parent().attr("value"), loadIssue);
            });

            $("#editform").submit(function() {
                var fuser = $.cookie("cookie_user");
                var faction = $("#faction").attr("value");
                var fid = $("#fid").attr("value");
                var fname = $("#fname").val();
                var fout = $("#fout").prop("checked") ? 1 : 0;
                $("#faction").attr("value", "add");
                $.ajax({
                    type: "POST",
                    url: "./dxx/issue.php",
                    cache: false,
                    data: {
                        "fuser": fuser,
                        "faction": faction,
                        "fid": fid,
                        "fname": fname,
                        "fout": fout
                    },
                    dataType: "text",
                    success: function(data, textStatus) {
                        if (parseInt(data) == 1) {
                            loadIssue();
                        }
                    }
                });

                return false;
            });
        }
    });
}