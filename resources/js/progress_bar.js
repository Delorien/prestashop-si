$(document).ready(function() {
    function rotate(selector) {
        $(selector).animate({
            left: $('.load').width()
        }, 1500, function() {
            $(selector).css("left", -($(selector).width()) + "px");
            rotate(selector);
        });
    }

    rotate('.bar');
});