//------------------------------------------------------------------------------
//	Valerie v0.5
//	(c) 2008 Chris Jaure
//	license: MIT License
//	website: http://www.chromasynthetic.com/
//
//	mootools-1.2.js, an adapter for jQuery 1.2.6
//------------------------------------------------------------------------------

var adapter = {
    
    init: function(obj) {
        obj.extended = jQuery('<div></div>');
    },
    
    setOptions: function(obj, options) {
        jQuery.extend(obj.options, options);
        jQuery.each(obj.options, function(name){
            if (typeof this == 'function') {
                obj.extended.bind(name, this);
            }
        });
    },
    
    getElement: function(el, selector) {
        return jQuery(el).find(selector).get(0);
    },
    
    addEvent: function(obj, type, fn) {
        try {
            jQuery(obj).bind(type, fn);
        } catch(e){
            obj.extended.bind(type, fn);
        }
    },
    
    fireEvent: function(obj, type, args) {
        obj.extended.trigger(type, args);
    },
    
    ajax: function(options) {
        return jQuery.extend(options, {
            success: options.onSuccess,
            error: options.onFailure,
            complete: options.onComplete,
            beforeSend: options.onRequest,
            dataType: 'json',
            type: 'post'
        });
    },
    
    sendAjax: function(ajax, form, string) {
        jQuery.ajax(jQuery.extend(ajax, {data: jQuery(form).serialize() + string}));
    }
}