"use strict";

// This is just here to make sure that I'm putting the directory into the right place.
var iconsBtn = document.getElementById('menu_icon-btn');
var iconsModal = document.getElementById('iconsModal');

(function ($) {
  iconsBtn.addEventListener('click', function (e) {
    $(iconsModal).modal('show');
  });
})(jQuery);