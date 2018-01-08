function loadCat() {
    document.title = "分类";
    $.ajax({
        type: "POST",
        url: "./dxx/cat.php",
        cache: false,
        data: {
            "fuser": $.cookie("cookie_user"),
            "faction": "listcat1"
        },
        dataType: "text",
        success: function(rawData, textStatus) {
            var data = $.parseJSON(rawData);
            var st = new SmartTable();
            st.setName("cat1");
            st.setEdit();
            st.setHeader([document.title]);
            $.each(data.cat, function(i, item) {
                st.addRow(item.id, [item.name]);
            });

            var html = "<div><form id='cat1form'>" +
                "<input type='hidden' id='faction' value='addcat1' />" +
                "<input type='hidden' id='fid' value='0' />" +
                "<label>大类</label><input type='text' id='fname' />" +
                "<input type='submit' /><input type='reset' />" +
                "</form></div>" + st.getTable();
            $("#main").html(html);

            $(".mod").click(function() {
                var $tr = $(this).parent().parent();
                var fid = $tr.attr("value");
                var fname = $tr.children("td").eq(0).text();
                $("#faction").attr("value", "updatecat1");
                $("#fid").attr("value", fid);
                $("#fname").val(fname);
            });

            $(".del").click(function() {
                onCustomDel("./dxx/cat.php", "deletecat1", $(this).parent().parent().attr("value"), loadCat);
            });

            $("#editform").submit(function() {
                var fuser = $.cookie("cookie_user");
                var faction = $("#faction").attr("value");
                var fid = $("#fid").attr("value");
                var fname = $("#fname").val();
                $("#faction").attr("value", "addcat1");
                $.ajax({
                    type: "POST",
                    url: "./dxx/cat.php",
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
                            loadCat();
                            return true;
                        }
                    }
                });

                return false;
            });
        }
    });
}