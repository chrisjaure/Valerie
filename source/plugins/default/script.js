/* default form script */

(function($){

  $.fn.valerie.events = {
    initialized: function(e, form) {
      var el = $('<div class="valerie-form-message"/>').prependTo(form).hide();
      var submitBtn = form.find('input[type=submit], button');
      form.data('message', el).data('button', submitBtn);
    },
    beforeSubmit: function(e, form) {
      form.data('button').attr('disabled', true);
      form.data('message')
        .text('Loading...')
        .show()
        .removeClass('valerie-form-message-error');
      form.find('.valerie-field-error').remove();
      form.find('.valerie-alert').removeClass('valerie-alert');
      var win = $(window), top = form.offset().top;
      if (win.scrollTop() > top) {
        win.scrollTop(top);
      }
    },
    afterSubmit: function(e, form) {
      form.data('button').attr('disabled', false);
      form.data('message').show();
    },
    formValidated: function(e, message, form) {
      form[0].reset();
      form.data('message').text(message);
    },
    formInvalidated: function(e, els, message, form) {
      form.data('message').text(message).addClass('valerie-form-message-error');
      $.each(els, function(){
        if (!this.message) return;
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
