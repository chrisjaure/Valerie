/* default form script */

(function($){
  var el, submitBtn;

  $.fn.valerie.events = {
    initialized: function(e, form) {
      el = $('<div class="valerie-form-message"/>').prependTo(form).hide();
      submitBtn = form.find('input[type=submit], button');
    },
    beforeSubmit: function(e, form) {
      submitBtn.attr('disabled', true);
      el.text('Loading...').show().removeClass('valerie-form-message-error');
      form.find('.valerie-field-error').remove();
      form.find('.valerie-alert').removeClass('valerie-alert');
      $(window).scrollTop(form.offset().top);
    },
    afterSubmit: function(e, form) {
      submitBtn.attr('disabled', false);
      el.show();
    },
    formValidated: function(e, message, form) {
      form[0].reset();
      el.text(message);
    },
    formInvalidated: function(e, els, message, form) {
      el.text(message).addClass('valerie-form-message-error');
      $.each(els, function(){
        var error = $('<span class="valerie-field-error">' + this.message + '</span>'),
            field = $('#'+this.id);
        if (field.is('[type=checkbox], [type=radio]')) {
          field.parent().append(error);
        }
        else if (field.is('legend')) {
          field.append(error).parent().addClass('valerie-alert');
        }
        else {
          field.before(error).addClass('valerie-alert');
        }
      });
    },
    error: function(e, form) {
      alert('Darn, something went wrong.');
    }
  }
})(jQuery);
