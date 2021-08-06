jQuery(function ($) {
    $('#teams_posts2').select2();
    $('#players_posts2').select2();

    $('#verified2,#unverified2,#vpending2').on("click", function () {
        $('input[type="radio"]').parent().removeClass('vactive')
        $('input[type="radio"]:checked').parent().addClass('vactive')
    });
});