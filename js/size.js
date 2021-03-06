function loadSize() {
    document.title = "尺寸";
    var flogin = $.cookie("cookie_login");
    $.ajax({
        type: "POST",
        url: "./dxx/size.php",
        cache: false,
        data: {
            "flogin": flogin,
            "faction": "list"
        },
        dataType: "text",
        success: function(rawData, textStatus) {
            var data = $.parseJSON(rawData);
            var st = new SmartTable();
            st.setArray();
            st.setEdit();
            st.setHeader([document.title]);
            $.each(data.size, function(i, item) {
                st.addRow(item.id, [item.name]);
            });

            var html = "<div><form id='editform'>" +
                "<input type='hidden' id='faction' value='add' />" +
                "<input type='hidden' id='fid' value='0' />" +
                "<label>" + document.title + "</label><input type='text' id='fname' />" +
                "<input type='submit' /><input type='reset' />" +
                "</form></div>" + st.getTable();
            $("#main").html(html);

            $(".array").click(function() {
                var faction = $(this).attr("value");
                var $tr = $(this).parent().parent();
                var fid = $tr.attr("value");
                $.ajax({
                    type: "POST",
                    url: "./dxx/size.php",
                    cache: false,
                    data: {
                        "flogin": flogin,
                        "faction": faction,
                        "fid": fid
                    },
                    dataType: "text",
                    success: function(data, textStatus) {
                        if (parseInt(data) == 1) {
                            loadSize();
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
                onDel("./dxx/size.php", $(this).parent().parent().attr("value"), loadSize);
            });

            $("#editform").submit(function() {
                var faction = $("#faction").attr("value");
                var fid = $("#fid").attr("value");
                var fname = $("#fname").val();
                $("#faction").attr("value", "add");
                $.ajax({
                    type: "POST",
                    url: "./dxx/size.php",
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
                            loadSize();
                        }
                    }
                });

                return false;
            });
        }
    });
}