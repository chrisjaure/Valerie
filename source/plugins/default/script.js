(function($){
  var el, submitBtn;

  $.fn.valerie.events = {
    initialized: function(e, form) {
      el = $('<strong class="valerie-form-message"></strong>').insertBefore(form).hide();
      submitBtn = form.find('input[type=submit]');
    },
    beforeSubmit: function(e, form) {
      submitBtn.attr('disabled', true);
      el.hide();
      form.find('.valerie-field-error').remove();
    },
    afterSubmit: function(e, form) {
      submitBtn.attr('disabled', false);
      el.show();
    },
    formValidated: function(e, message, form) {
      form[0].reset();
      el.text(message)
        .removeClass('valerie-form-message-error');
    },
    formInvalidated: function(e, els, message, form) {
      el.text(message).addClass('valerie-form-message-error');
      $.each(els, function(){
        var error = $('<label for="' + this.id + '" class="valerie-field-error">' + this.message + '</label>'),
            field = $('#'+this.id);
        if (field.is('[type=checkbox], [type=radio]')) {
          field.next().after(error);
        }
        else {
          field.after(error);
        }
        
      });
      $(window).scrollTop(form.offset().top);
    },
    error: function(e, form) {
      alert('Error!');
    }
  }
})(jQuery);
