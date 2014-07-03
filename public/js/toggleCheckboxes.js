
toggleCheckboxStatus = true;
$('a.toggle-checkbox').click(function(e) {
    e.preventDefault();
    $(this).html(toggleCheckboxStatus ? "Dé-sélectionner tout" : "Sélectionner tout");
    $('input[type=checkbox]').each(function() {
        this.checked = toggleCheckboxStatus;
    });
    toggleCheckboxStatus = !toggleCheckboxStatus;
});