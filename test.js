var table_cityTable;
document.addEventListener("DOMContentLoaded", function () {
    table_cityTable = $("#cityTable").DataTable({
        processing: true, serverSide: true, pageLength: 10, select: true, ajax: {
            url: "http://localhost/yazilimdelisi/admin555/list",
            type: "GET",
            data: function (d) {
                return {
                    page: (d.start / d.length) + 1,
                    length: d.length,
                    order: d.order,
                    search: d.search
                };
            },
            dataFilter: function (data) {
                var json = JSON.parse(data);
                return JSON.stringify({
                    draw: json.draw,
                    recordsTotal: json.datas.pagination.total,
                    recordsFiltered: json.datas.pagination.total,
                    data: json.datas.data
                });
            }
        }, columns: [{ data: "id", }, { data: "cityName", }, { data: "countryGuid", }, { data: "status", render: function (data) { return data == 1 ? "Aktif" : "Pasif"; }, },]
    });
    function handleButtonClick(functionName) {
        var selected = table_cityTable.rows({ selected: true }).data().toArray();
        if (typeof window[functionName] === "function") {
            window[functionName](selected);
        }
    }
});