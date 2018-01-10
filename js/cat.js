var cat1_id = 0;

function loadCat() {
    document.title = "分类";
    $.ajax({
        type: "POST",
        url: "./dxx/cat.php",
        cache: false,
        data: {
            "fuser": $.cookie("cookie_user"),
            "faction": "listcat1",
            "fcat1": cat1_id
        },
        dataType: "text",
        success: function(rawData, textStatus) {
            var data = $.parseJSON(rawData);
            cat1_id = data.cat1_id;

            var st1 = new SmartTable();
            st1.setName("cat1");
            st1.setEdit();
            st1.setHeader([document.title]);
            $.each(data.cat1, function(i, item) {
                st1.addRow(item.id, [item.name]);
            });

            var html = "<div class='left'><div><form id='cat1form'>" +
                "<input type='hidden' id='faction1' value='addcat1' />" +
                "<input type='hidden' id='fid1' value='0' />" +
                "<label>大类</label><input type='text' id='fname1' />" +
                "<input type='submit' /><input type='reset' />" +
                "</form></div>" + st1.getTable() + "</div>";

            var st2 = new SmartTable();
            st2.setName("cat2");
            st2.setEdit();
            st2.setHeader([document.title]);
            $.each(data.cat2, function(i, item) {
                st2.addRow(item.id, [item.name]);
            });

            html += "<div class='middle'>&nbsp;</div><div class='right'><div><form id='cat2form'>" +
                "<input type='hidden' id='faction2' value='addcat2' />" +
                "<input type='hidden' id='fid2' value='0' />" +
                "<label>小类</label><input type='text' id='fname2' />" +
                "<input type='submit' /><input type='reset' />" +
                "</form></div>" + st2.getTable() + "</div>";

            $("#main").html(html);

            $("#cat1 .mod").click(function() {
                var $tr = $(this).parent().parent();
                var fid = $tr.attr("value");
                var fname = $tr.children("td").eq(0).text();
                $("#faction1").attr("value", "updatecat1");
                $("#fid1").attr("value", fid);
                $("#fname1").val(fname);
            });

            $("#cat1 .del").click(function() {
                onCustomDel("./dxx/cat.php", "deletecat1", $(this).parent().parent().attr("value"), loadCat);
            });

            $("#cat1form").submit(function() {
                var fuser = $.cookie("cookie_user");
                var faction = $("#faction1").attr("value");
                var fid = $("#fid1").attr("value");
                var fname = $("#fname1").val();
                $("#faction1").attr("value", "addcat1");
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
                        }
                    }
                });

                return false;
            });

            $("#cat1 td:nth-child(1)").addClass("clickable");
            $("#cat1 td:nth-child(1)").click(function() {
                var $tr = $(this).parent();
                cat1_id = $tr.attr("value");
                loadCat();
            });

            $("#cat2 .mod").click(function() {
                var $tr = $(this).parent().parent();
                var fid = $tr.attr("value");
                var fname = $tr.children("td").eq(0).text();
                $("#faction2").attr("value", "updatecat2");
                $("#fid2").attr("value", fid);
                $("#fname2").val(fname);
            });

            $("#cat2 .del").click(function() {
                onCustomDel("./dxx/cat.php", "deletecat2", $(this).parent().parent().attr("value"), loadCat);
            });

            $("#cat2form").submit(function() {
                var fuser = $.cookie("cookie_user");
                var faction = $("#faction2").attr("value");
                var fid = $("#fid2").attr("value");
                var fname = $("#fname2").val();
                $("#faction2").attr("value", "addcat2");
                $.ajax({
                    type: "POST",
                    url: "./dxx/cat.php",
                    cache: false,
                    data: {
                        "fuser": fuser,
                        "faction": faction,
                        "fcat1": cat1_id,
                        "fid": fid,
                        "fname": fname
                    },
                    dataType: "text",
                    success: function(data, textStatus) {
                        if (parseInt(data) == 1) {
                            loadCat();
                        }
                    }
                });

                return false;
            });
        }
    });
}