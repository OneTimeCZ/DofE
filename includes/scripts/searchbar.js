$(function () {
    //setup, interval in [ms]
    var typingTimer, doneTypingInterval = 1500;

    //user has finished typing
    function doneTyping() {
        $.ajax({
            type: 'POST',
            data: {searchdata: $("#searchbar").val()},
            url: '/vyhledavani'
        }).done(function (response) {
            alert(response);
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