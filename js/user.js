var orderType = 0;
var orderDir = [0];

function loadUser() {
    document.title = "人员";
    var flogin = $.cookie("cookie_login");
    $.ajax({
        type: "POST",
        url: "./dxx/user.php",
        cache: false,
        data: {
            "flogin": flogin,
            "faction": "list",
            "fordertype": orderType,
            "forderdir": orderDir[orderType]
        },
        dataType: "text",
        success: function(rawData, textStatus) {
            var data = $.parseJSON(rawData);
            var st = new SmartTable();
            st.setEdit();
            st.setHeader([document.title]);
            st.setOrder(orderDir);
            $.each(data.user, function(i, item) {
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
                orderType = $(this).parent().parent().find("th").index($(this).parent()[0]);
                orderDir[orderType] = (orderDir[orderType] === 0 ? 1 : 0);
                loadUser();
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
                onDel("./dxx/user.php", $(this).parent().parent().attr("value"), loadUser);
            });

            $("#editform").submit(function() {
                var faction = $("#faction").attr("value");
                var fid = $("#fid").attr("value");
                var fname = $("#fname").val();
                $("#faction").attr("value", "add");
                $.ajax({
                    type: "POST",
                    url: "./dxx/user.php",
                    cache: false,
                    data: {
                        "flogin": flogin,
                        "faction": faction,
                        "fid": fid,
                        "fname": fname
                    },
                    dataType: "text",
                    success: function(data, textStatus) {
                        if (parseInt(data) == 1) {
                            loadUser();
                        }
                    }
                });

                return false;
            });
        }
    });
}