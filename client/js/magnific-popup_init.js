
$(function() {

  // Get the elements
  var $items = $('.photo-item');

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
