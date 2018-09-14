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
    var form = $(this);
    var athleteID = $(this).attr("data-id");
    event.preventDefault();
    $.ajax({
      type: "PUT",
      url: "http://localhost:8080/firstSlim/Athletes/" + athleteID,
      data: form.serialize(),
      success: function(data) {
        window.location.replace("http://localhost:8080/api");
      }
    });
  });
  $( ".deletebtn" ).click(function() {
    if (window.confirm("Do you want to delete this Athelte?")) {
         $.ajax({
           type: "DELETE",
           url: "http://localhost:8080/firstSlim/Athletes/" + $(this).attr("data-id"),
           success: function(data) {
             window.location.reload();
           }
         });
       }
     });
     });
