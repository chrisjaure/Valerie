var default_jquery = {
    onInitialize: function(event, obj) {
        obj.message = jQuery('<div></div>').prependTo(obj.form);
    },
    onBeforeSubmit: function(event, obj) {
        jQuery(obj.form).find('.validator_error').remove();
        obj.message.text('');
    },
    onFormInvalidate: function(event, response, message, obj) {
        jQuery.each(response, function(){
            jQuery('#' + this.id).after('<span class="validator_error">' + this.message + '</span>');
        });
        obj.message.text(message);
    },
    onFormValidate: function(event, message, obj) {
        obj.message.text(message);
    },
    onFieldInvalidate: function(event, response, obj) {
        jQuery('#' + response.id).next('.validator_error').remove().end()
         .after('<span class="validator_error">' + response.message + '</span>');
    },
    onFieldValidate: function(event, id, obj) {
        jQuery('#' + id).next('.validator_error').remove();
    }
}