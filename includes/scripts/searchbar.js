//variable for keeping track of user typing breaks
var typingTimer;

//interval before calling function after user hasn't typed anything in miliseconds
var typingInterval = 3000;

//on releasing a key, waits defined time, and then calls function doneTyping()
$('#searchbar').keyup(function () {
    clearTimeout(typingTimer);
    if('#searchbar'.val()) {
        typingTimer = setTimeout(doneTyping, typingInterval);
    }
});

//user finished typing
function doneTyping() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
            //dropdown under the search bar, fill with foreach
            document.getElementById('#search-dropdown').innerHTML = xhttp.responseText;
        }
    };
    
    var searchtxt = '#searchbar'.val();
    
    xhttp.open('POST', '/vyhledavani', true);
    xhttp.send('text='+searchtxt);
}