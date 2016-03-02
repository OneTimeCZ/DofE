$(function () {
    $("div.search button").click(function () {
        $.ajax({
            type: 'POST',
            data: {
                searchdata: $("#searchbar").val()
            },
            url: '/vyhledavani',
            success: function (response) {
                var data = JSON.parse(response);
                alert(data);
            }
        });
    });
});