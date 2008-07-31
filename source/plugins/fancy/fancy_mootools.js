var fancy_mootools = (function(){
    function insertError(id, message, obj) {
        var coords = $(id).getCoordinates();
        obj.error_message.clone().inject(id, 'after').setStyles({
            top: coords.top - 2,
            left: coords.right + 10,
            opacity:0
        }).fade('in').getElement('.content').set('text', message);
    }
    
    return {
        onInitialize: function(obj) {
            obj.message = new Element('div', {
                'class': 'validator_message'
            }).inject(obj.form, 'top');
            obj.error_message = new Element('span', {
                'class': 'validator_error',
                html: '<p class="content"></p><div class="bottom"></div>'
            }); 
        },
        onBeforeSubmit: function(obj) {
            try {
                $(obj.form).getElements('.validator_error').destroy();
            } catch(e){}
            obj.message.set('text', '');
        },
        onFormInvalidate: function(response, message, obj) {
            obj.message.set('text', message);
            $each(response, function(el){
                insertError(el.id, el.message, obj);
            });
            var scroll = $(obj.form).getPosition();
            window.scrollTo(scroll.x, scroll.y);
        },
        onFormValidate: function(message, obj) {
            obj.message.set('text', message);
        },
        onFieldInvalidate: function(response, obj) {
            var next = $(response.id).getNext();
            if (next.hasClass('validator_error')) { next.destroy(); }
            insertError(response.id, response.message, obj);
        },
        onFieldValidate: function(id, obj) {
            var next = $(id).getNext(); 
            if (next.hasClass('validator_error')) { next.destroy(); }
        }
    }
})();
