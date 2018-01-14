
$(function() {

  // Get the elements
  var $items = $('.album__photos');

  if ($items.length) {
    $items.magnificPopup({
      delegate: 'a',
      type: 'image',
      gallery: {
        enabled: true
      }
    });
  }

});
