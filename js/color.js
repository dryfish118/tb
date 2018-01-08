function loadColor() {
    document.title = "颜色";
    $.ajax({
        type: "POST",
        url: "./dxx/color.php",
        cache: false,
        data: {
            "fuser": $.cookie("cookie_user"),
            "faction": "list"
        },
        dataType: "text",
        success: function(rawData, textStatus) {
            var data = $.parseJSON(rawData);
            var st = new SmartTable();
            st.setOrder();
            st.setEdit();
            st.setHeader([document.title]);
            $.each(data.color, function(i, item) {
                st.addRow(item.id, [item.name]);
            });

            var html = "<form id='editform'>" +
                "<input type='hidden' id='faction' value='add' />" +
                "<input type='hidden' id='fid' value='0' />" +
                "<label>" + document.title + "</label><input type='text' id='fname' />" +
                "<input type='submit' /><input type='reset' />" +
                "</form>" +
                "<div>" + st.getTable() + "</div>";
            $("#main").html(html);

            $(".order").click(function() {
                var fuser = $.cookie("cookie_user");
                var faction = $(this).attr("value");
                var $tr = $(this).parent().parent();
                var fid = $tr.attr("value");
                $.ajax({
                    type: "POST",
                    url: "./dxx/color.php",
                    cache: false,
                    data: {
                        "fuser": fuser,
                        "faction": faction,
                        "fid": fid
                    },
                    dataType: "text",
                    success: function(data, textStatus) {
                        if (parseInt(data) == 1) {
                            loadColor();
                        }
                    }
                });
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
                onDel("./dxx/color.php", $(this).parent().parent().attr("value"), loadColor);
            });

            $("#editform").submit(function() {
                var fuser = $.cookie("cookie_user");
                var faction = $("#faction").attr("value");
                var fid = $("#fid").attr("value");
                var fname = $("#fname").val();
                $("#faction").attr("value", "add");
                $.ajax({
                    type: "POST",
                    url: "./dxx/color.php",
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
                            loadColor();
                            return true;
                        }
                    }
                });

                return false;
            });
        }
    });
}