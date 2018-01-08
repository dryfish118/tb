function onUser() {
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
                return true;
            }
        }
    });

    return false;
}

function loadUser() {
    document.title = "人员";
    $.ajax({
        type: "POST",
        url: "./dxx/user.php",
        cache: false,
        data: {
            "fuser": $.cookie("cookie_user"),
            "faction": "list"
        },
        dataType: "text",
        success: function(rawData, textStatus) {
            var data = $.parseJSON(rawData);
            var st = new SmartTable();
            st.setEdit();
            st.setHeader([document.title]);
            $.each(data.user, function(i, item) {
                st.addRow(item.id, [item.name]);
            });

            var html = "<form id='editform' onsubmit='return onUser();'>" +
                "<input type='hidden' id='faction' value='add' />" +
                "<input type='hidden' id='fid' value='0' />" +
                "<label>" + document.title + "</label><input type='text' id='fname' />" +
                "<input type='submit' /><input type='reset' />" +
                "</form>" +
                "<div>" + st.getTable() + "</div>";
            $("#main").html(html);

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
        }
    });
}