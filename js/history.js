var pageCurrent = 1;
var pageCount = 10;

function loadHistory() {
    document.title = "历史";
    $.ajax({
        type: "POST",
        url: "./dxx/history.php",
        cache: false,
        data: {
            "flogin": $.cookie("cookie_login"),
            "faction": "list",
            "fcurrent": pageCurrent,
            "fcount": pageCount,
        },
        dataType: "text",
        success: function(rawData, textStatus) {
            var data = $.parseJSON(rawData);
            var st = new SmartTable();
            st.setPage(data.pages, data.current);
            st.setHeader(["人员", "时间", "内容"]);
            $.each(data.history, function(i, item) {
                st.addRow(item.id, [item.user, item.time, item.contents]);
            });

            var html = st.getTable();
            $("#main").html(html);

            $(".page").click(function() {
                pageCurrent = onPage($(this).text(), data.pages);
                loadHistory();
            });

            $("#pagenum").bind("keypress", function(event) {
                if (event.keyCode == "13") {
                    pageCurrent = onPage($(this).val(), data.pages);
                    loadHistory();
                }
            });
        }
    });
}