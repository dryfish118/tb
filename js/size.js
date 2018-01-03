function onOrderSize(row, ftype) {
    var fuser = $.cookie("cookie_user");
    var $tr = $("#edittable tr").eq(row + 1);
    var fid = $tr.attr("value");
    $("#fid").attr("value", fid);
    $.ajax({
        type: "POST",
        url: "./dxx/size.php",
        cache: false,
        data: {
            "fuser": fuser,
            "faction": ftype,
            "fid": fid
        },
        dataType: "text",
        success: function(data, textStatus) {
            if (parseInt(data) == 1) {
                loadSize();
            }
        }
    });
}

function onModSize(row) {
    var $tr = $("#edittable tr").eq(row + 1);
    var fid = $tr.attr("value");
    var fname = $tr.children("td").eq(0).text();
    $("#faction").attr("value", "update");
    $("#fid").attr("value", fid);
    $("#fname").val(fname);
}

function onDelSize(row) {
    if (!confirm("确定要删除吗？")) {
        return;
    }
    var fuser = $.cookie("cookie_user");
    var $tr = $("#edittable tr").eq(row + 1);
    var fid = $tr.attr("value");
    $.ajax({
        type: "POST",
        url: "./dxx/size.php",
        cache: false,
        data: {
            "fuser": fuser,
            "faction": "delete",
            "fid": fid
        },
        dataType: "text",
        success: function(data, textStatus) {
            if (parseInt(data) == 1) {
                loadSize();
            }
        }
    });
}

function onSize() {
    var fuser = $.cookie("cookie_user");
    var faction = $("#faction").attr("value");
    var fid = $("#fid").attr("value");
    var fname = $("#fname").val();
    $("#faction").attr("value", "add");
    $.ajax({
        type: "POST",
        url: "./dxx/size.php",
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
                loadSize();
                return true;
            }
        }
    });

    return false;
}

function loadSize() {
    document.title = "尺寸";
    $.ajax({
        type: "POST",
        url: "./dxx/size.php",
        cach: false,
        data: {
            "fuser": $.cookie("cookie_user"),
            "faction": "list"
        },
        dataType: "text",
        success: function(rawData, textStatus) {
            var data = $.parseJSON(rawData);
            var size = data.size;

            var st = new SmartTable();
            st.setOrder("onOrderSize");
            st.setModify("onModSize");
            st.setDelete("onDelSize");
            st.setHeader([document.title]);
            $.each(size, function(i, item) {
                st.addRow(item.id, [item.name]);
            });

            var html = "<form id='editform' onsubmit='return onSize();'>" +
                "<input type='hidden' id='faction' value='add' />" +
                "<input type='hidden' id='fid' value='0' />" +
                "<label>" + document.title + "</label><input type='text' id='fname' />" +
                "<input type='submit' /><input type='reset' />" +
                "</form>" +
                "<div>" + st.getTable() + "</div>";
            $('#main').html(html);
        }
    });
}