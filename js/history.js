function loadHistory() {
    document.title = "历史";
    $.ajax({
        type: "POST",
        url: "./dxx/history.php",
        cache: false,
        data: {
            "fuser": $.cookie("cookie_user"),
            "faction": "list"
        },
        dataType: "text",
        success: function(rawData, textStatus) {
            var data = $.parseJSON(rawData);
            var history = data.history;

            var st = new SmartTable();
            st.setHeader(["人员", "时间", "内容"]);
            $.each(history, function(i, item) {
                st.addRow(item.id, [item.user, item.time, item.contents]);
            });

            var html = "<div id='edittable'>" + st.getTable() + "</div>";
            $('#main').html(html);
        }
    });
}