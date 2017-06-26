$(document).ready(function() {
    $(document.body).on("click", '.entTab', function() {
        if (!$(this).hasClass("selected")) {
            var id = $(this).attr("id");
            $(".entTab").removeClass("selected");
            $(".divTab").slideUp(500);
            $("#ta" + id).slideDown(500);
            $(this).addClass("selected");
        }
    })
})