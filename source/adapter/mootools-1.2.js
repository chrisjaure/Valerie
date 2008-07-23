//------------------------------------------------------------------------------
//	Valerie v0.5
//	(c) 2008 Chris Jaure
//	license: MIT License
//	website: http://www.chromasynthetic.com/
//
//	mootools-1.2.js, an adapter for Mootools 1.2
//------------------------------------------------------------------------------

var adapter = {
    
    init: function(obj) {
        obj.extended = new new Class({Implements: [Options, Events]});
    },
    
    setOptions: function(obj, options) {
        obj.options = $merge(obj.options, options);
        obj.extended.setOptions(obj.options);
    },
    
    getElement: function(el, selector) {
        return $(el).getElement(selector);
    },
    
    addEvent: function(obj, type, fn) {
        try {
            obj.extended.addEvent(type, fn);
        } catch(e){
            obj.addEvent(type, fn);
        }
    },
    
    fireEvent: function(obj, type, args) {
        obj.extended.fireEvent(type, args);
    },
    
    ajax: function(options) {
        return new Request.JSON(options);
    },
    
    sendAjax: function(ajax, form, string) {
        ajax.options.data = $(form).toQueryString() + string; 
        ajax.post();
    }
}