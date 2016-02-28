$(document).ready(function () {
    $("a.bronze").click(function () {
        $("h2.bronze").toggleClass("normal");
        $("h2.bronze a span").toggleClass("fa-plus");
        $("h2.bronze a span").toggleClass("fa-minus");
    });
    
    $("a.silver").click(function () {
        $("h2.silver").toggleClass("normal");
        $("h2.silver a span").toggleClass("fa-plus");
        $("h2.silver a span").toggleClass("fa-minus");
    });
    
    $("a.gold").click(function () {
        $("h2.gold").toggleClass("normal");
        $("h2.gold a span").toggleClass("fa-plus");
        $("h2.gold a span").toggleClass("fa-minus");
    });
});