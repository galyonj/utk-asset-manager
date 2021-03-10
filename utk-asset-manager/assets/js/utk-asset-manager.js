"use strict";

var iconsBtn = document.getElementById('menu_icon-btn');
var iconsModal = document.getElementById('iconsModal');
var icons = document.querySelectorAll('.icons-list li svg');
var label = document.getElementById('label');
var singularLabel = document.getElementById('singular_label');
var itemName = document.getElementById('name');
var updateContent = document.querySelector('.update-posts-terms');

(function ($) {
  $(iconsBtn).click(function (e) {
    e.preventDefault();
    $(iconsModal).modal({
      focus: true
    }, 'show');
  });
  $(document).ready(function () {
    console.log('Hello!');
  });
  $('.selected-icon').hide(); // Listen for click on one of the icons

  $(icons).click(function () {
    console.log('Click on ' + this.parentNode.id); // Make sure that the 'selected' class is removed from all of the list items

    $('.icons-list').children('li').removeClass('selected'); // Then add it back to the list item parent of the click target

    this.parentNode.classList.add('selected'); // Display the selected SVG file in the modal footer for confirmation

    $('.selected-icon').show().find('span').empty();
    $('.selected > svg').clone().appendTo($('.selected-icon span'));
  });
  $('#icon-select-btn').click(function (e) {
    var selectedIcon = $('.selected').attr('id').replace('[', '-').replace(']', '');
    $('#menu_icon').val(selectedIcon).focus();
    console.log($('.selected').attr('id') + ' was selected');
    $(iconsModal).modal('hide');
  });
})(jQuery); // TODO: Detect whether a cpt or taxonomy
//  has already been registered during form
//  creation instead of waiting for the $_POST?

/**
 * On change to the singular label field,
 * populate the name field with a formatted
 * version of the value from the singular
 * label field.
 *
 * @since 0.5.0
 */


singularLabel.addEventListener('change', function (e) {
  var regex = '/[\'"]/ig';
  document.getElementById('name').value = e.target.value.toLowerCase().replaceAll(regex, '').split(' ').join('_');
});
/**
 * We only want to display the checkbox for updating the
 */

itemName.addEventListener('change', function (e) {
  var qs = window.location.search;
  var params = new URLSearchParams(qs);

  if (params.has('name') && e.target.value !== params.get('name')) {
    updateContent.css('display', 'block');
  }
});