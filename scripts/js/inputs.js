var typingTimer;
var usernameInputDoneTypingInterval = 5000;
var $input = $("#myInput");

$input.on("keyup", function() {
  clearTimeout(typingTimer);
  typingTimer = setTimeout(
    usernameInputDoneTyping,
    usernameInputDoneTypingInterval
  );
});

$input.on("keydown", function() {
  clearTimeout(typingTimer);
});

function usernameInputDoneTyping() {
  //do something
}

$("#show_hide_password a").on("click", function(event) {
  event.preventDefault();
  if ($("#show_hide_password input").attr("type") == "text") {
    $("#show_hide_password input").attr("type", "password");
    $("#show_hide_password i").addClass("fa-eye-slash");
    $("#show_hide_password i").removeClass("fa-eye");
  } else if ($("#show_hide_password input").attr("type") == "password") {
    $("#show_hide_password input").attr("type", "text");
    $("#show_hide_password i").removeClass("fa-eye-slash");
    $("#show_hide_password i").addClass("fa-eye");
  }
});

var removeText = $("<div class='removeContent'>X</div>");
removeText

$("input").on("keypress", function() {
  var current = $(this);
  if (current.parent().find(".removeContent").length == 0) {
    var clone = removeText.clone();
    clone.on("click", function() {
      var current = $(this);
      current.prev().val("");
      current.remove();
    });
    current.after(clone);
  }
});
