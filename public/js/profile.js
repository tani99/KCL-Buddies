$(".box").mouseenter(function () {
    const textBox = $(this).children(".text");
    input = $(this).children(".input");

    input.children("input").val(textBox.html());
    textBox.css("display", "none");
    input.css("display", "inline-block");

}).mouseleave(function () {
    const textBox = $(this).children(".text");
    const input = $(this).children(".input");

    textBox.html(input.children("input").val());
    input.css("display", "none");
    textBox.css("display", "inline-block");
});

$( function() {
    $( "#datepicker" ).datepicker();
} );

$( "#datepicker" ).datepicker({
    dateFormat: "yy-mm-dd"
});


// document.getElementById("birthdate_input").value = $('#datepicker').val();