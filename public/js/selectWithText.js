$("a[data-add-to]").click(function(e) {
    e.preventDefault();
    // Find text to add
    var name = $(this).data('add-to');
    var newTextBox = $("input[name='new_"+name+"']");
    var newText = newTextBox.val();
    if (!newText) {
        alert('Veuillez entrer une valeur à ajouter');
        return false;
    }
    
    // Create option from text and empty textbox
    var newValue = "add".concat(newText);
    var optionString = "<option value='###VALUE###'>###TEXT###</option>";
    var newOption = optionString.replace('###VALUE###', newValue).replace('###TEXT###', newText);
    newTextBox.val('');

    // Add the option to the textbox
    var dropDown = $("select[name='"+name+"']");
    dropDown.prepend(newOption);

    // Select the new option
    if (dropDown.attr('multiple')) {
        dropDown.multiselect('rebuild');
        dropDown.multiselect('select', newValue);
    }
    else {
        $("select[name='"+name+"'] option[value='"+newValue+"']").prop('selected', true);
    }

    // Duplicate new option if needed, usefull for artist and author
    if ($(this).data('duplicate-in')) {
        var secondDropDownName = $(this).data('duplicate-in');
        var secondDropDownSelector = "select[name='"+secondDropDownName+"']";
        var secondDropDown = $(secondDropDownSelector);
        secondDropDown.prepend(newOption);
        if (secondDropDown.attr('multiple')) {
            secondDropDown.multiselect('rebuild');
            secondDropDown.multiselect('select', newValue);
        }
        else {
            $(secondDropDownSelector+" option[value='"+newValue+"']").prop('selected', true);
        }
    }
});