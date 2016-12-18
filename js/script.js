$(function() {
  $('#cp4').colorpicker({
      color: '#ffffff',
      format: 'rgba'
  });
});


$('#cp4').on('changeColor', function(e) {
    $('body')[0].style.backgroundColor = "hsla(" + (e.color.value.h * 360) + ", " + (e.color.value.s * 100) + "%, " + (e.color.value.b * 100) + "%, " + e.color.value.a + ")";
});

$("#heightspacing, #widthspacing").on("input change", function() {
  if ($(this).prop("id") == "heightspacing") {
    $("#yspacing").html($(this).val());
  } else {
    $("#xspacing").html($(this).val());
  }
});

$("#maxoffsety, #maxoffsetx").on("input change", function() {
  if ($(this).prop("id") == "maxoffsety") {
    $("#maxoy").html($(this).val());
  } else {
    $("#maxox").html($(this).val());
  }
});

$("#minsize, #maxsize").on("input change", function() {
  if ($(this).prop("id") == "minsize") {
    $("#minsizenumber").html($(this).val());
  } else {
    $("#maxsizenumber").html($(this).val());
  }
});

$("#size").on("input change", function() {
  $("#sizenumber").html($(this).val());
});

$('.showhidden').click(function(){
    if($(this).is(':checked')){
        $('.hiddenrange').show();
        $('.shownrange').hide();
    } else {
        $('.hiddenrange').hide();
        $('.shownrange').show();
    }
});

$('.shapes label').click(function(){
    if($(this).find("input").val() == "triangle"){
        $('.enableshapes').hide();
        $('.triangle_position').show();
    } else if ($(this).find("input").val() == "mixed") {
        if ($(".enabletriangle").is(":checked")) {
          $('.triangle_position').show();
        } else {
          $('.triangle_position').hide();
        }
        $('.enableshapes').show();
    } else {
        $('.triangle_position').hide();
        $('.enableshapes').hide();
    }
});

$('.enableshapes label').click(function(){
    if($(this).find("input").val() == "triangle" && $(this).find("input").is(":checked")){
        $('.triangle_position').hide();
    } else if ($(this).find("input").val() == "triangle") {
        $('.triangle_position').show();
    }
});

$("#submit").click(function(){

  $(".file_selection").css("border", "none");
  $(".enableshapes").css("border", "none");

  //get values
  var shape = $('input[name=shape]:checked').val();
  var bgcss = $('body').css("background-color");
  var rgb = bgcss.replace(/^rgba?\(|\s+|\)$/g,'').split(',');
  var xspacing = $("#widthspacing").val();
  var yspacing = $("#heightspacing").val();
  var maxoffsetx = $("#maxoffsetx").val();
  var maxoffsety = $("#maxoffsety").val();
  var colorfrom = $('input[name=colorfrom]:checked').val();
  var triangleposition = $('input[name=triangle_position]:checked').val();
  var enabledshapes = $('input[name=shapesenabled]:checked').map(function(){
    return $(this).val();
  }).get();

  if (shape == "mixed") {
    if (enabledshapes.length < 2) {
      $(".enableshapes").css("border", "5px solid red");
      alert("You need to select at least two shapes in 'Enable / disable shapes'! If you want to select only one shape, use the buttons on top of the page.");
      return
    }
  }

  if ($('#choose_file')[0].files[0] === undefined) {
    $(".file_selection").css("border", "5px solid red");
    alert("Please select a file.");
    return
  }

  var formData = new FormData();
  formData.append('file', $('#choose_file')[0].files[0]);
  formData.append('shape', shape);
  formData.append('background', JSON.stringify(rgb)); //fuck off with this bs, why do i need to stringify an array just to append it to the form data
  formData.append('xspacing', xspacing);
  formData.append('yspacing', yspacing);
  formData.append('xoffset', maxoffsetx);
  formData.append('yoffset', maxoffsety);
  formData.append('colorfrom', colorfrom);
  formData.append('triangleposition', triangleposition);
  if (shape == "mixed") {
    formData.append('enabledshapes', JSON.stringify(enabledshapes));
  }

  if ($('.showhidden').is(':checked')) {
    var minsize = $("#minsize").val();
    var maxsize = $("#maxsize").val();
    if (minsize > maxsize) {
      alert("Min size cannot be more than (or equal to) max size!");
      return
    }
    formData.append('minsize', minsize);
    formData.append('maxsize', maxsize);
  } else {
    var size = $("#size").val();
    formData.append('size', size);
  }

  $(".loader").show();

  $.ajax({
         url : 'makeimage.php',
         type : 'POST',
         data : formData,
         processData: false,  // tell jQuery not to process the data
         contentType: false,  // tell jQuery not to set contentType
         success : function(data) {
            $(".loader").hide();
            $(".results").append(data);
         }
  });

});
