var fancy_jquery = (function(){
    
    function insertError(id, message, obj) {
        var el = jQuery('#' + id), offset = el.offset(), width = el.width();
        obj.error_message.clone().find('.content').text(message).end().css({
            top: offset.top - 2,
            left: offset.left + width + 20,
            display: 'none'
        }).insertAfter(el).fadeIn();
    }

    return {
        onInitialize: function(event, obj){
            obj.message = jQuery('<div class="validator_message"></div>').prependTo(obj.form);
            obj.error_message = jQuery('<div class="validator_error"><p class="content"></p><div class="bottom"></div></div>');
        },
        onBeforeSubmit: function(event, obj){
            jQuery(obj.form).find('.validator_error').remove();
            obj.message.text('');
        },
        onFormInvalidate: function(event, response, message, obj){
            obj.message.text(message);
            jQuery.each(response, function(){
                insertError(this.id, this.message, obj);
            });
            var scroll = jQuery(obj.form).offset();
            window.scrollTo(scroll.left, scroll.top);
        },
        onFormValidate: function(event, message, obj){
            obj.message.text(message);
        },
        onFieldInvalidate: function(event, response, obj){
            jQuery('#' + response.id).next('.validator_error').remove();
            insertError(response.id, response.message, obj);
        },
        onFieldValidate: function(event, id, obj){
            jQuery('#' + id).next('.validator_error').remove();
        }
    }
})();
