var default_mootools = {
    onInitialize: function(obj) {
        obj.message = new Element('div').inject(obj.form, 'top');
    },
    onBeforeSubmit: function(obj) {
        try {
            $(obj.form).getElement('.validator_error').destroy();
        } catch(e){}
        obj.message.set('text', '');
    },
    onFormInvalidate: function(response, message, obj) {
        $each(response, function(el){
            new Element('span', {'class':'validator_error', text: el.message}).inject(el.id, 'after');
        });
        obj.message.set('text', message);
    },
    onFormValidate: function(message, obj) {
        obj.message.set('text', message);
    },
    onFieldInvalidate: function(response, obj) {
        var next = $(response.id).getNext();
        if (next.hasClass('validator_error')) { next.destroy(); }
        new Element('span', {'class':'validator_error', text: response.message}).inject(response.id, 'after');
    },
    onFieldValidate: function(id, obj) {
        var next = $(id).getNext(); 
        if (next.hasClass('validator_error')) { next.destroy(); }
    }
}