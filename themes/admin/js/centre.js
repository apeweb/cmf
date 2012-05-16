(function($){
  $.fn.extend({
    centre: function () {
      return this.each(function() {
        var top = ($(window).height() - $(this).outerHeight()) / 2;
        top += document.body.scrollTop;
        $(this).css({'margin-top':top+'px'});
      });
    }
  });
})(jQuery);

$(document).ready(function() {
  $('#content').centre();
});
$(window).resize(function() {
  $('#content').centre();
});