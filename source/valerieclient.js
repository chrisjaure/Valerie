//------------------------------------------------------------------------------
//	Valerie v0.6
//	(c) 2009 Chris Jaure
//	license: MIT License
//	website: http://code.google.com/p/valerie/
//
//	valerieclient.js
//------------------------------------------------------------------------------

(function($){
  $.fn.valerie = function(o) {
  
    var events = $.extend({}, $.fn.valerie.events, o);
  
    return this.each(function(){
      var form = $(this);
      
      for (var event in events) {
        form.bind('valerie.'+event, events[event]);
      }

      form.submit(function(e){
        e.preventDefault();
        $.ajax({
          url: form.attr('action'),
          type: form.attr('method'),
          data: form.serialize() + '&_ajax=1',
          dataType: 'json',
          beforeSend: function(){
            form.trigger('valerie.beforeSubmit', [form]);
          },
          complete: function(){
            form.trigger('valerie.afterSubmit', [form]);
          },
          success: function(response){
            if (response) {
              if (parseInt(response.type, 10) > 1) {
                form.trigger('valerie.formInvalidated', [response.content, response.message, form]);
              }
              else {
                form.trigger('valerie.formValidated', [response.message, form]);
              }
            }
            else {
              form.trigger('valerie.error', [form]);
            }
          },
          error: function(){
            form.trigger('valerie.error', [form]);
          }
        });
      }).trigger('valerie.initialized', [form]);
    });
    
  }
  
  $.fn.valerie.events = {};
  
})(jQuery);
