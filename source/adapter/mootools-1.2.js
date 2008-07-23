//------------------------------------------------------------------------------
//	Valerie v0.5
//	(c) 2008 Chris Jaure
//	license: MIT License
//	website: http://www.chromasynthetic.com/
//
//	mootools-1.2.js, an adapter for Mootools 1.2
//------------------------------------------------------------------------------

var adapter = {

    init: function(obj){
        obj.extended = new new Class({
            Implements: [Options, Events]
        });
    },
    
    setOptions: function(obj, options, plugin){
        obj.options = $merge(obj.options, plugin ||
        {}, options);
        obj.extended.setOptions(obj.options);
    },
    
    getElement: function(el, selector){
        return $(el).getElement(selector);
    },
    
    addEvent: function(obj, type, fn){
        try {
            obj.extended.addEvent(type, fn);
        } 
        catch (e) {
            obj.addEvent(type, fn);
        }
    },
    
    fireEvent: function(obj, type, args){
        obj.extended.fireEvent(type, args);
    },
    
    ajax: function(options){
        return new Request.JSON(options);
    },
    
    sendAjax: function(ajax, form, string){
        var queryString = [];
        form.getElements('input, select, textarea').each(function(el){
            if (!el.name || el.disabled) return;
            var value = (el.tagName.toLowerCase() == 'select') ? Element.getSelected(el).map(function(opt){
                return opt.value;
            }) : ((el.type == 'radio' || el.type == 'checkbox') && !el.checked) ? null : el.value;
            $splat(value).each(function(val){
                if (typeof val != 'undefined') queryString.push(el.name + '=' + encodeURIComponent(val));
            });
        });
        queryString = queryString.join('&');
        
        ajax.options.data = queryString + string;
        ajax.post();
    }
}
