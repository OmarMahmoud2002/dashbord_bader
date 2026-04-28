// Switch links
$(".links_stng li").click(function () {
  $(this).addClass("active").siblings().removeClass("active");
  $(".all_sections > div").hide();
  $($(this).data("sec")).fadeIn();
});