loanButtonChanged = false;

$(function() {
    $("input[name='mangasId[]']").click(function() {
        if (!loanButtonChanged) {
            loanButtonChanged = true;
            var button = $(".loan-button");
            var method = $("input[name='_method']");
            if (button.hasClass('btn-success')) {
                button.removeClass('btn-success');
                button.text("Prêter les mangas");
            }
            else {
                button.removeClass('btn-danger');
                button.text("Modifier le prêt");
            }
            method.val('POST');
            button.addClass('btn-warning');
        }
    });
});