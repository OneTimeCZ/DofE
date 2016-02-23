$(function () {
    $('button.next').click(function () {
        $(this).parents().eq(2).toggleClass('hidden');
        $(this).parents().eq(2).next().toggleClass('hidden');
    });
    
    $('button.previous').click(function () {
        $(this).parents().eq(2).toggleClass('hidden');
        $(this).parents().eq(2).prev().toggleClass('hidden');
    });
});