function SmartTable() {
    this.pageTotal = 0;
    this.pageCur = 0;
    this.ths = null;
    this.ids = [];
    this.trs = [];
    this.hasOrder = false;
    this.hasEdit = false;

    this.setPage = function(pageTotal, pageCur) {
        this.pageTotal = parseInt(pageTotal);
        this.pageCur = parseInt(pageCur);
    };

    this.setHeader = function(ths) {
        this.ths = ths;
    };

    this.addRow = function(id, td) {
        this.ids.push(id);
        this.trs.push(td);
    };

    this.setOrder = function() {
        this.hasOrder = true;
    };

    this.setEdit = function() {
        this.hasEdit = true;
    };

    this.getTable = function() {
        var i = 0,
            j = 0;
        var html = "";

        if (this.pageTotal > 1) {
            html += "<div id='gotoPage'>";
            var pageMax = 6;
            var pageTotal = this.pageTotal;
            if (pageTotal > pageMax) {
                pageTotal = pageMax;
            }
            var from = 1,
                to = this.pageTotal;
            var l = this.pageCur - from,
                r = to - this.pageCur;
            if (l < r) {
                if (l > pageMax / 2) {
                    from = this.pageCur - pageMax / 2;
                    to = this.pageCur + pageMax / 2;
                } else {
                    to = this.pageCur + pageMax - l;
                }
            } else {
                if (r > pageMax / 2) {
                    from = this.pageCur - pageMax / 2;
                    to = this.pageCur + pageMax / 2;
                } else {
                    from = this.pageCur - (pageMax - r);
                }
            }
            if (from > 1) {
                html += "<span class='page'>&lt;&lt;</span>&nbsp;";
            }
            for (i = from; i < this.pageCur; i++) {
                html += "<span class='page'>" + i + "</span>&nbsp;";
            }
            html += "<span class='pagecurrent'>" + this.pageCur + "</span>";
            for (i = this.pageCur + 1; i <= to; i++) {
                html += "&nbsp;<span class='page'>" + i + "</span>";
            }
            if (to < this.pageTotal) {
                html += "&nbsp;<span class='page'>&gt;&gt;</span>";
            }
            if (pageMax < this.pageTotal) {
                html += "&nbsp;<span>" +
                    "<input type='text' id='fpage' " +
                    "min='1' max='" + this.pageTotal + "' " +
                    "value='" + this.pageCur + "' /></span>";
            }
            html += "</div>";
        }

        html += "<div><table id='edittable'>";
        if (this.ths !== null || this.hasOrder || this.hasEdit) {
            html += "<tr>";
            for (i = 0; i < this.ths.length; i++) {
                html += "<th>" + this.ths[i] + "</th>";
            }
            if (this.hasOrder) {
                html += "<th>" + "排序" + "</th>";
            }
            if (this.hasEdit) {
                html += "<th>" + "操作" + "</th>";
            }
            html += "</tr>";
        }
        if (this.trs.length > 0) {
            for (i = 0; i < this.trs.length; i++) {
                html += "<tr value='" + this.ids[i] + "'>";
                for (j = 0; j < this.trs[i].length; j++) {
                    html += "<td>" + this.trs[i][j] + "</td>";
                }
                if (this.hasOrder) {
                    html += "<td>" +
                        "<span class='order' value='top'>顶</span>&nbsp;" +
                        "<span class='order' value='bottom'>底</span>&nbsp;" +
                        "<span class='order' value='up'>上</span>&nbsp;" +
                        "<span class='order' value='down'>下</span>" +
                        "</td>";
                }
                if (this.hasEdit) {
                    html += "<td>" +
                        "<span class='mod'>改</span>&nbsp;" +
                        "<span class='del'>删</span>" +
                        "</td>";
                }
                html += "</tr>";
            }
        }
        html += "</table></div>";
        return html;
    };
}