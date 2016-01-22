$(document).ready(function(){
    $("a.bronze").click(function(){
        $("h2.bronze").toggleClass("normal");
    });
    
    $("a.silver").click(function(){
        $("h2.silver").toggleClass("normal");
    });
    
    $("a.gold").click(function(){
        $("h2.gold").toggleClass("normal");
    });
});