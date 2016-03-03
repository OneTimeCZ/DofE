$(function () {
    $("div.search button").click(function () {
        $.ajax({
            type: 'POST',
            data: {
                searchdata: $("#searchbar").val()
            },
            url: '/vyhledavani',
            success: function (response) {
                //objects
                var data = JSON.parse(response);
                
                /*
                $.each(data, function (i, d) {
                    console.log(d.Username);
                });
                */
            }
        });
    });
});