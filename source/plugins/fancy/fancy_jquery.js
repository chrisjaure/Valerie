var fancy_jquery = {
    onInitialize: function(event, obj) {
        obj.message = jQuery('<div class="validator_message"></div>').prependTo(obj.form);
        obj.error_message = jQuery('<div class="validator_error"><p class="content"></p><div class="bottom"></div></div>');
    },
    onBeforeSubmit: function(event, obj) {
        jQuery(obj.form).find('.validator_error').remove();
        obj.message.text('');
    },
    onFormInvalidate: function(event, response, message, obj) {
        obj.message.text(message);
        jQuery.each(response, function(){
            var offset = jQuery('#' + this.id).offset(), width = jQuery('#' + this.id).width();
            obj.error_message.clone().find('.content').text(this.message).end().css({
                top: offset.top - 2,
                left: offset.left + width + 20,
                display: 'none'
            }).insertAfter('#' + this.id).fadeIn();
        });
        var scroll = jQuery(obj.form).offset();
        window.scrollTo(scroll.left, scroll.top);
    },
    onFormValidate: function(event, message, obj) {
        obj.message.text(message);
    },
    onFieldInvalidate: function(event, response, obj) {
        jQuery('#' + response.id).next('.validator_error').remove();
        var offset = jQuery('#' + response.id).offset(), width = jQuery('#' + response.id).width();
        obj.error_message.clone().find('.content').text(response.message).end().css({
            top: offset.top - 2,
            left: offset.left + width + 20,
            display: 'none'
        }).insertAfter('#' + response.id).fadeIn();
    },
    onFieldValidate: function(event, id, obj) {
        jQuery('#' + id).next('.validator_error').remove();
    }
}