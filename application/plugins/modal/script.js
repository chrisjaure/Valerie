var modal = {};

jQuery(function($){

  modal.obj = $('<div id="modal-overlay"></div>').appendTo('body').css('opacity', 0).click(function(){
    modal.hideForm();
  });

  $('.modal-show').click(function(){
    modal.showForm($(this).next());
    return false;
  });
  
  $('.modal-cancel').click(function(){
    modal.hideForm();
    return false;
  });
  
  modal.showForm = function(frm) {
    var win = $(window),
        x = win.width() / 2 - frm.outerWidth() / 2,
        y = win.height() / 2 - frm.outerHeight() / 2,
        left = x + win.scrollLeft(),
        top = y + win.scrollTop();
    
    if (top < 10) top = 10;
    if (left < 10) left = 10;

    frm.css({
      left: left,
      top: top
    }).show();
    modal.obj.fadeTo("fast", 0.8).show();
  }
  
  modal.hideForm = function() {
    $('.modal-wrapper:visible').hide();
    modal.obj.hide().css('opacity', 0);
  }

});
