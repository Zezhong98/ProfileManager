$(document).ready(function() {
  positionItemTemplate = $('#positionTemplate').html();
  positionCounter = 0;
  educationItemTemplate = $('#educationTemplate').html();
  educationCounter = 0;


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

    addPositionItem({positionID: positionCounter });

    positionCounter++;

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

    addEducationItem({educationID: educationCounter });

    educationCounter++;

    console.log("education counter: " + educationCounter);
  })


  // position remover
  $('#positionSpace').on('click', '.positionRemoveBtn', function(event) {
    // ATTENTION : use delegate to dynamically catch new DOM element
    console.log("- clicked");
    event.preventDefault();

    var $positionItem = $(this).closest('.positionItem');
    var positionID = $positionItem.attr('id');

    $positionItem.remove();
    console.log(positionID + " removed");


    if (positionID == positionCounter) {
      console.log("positionCounter-1");
      positionCounter--;
    }
    else {
      console.log("existing ids changing");

      for (var i = Number(positionID)+1; i <= positionCounter; i++) {
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

    console.log("position counter: " + positionCounter);
  })


  // education remover
  $('#educationSpace').on('click', '.educationRemoveBtn', function(event) {
    // ATTENTION : use delegate to dynamically catch new DOM element
    console.log("education - clicked");
    event.preventDefault();

    var $educationItem = $(this).closest('.educationItem');
    var educationID = $educationItem.attr('id');

    $educationItem.remove();
    console.log("education " + educationID + " removed");


    if (educationID == educationCounter) {
      console.log("educationCounter-1");
      educationCounter--;
    }
    else {
      console.log("existing ids changing");

      for (var i = Number(educationID)+1; i <= educationCounter; i++) {
        console.log("index i is " + i);
        $item = $('#'+i+'.educationItem');
        console.log($item.html());
        $item.attr('id', i-1);
        $item.find("input").attr('name', "year_edu_"+(i-1));
        $item.find("input").attr('name', "school_edu_"+(i-1));
        console.log($item.html());
      }
      educationCounter--;
    }

    console.log("education counter: " + educationCounter);
  })
})
