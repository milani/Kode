$(document).ready(function() {
    // Handling with tables (adding first and last classes for borders and adding alternate bgs)
    $('tbody tr:even').addClass('even');
    $('table.grid tbody tr:last-child').addClass('last');
    $('tr th:first-child, tr td:first-child').addClass('first');
    $('tr th:last-child, tr td:last-child').addClass('last');
    $('form.fields fieldset:last-child').addClass('last');
    // Handling with lists (alternate bgs)
    $('ul.simple li:even').addClass('even');
    
    // // Superfish navigation
    $("ul#nav").superfish({
        delay: 0,
        speed: 'fast'
    });
});