// Change toggle icon and type on click.
$(document).ready(function(){
  $("#togglePasswordLogin").on("click", function() {
    $(this).toggleClass("bi-eye");
    var password = $('#inputPassword3');
    var type = password.attr('type') === "password" ? "text" : "password";
    $('#inputPassword3').attr('type', type);
  });
  $("#loginSubmit").on("click", function(event) {
    var val = $("#privilege").val();
    if (val != "User" || val != "Admin") {
      $("#privilegeSpan").toggle();
      // event.preventDefault();
    }
  });
});
