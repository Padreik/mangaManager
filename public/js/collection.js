function getNextSeries() {
    base = $('head base').attr('href');
    $.ajax({
        url: base + "/import/ajax/series",
        dataType: "json"
    }).done(function(response) {
        currentValue = response.current_series;
        progressBar = $('.progress-bar');
        
        currentPourcentage = currentValue / progressBar.attr('aria-valuemax') * 100;
        
        progressBar.attr('aria-valuenow', currentValue);
        progressBar.css('width', currentPourcentage + "%");
        progressBar.find('.sr-only').text(currentPourcentage.toFixed() + "% Complete");
        
        if (response.next_series) {
            $("#importation_title").text(response.next_series);
            getNextSeries();
        }
        else {
            $("#importation_text").text("Importation terminé");
        }
    });
};