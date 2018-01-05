function callLoad(page) {
    var fun = eval("load" + page.substring(0, 1).toUpperCase() + page.substring(1));
    if (fun) {
        fun();
    }
}

function loadPage(page) {
    /*
    var c = $.cookie("cookie_" + page);
    if (typeof(c) == "undefined" || c == null || c == "null") {
        $.getScript("./js/" + page + ".js", function(data, textStatus, jqXHR) {
            $.cookie("cookie_" + page, 1);
            callLoad(page);
        });
    } else {
        callLoad(page);
    }
    */
    $.getScript("./js/" + page + ".js", function(data, textStatus, jqXHR) {
        $.cookie("cookie_" + page, 1);
        callLoad(page);
    });
}

function loadMain(page) {
    // var table = $.cookie("cookie_table");
    // if (typeof(table) == "undefined" || table == null || table == "null") {
    //     $.getScript("./js/table.js", function(data, textStatus, jqXHR) {
    //         $.cookie("cookie_table", 1);
    //         loadPage(page);
    //     });
    // } else {
    //     loadPage(page);
    // }
    $.getScript("./js/table.js", function(data, textStatus, jqXHR) {
        $.cookie("cookie_table", 1);
        loadPage(page);
    });
}