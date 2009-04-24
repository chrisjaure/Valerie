//------------------------------------------------------------------------------
//	Valerie v0.6
//	(c) 2009 Chris Jaure
//	license: MIT License
//	website: http://code.google.com/p/valerie/
//
//	valerieclient.js
//------------------------------------------------------------------------------

/*
  Function: valerie
  
  jQuery plugin to enable instant form validation feedback. This function simply
  adds event hooks for plugins to interact with.
  
      $('#formId').valerie({
        ititialized: function(event, form){
          alert("Valerie has been initialized");
        },
        ...
      });
  
  The following custom events are fired in the form.
  
  - valerie.initialized (receives event and form)
  - valerie.beforeSubmit (receives event and form)
  - valerie.afterSubmit (receives event and form)
  - valerie.formInvalidated (receives event, array of invalid fields,
    response message, and form)
  - valerie.formValidated (receives event, response message, and form)
  - valerie.error (receives event and form)
      
  Global defaults can also be set:
  
      $.fn.valerie.events = {
        ititialized: function(event, form){
          alert("Valerie has been initialized");
        },
      }
*/

(function($){
  $.fn.valerie = function(o) {
  
    var events = $.extend({}, $.fn.valerie.events, o);
  
    return this.each(function(){
      var form = $(this);
      
      for (var event in events) {
        form.bind('valerie.'+event, events[event]);
      }

      form.submit(function(e){
        if (form.find('input[type=file]')[0]) return true;
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
                form.trigger(
                  'valerie.formInvalidated',
                  [response.content, response.message, form]
                );
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
