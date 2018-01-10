var order_name = 0;

function loadUser() {
    document.title = "人员";
    $.ajax({
        type: "POST",
        url: "./dxx/user.php",
        cache: false,
        data: {
            "fuser": $.cookie("cookie_user"),
            "faction": "list",
            "forder": order_name
        },
        dataType: "text",
        success: function(rawData, textStatus) {
            var data = $.parseJSON(rawData);
            var st = new SmartTable();
            st.setEdit();
            st.setHeader([document.title]);
            st.setOrder([order_name]);
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
                order_name = (order_name === 0) ? 1 : 0;
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
                var fuser = $.cookie("cookie_user");
                var faction = $("#faction").attr("value");
                var fid = $("#fid").attr("value");
                var fname = $("#fname").val();
                $("#faction").attr("value", "add");
                $.ajax({
                    type: "POST",
                    url: "./dxx/user.php",
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
                            loadUser();
                        }
                    }
                });

                return false;
            });
        }
    });
}