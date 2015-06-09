$(function()
{
	
});

$(function () {
  var $logo = $("div.topmore");
  var $ul = $(".hide-nav");
  var $cover = $("div.cover-1");
  var $ula = $('.hide-nav li a');
  $logo.on("click", function () {
      $("div.cover-1").toggle();
      $(".hide-nav").slideToggle();
  });
  $cover.bind('click', function (e) {
      $("div.cover-1").toggle();
      $(".hide-nav").slideToggle();
  });
  });