(function($){
  $(window).on("load",function(){
    var winWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
    if(winWidth>1910){
      $(".mCustomScrollbar").mCustomScrollbar("destroy");
    }else{
      $(".mCustomScrollbar").mCustomScrollbar({
        axis:'x',
        advanced:{autoExpandHorizontalScroll:true},
      });
  }
  });
})(jQuery);