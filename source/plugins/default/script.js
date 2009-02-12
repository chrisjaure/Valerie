(function($){
  var el, submitBtn;

  $.fn.valerie.events = {
    initialized: function(e, form) {
      el = $('<div class="message"></div>').insertBefore(form).hide();
      submitBtn = form.find('input[type=submit]');
    },
    beforeSubmit: function(e, form) {
      submitBtn.attr('disabled', true);
      el.hide();
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
      el.text(message);
    },
    error: function(e, form) {
      alert('Error!');
    }
  }
})(jQuery);
