var fancy_mootools = {
    onInitialize: function(obj) {
        obj.message = new Element('div', {
            'class': 'validator_message'
        }).inject(obj.form, 'top');
        obj.error_message = new Element('div', {
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
            var coords = $(el.id).getCoordinates();
            obj.error_message.clone().inject(el.id, 'after').setStyles({
                top: coords.top - 2,
                left: coords.right + 10,
                opacity:0
            }).fade('in').getElement('.content').set('text', el.message);
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
        var coords = $(response.id).getCoordinates();
        obj.error_message.clone().inject(response.id, 'after').setStyles({
            top: coords.top - 4,
            left: coords.right + 10,
            opacity:0
        }).fade('in').getElement('.content').set('text', response.message);
    },
    onFieldValidate: function(id, obj) {
        var next = $(id).getNext(); 
        if (next.hasClass('validator_error')) { next.destroy(); }
    }
}