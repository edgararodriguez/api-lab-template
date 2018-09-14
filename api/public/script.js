$(document).ready(function() {
  $("#AthletesForm").submit(function(event) {
    var form = $(this);
    event.preventDefault();
    $.ajax({
      type: "POST",
      url: "http://localhost:8080/firstSlim/Athletes",
      data: form.serialize(), // serializes the form's elements.
      success: function(data) {
        window.location.replace("http://localhost:8080/api");
      }
    });
  });
  $("#AthletesEditForm").submit(function(event) {
    alert( "TODO: build submit handler.  See AthletesForm submit handler for inspiration " );

  });
  $( ".deletebtn" ).click(function() {
    if (window.confirm("Do you want to delete this Athelte?")) {
         $.ajax({
           type: "DELETE",
           url: "http://localhost:8080/api/Athletes/" + $(this).attr("data-id"),
           success: function(data) {
             window.location.reload();
           }
         });
       }
     });
     });
