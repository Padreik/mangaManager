
base = $('head base').attr('href');
totalTime = 0;
// Add this variable in the template
//totalImportationToDo = 0;

function getNextSeries() {
    startTime = $.now();
    $.ajax({
        url: base + "/import/ajax/series",
        dataType: "json"
    }).done(function(response) {
        currentValue = response.current_index;
        
        currentPourcentage = currentValue / totalImportationToDo * 100;
        
        progressBar = $('.progress-bar');
        progressBar.attr('aria-valuenow', currentValue);
        progressBar.css('width', currentPourcentage + "%");
        progressBar.find('.sr-only').text(currentPourcentage.toFixed() + "% Complete");
        
        endTime = $.now();
        totalTime += (endTime - startTime)/1000;
        
        if (response.next_import) {
            averageTime = totalTime / currentValue;
            remaining = (totalImportationToDo - currentValue) * averageTime;
            remainingText = millisecondsToRealTime(remaining);
            
            $("#temps_restants").text(remainingText);
            $("#importation_progression").text(currentValue + 1);
            $("#importation_title").text(response.next_import);
            getNextSeries();
        }
        else {
            $("#temps_restants").text("-");
            $("#importation_text").text("Importation terminé");
        }
    });
}

function millisecondsToRealTime(milli) {
    secondesTotales = milli / 1000;
    secondes = Math.floor(milli % 60);
    minutes = Math.floor(milli / 60);
    text = '';
    if (minutes > 0) {
        text = minutes + ' minute' + (minutes > 1 ? 's' : '') + ' ';
    }
    if (secondes > 0) {
        text += secondes + ' seconde' + (secondes > 1 ? 's' : '') + ' ';
    }
    return text;
}