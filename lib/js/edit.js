$(document).ready(function() {
  positionItemTemplate = $('#positionTemplate').html();
  educationItemTemplate = $('#educationTemplate').html();

  positionCounter = $('#positionSpace input[name="num_pos"]').attr('value');
  $('#positionSpace').removeAttr("num");
  console.log('position counter: ' + positionCounter);

  educationCounter = $('#educationSpace input[name="num_edu"]').attr('value');
  $('#educationSpace').removeAttr("num");
  console.log('education counter: ' + educationCounter);

  $('#educationSpace').on('keyup', '.educationItem input.school', function() {
    console.log($('.educationItem input.school').html());
    $(this).autocomplete({
      source: "lib/school.php"
    });
  })

  function addPositionItem(item) {
    $(".profileForm #positionSpace").append(Mustache.render(positionItemTemplate, item));
  }

  function addEducationItem(item) {
    $(".profileForm #educationSpace").append(Mustache.render(educationItemTemplate, item));
  }


  // position adder
  $('#addPositionBtn').on('click', function(event) {
    console.log("position + clicked");
    event.preventDefault();
    // ATTENTION : use preventDefault to prevent button from loading
    if (positionCounter >=9 ) {
      alert('Max number of positions is 9!');
      return;
    }

    addPositionItem({positionID: Number(positionCounter) });

    positionCounter++;
    $('#positionSpace input[name="num_pos"]').attr("value", positionCounter);

    console.log("position counter: " + positionCounter);
  })


  // education adder
  $('#addEducationBtn').on('click', function(event) {
    console.log("education + clicked");
    event.preventDefault();
    // ATTENTION : use preventDefault to prevent button from loading
    if (educationCounter >= 9 ) {
      alert('Max number of educations is 9!');
      return;
    }

    addEducationItem({educationID: Number(educationCounter) });

    educationCounter++;
    $('#educationSpace input[name="num_edu"]').attr("value", educationCounter);

    console.log("education counter: " + educationCounter);
  })


  // position remover
  $('#positionSpace').on('click', '.positionRemoveBtn', function(event) {
    // ATTENTION : use delegate to dynamically catch new DOM element
    console.log("position - clicked");
    event.preventDefault();

    var $positionItem = $(this).closest('.positionItem');
    var positionRank = $positionItem.attr('id');

    // remove position item
    $positionItem.remove();
    console.log("position " + positionRank + " removed");


    // manage positionCounter and other html items
    if (positionRank == positionCounter) {
      console.log("positionCounter--");
      positionCounter--;
    }
    else {
      console.log("existing ids changing");

      for (var i = Number(positionRank)+1; i <= positionCounter; i++) {
        console.log("index i is " + i);
        $item = $('#'+i+'.positionItem');
        console.log($item.html());
        $item.attr('id', i-1);
        $item.find("input").attr('name', "year_pos_"+(i-1));
        $item.find("textarea").attr('name', "detail_pos_"+(i-1));
        console.log($item.html());
      }
      positionCounter--;
    }

    $('#positionSpace input[name="num_pos"]').attr("value", positionCounter);
    console.log("position counter: " + positionCounter);
  })


  // education remover
  $('#educationSpace').on('click', '.educationRemoveBtn', function(event) {
    // ATTENTION : use delegate to dynamically catch new DOM element
    console.log("education - clicked");
    event.preventDefault();

    var $educationItem = $(this).closest('.educationItem');
    var educationRank = $educationItem.attr('id');

    $educationItem.remove();
    console.log("education " + educationRank + " removed");

    // manage educationCounter and other html items
    if (educationRank == educationCounter) {
      console.log("educationCounter--");
      educationCounter--;
    }
    else {
      console.log("existing ids changing");

      for (var i = Number(educationRank)+1; i <= educationCounter; i++) {
        console.log("index i is " + i);
        $item = $('#'+i+'.educationItem');
        // console.log($item.html());
        $item.attr('id', i-1);
        $item.find("input").attr('name', "year_edu_"+(i-1));
        $item.find("input.school").attr('name', "school_edu_"+(i-1));
        // console.log($item.html());
      }
      educationCounter--;
    }

    $('#educationSpace input[name="num_edu"]').attr("value", educationCounter);
    console.log("education counter: " + educationCounter);
  })
})
