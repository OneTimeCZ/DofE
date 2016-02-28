$(function () {
    $("div.search button").click(function () {
        $.ajax({
            type: 'POST',
            data: {
                searchdata: $("#searchbar").val()
            },
            url: '/vyhledavani',
            success: function (response) {
                //this is an user object pff
                alert(JSON.parse(response));
            }
        });
    });
});