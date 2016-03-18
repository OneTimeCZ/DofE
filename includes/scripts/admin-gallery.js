$(document).ready(function () {
    $("img").click(function () {
        $(this).parents("div.inner").toggleClass("to-delete");
        if($(this).parents("div.inner").hasClass("to-delete")){
            $("form.image-delete").append("<input type='hidden' name='users[]' value="+$(this).attr("data-id")+">");
        } else {
            $("input[value="+$(this).attr("data-id")+"]").remove();
        }
    });
    
    $("a#delete").click(function () {
        $("form.image-delete").submit();
    });
});