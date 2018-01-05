function onModBrand(row) {
    var $tr = $("#edittable tr").eq(row + 1);
    var fid = $tr.attr("value");
    var fname = $tr.children("td").eq(0).text();
    $("#faction").attr("value", "update");
    $("#fid").attr("value", fid);
    $("#fname").val(fname);
}

function onDelBrand(row) {
    if (!confirm("确定要删除吗？")) {
        return;
    }
    var fuser = $.cookie("cookie_user");
    var $tr = $("#edittable tr").eq(row + 1);
    var fid = $tr.attr("value");
    $.ajax({
        type: "POST",
        url: "./dxx/brand.php",
        cache: false,
        data: {
            "fuser": fuser,
            "faction": "delete",
            "fid": fid
        },
        dataType: "text",
        success: function(data, textStatus) {
            if (parseInt(data) == 1) {
                loadBrand();
            }
        }
    });
}

function onBrand() {
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
                return true;
            }
        }
    });

    return false;
}

function loadBrand() {
    document.title = "品牌";
    $.ajax({
        type: "POST",
        url: "./dxx/brand.php",
        cache: false,
        data: {
            "fuser": $.cookie("cookie_user"),
            "faction": "list"
        },
        dataType: "text",
        success: function(rawData, textStatus) {
            var data = $.parseJSON(rawData);
            var brand = data.brand;

            var st = new SmartTable();
            st.setModify("onModBrand");
            st.setDelete("onDelBrand");
            st.setHeader([document.title]);
            st.setPage(15, 12);
            $.each(brand, function(i, item) {
                st.addRow(item.id, [item.name]);
            });

            var html = "<form id='editform' onsubmit='return onBrand();'>" +
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