$(document).ready(function () {
    if($("input#islogged").attr("user-logged") == "true"){
        $("button#like").click(function () {
            var me = $(this);

            if($(this).hasClass("active")){
                $.ajax({
                    url: "/clanek/komentar/"+me.attr("comment-id")+"/remove-like"
                }).done(function (data) {
                    console.log($("span.like-count"+me.attr("comment-id")));
                    $("span.like-count"+me.attr("comment-id")).html(data);
                });
            } else {
                $.ajax({
                    url: "/clanek/komentar/"+me.attr("comment-id")+"/like"
                }).done(function (data) {
                    $("span.like-count"+me.attr("comment-id")).html(data);
                });
            }

            $(this).toggleClass("active");
        });
    }
});