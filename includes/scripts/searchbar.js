$(function () {
    //setup, interval in [ms]
    var typingTimer, doneTypingInterval = 1500;

    //user has finished typing
    function doneTyping() {
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
    }
    
    //start countdown onKeyUp
    $('#searchbar').keyup(function () {
        clearTimeout(typingTimer);
        if ($('#searchbar').val) {
            typingTimer = setTimeout(doneTyping, doneTypingInterval);
        }
    });
});