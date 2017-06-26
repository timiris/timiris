$(document).ready(function() {
    $(document.body).on("click", '.tabChrome', function() {
        if (!$(this).hasClass("active")) {
            $('.tabChrome').removeClass('active');
            var id = $(this).attr("id");
            $(".divTab").slideUp(500);
            $("#ta" + id).slideDown(500);
            $(this).addClass("active");
        }
    })
})