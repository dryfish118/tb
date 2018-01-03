function loadMain(page) {
    switch (page) {
        case "user":
            $.getScript("./js/user.js", function(data, textStatus, jqXHR) {
                loadUser();
            });
            break;
        case "issue":
            $.getScript("./js/issue.js", function(data, textStatus, jqXHR) {
                loadIssue();
            });
            break;
        case "brand":
            $.getScript("./js/brand.js", function(data, textStatus, jqXHR) {
                loadBrand();
            });
            break;
        case "size":
            $.getScript("./js/size.js", function(data, textStatus, jqXHR) {
                loadSize();
            });
            break;
        case "color":
            $.getScript("./js/color.js", function(data, textStatus, jqXHR) {
                loadColor();
            });
            break;
        case "cat":
            $.getScript("./js/cat.js", function(data, textStatus, jqXHR) {
                loadCat();
            });
            break;
        case "client":
            $.getScript("./js/client.js", function(data, textStatus, jqXHR) {
                loadClient();
            });
            break;
        case "history":
            $.getScript("./js/history.js", function(data, textStatus, jqXHR) {
                loadHistory();
            });
            break;
    }
}