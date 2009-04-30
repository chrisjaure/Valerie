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
  - valerie.formInvalidated (receives event, object of fields, response message,
    form, and response)
  - valerie.formValidated (receives event, response message, form, and response)
  - valerie.error (receives event and form)
  
  The response for `formInvalidated` and `formValidated` can contain data set
  by ValerieServer.
  
  The object of fields is structured like this:
  
      {
        "field_name": {
          "id": "field_id",
          "name": "field_name",
          "message": "Error message if invalid."
        },
        ...
      }
  
  Global defaults can also be set:
  
      $.fn.valerie.events = {
        ititialized: function(event, form){
          alert("Valerie has been initialized");
        },
      }
      
  Events can also be attached to the form itself. Since all the events are
  prefixed with "valerie.", they can all be removed with a single call
  to `unbind`.
  
      // remove all events
      $('#frm_id').unbind('valerie');
      
      // attach new events
      $('#frm_id')
        .bind('valerie.onValidate', function(e, message, form, response){ 
          //do stuff 
        })
        .bind('valerie.onInvalidate', function(e, message, form, response){
          //do stuff
        });
        
  It's not necessary to remove all events before adding your own if you want
  to add to the existing events set by the plugin in use.
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
            if (response.form) {
              if (response.form.message_type == 'invalid') {
                form.trigger(
                  'valerie.formInvalidated',
                  [response.form.elements, response.form.message, form, response]
                );
              }
              else {
                form.trigger(
                  'valerie.formValidated',
                  [response.form.message, form, response]
                );
                if (response['goto']) {
                  window.location = response['goto'];
                }
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
