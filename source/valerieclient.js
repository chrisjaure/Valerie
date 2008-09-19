//------------------------------------------------------------------------------
//	Valerie v0.5
//	(c) 2008 Chris Jaure
//	license: MIT License
//	website: http://www.chromasynthetic.com/
//
//	valerieclient.js
//------------------------------------------------------------------------------

var ValerieClient = function(){
    adapter.init(this);
    this.options = {
        onInitialize: function(){},
        onFormValidate: function(){},
        onFormInvalidate: function(){},
        onFieldValidate: function(){},
        onFieldInvalidate: function(){},
        onError: function(){},
        onBeforeSubmit: function(){},
        onSubmitted: function(){},
        validateField: false,
        ERROR: "An error has occurred.",
        LOADING: "Loading..."
    };
    this.initialize(arguments[0], arguments[1], arguments[2]);
}
ValerieClient.prototype = {
    
    initialize: function(form, options, plugin){
        var self = this;
        adapter.setOptions(this, options, plugin);
        this.form = document.getElementById(form);
        this.submitBtn = adapter.getElement(this.form, 'input[type=submit]');
        this.submitBtn.orgVal = this.submitBtn.value;
        this.typing = {
            time: null,
            sent: false
        };
        this.req = adapter.ajax({
            url: this.form.action,
            onRequest: function(){
                self.beforeSubmit();
            },
            onComplete: function(){
                self.submitted();
            },
            onSuccess: function(response){
                if (response) {
                    if (parseInt(response.type) > 1) {
                        self.formInvalidate(response);
                    }
                    else {
                        self.formValidate(response);
                    }
                }
                else {
                    self.error();
                }
            },
            onFailure: function(){
                self.error();
            }
        });
        
        this.periodical = adapter.ajax({
            url: this.form.action,
            onSuccess: function(response){
                if (response) {
                    if (parseInt(response.type) > 1) {
                        self.fieldInvalidate(response);
                    }
                    else {
                        self.fieldValidate(response);
                    }
                }
            },
            onComplete: function() {
                self.typing.sent = false;
            }
        });
        
        adapter.addEvent(this.form, 'submit', function(){
            adapter.sendAjax(self.req, self.form, '&_ajax=1');
            return false;
        });
        
        if (this.options.validateField) {
            adapter.addEvent(this.form, 'keyup', function(event){
                var target = event.target, type = target.type, key = event.code || event.which;
                if ((type == 'text' || type == 'password' || target.tagName.toLowerCase() == 'textarea') && key != 9) {
                    self.typing.time = new Date().getTime();
                    setTimeout(function(){
                        var time = new Date().getTime() - self.typing.time;
                        if (time >= 790 && !self.typing.sent) {
                            self.typing.sent = true;
                            adapter.sendAjax(self.periodical, self.form, '&_periodical=' + target.id);
                        }
                    }, 800);
                }
            });
        }
        
        adapter.fireEvent(this, 'onInitialize', this);
    },
    
    formValidate: function(data){
        this.form.reset();
        return adapter.fireEvent(this, 'onFormValidate', [data.content.message, this]);
    },
    
    formInvalidate: function(data){
        return adapter.fireEvent(this, 'onFormInvalidate', [data.content, data.message, this]);
    },
    
    fieldValidate: function(data){
        return adapter.fireEvent(this, 'onFieldValidate', [data.content.id, this]);
    },
    
    fieldInvalidate: function(data){
        return adapter.fireEvent(this, 'onFieldInvalidate', [data.content, this]);
    },
    
    error: function(){
        return adapter.fireEvent(this, 'onError', this);
    },
    
    beforeSubmit: function(){
        this.submitBtn.value = this.options.LOADING;
        this.submitBtn.disabled = true;
        return adapter.fireEvent(this, 'onBeforeSubmit', this);
    },
    
    submitted: function(){
        this.submitBtn.value = this.submitBtn.orgVal;
        this.submitBtn.disabled = false;
        return adapter.fireEvent(this, 'onSubmitted', this);
    }
    
}