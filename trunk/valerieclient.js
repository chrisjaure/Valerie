//------------------------------------------------------------------------------
//	Valerie v0.4
//	(c) 2008 Chris Jaure
//	license: MIT License
//	website: http://www.chromasynthetic.com/
//
//	valerieclient.js
//------------------------------------------------------------------------------

/*
  Class: ValerieClient
  Used for validating form data.
*/
var ValerieClient = new Class({
    
    Implements: [Events, Options],
    
    /*
      Options:
        onInitialize - event to fire when instantiation is initialized
        onFormValidate - event to fire when form is validated
        onFormInvalidate - event to fire when form is invalidated
        onFieldValidate - event to fire when an individual field is validated
        onFieldInvalidate - event to fire when an individual field is invalidated
        onError - event to fire when an error occurs
        onBeforeSubmit - event to fire when form is submitted but before any validation occurs
        onSubmitted - event to fire when form has been submitted and results are returned
        validateOnKeyUp - validate while user types
        ERROR - the only message not in the PHP localization file
    */
    
    options: {
      onInitialize: function (obj) {
        obj.fieldMessage = new Hash(); 
        obj.form.getElements('input[type=text], input[type=password], textarea').each(function(el){
          var coordinates = el.getCoordinates();
          var newEl = new Element('div', {
            'class': 'error',
            'styles': {
              'left': coordinates.right + 10,
              'top': coordinates.top,
              'opacity': 0
            }
          }).inject(el, 'after');
          obj.fieldMessage.set(el.get('id'), newEl);
        });
      }, 
      onFormValidate: function (message, obj) {
        obj.message.set('class', 'message').set('text', message);
        window.scrollTo(0, obj.message.getPosition().y);
      },
      onFormInvalidate: function (errors, obj) {
        var position = $(errors[0].id).getPrevious().getPosition();
        window.scrollTo(0, position.y);
        errors.each(function(error) {
          obj.fieldMessage.get(error.id).set('text', error.message).fade('in');
        });
      },
      onFieldInvalidate: function (error, obj) {
        obj.fieldMessage.get(error.id).set('text', error.message).fade('in');
      },
      onFieldValidate: function (id, obj) {
        obj.fieldMessage.get(id).fade('out');
      },
      onError: function (obj) {
        obj.message.set('class', 'error').set('text', obj.options.ERROR);
      },
      onSubmitted: function (obj) {
        obj.message.empty();
        obj.fieldMessage.each(function(el){
          el.fade('hide');
        });
      },
      validateField: false,
      ERROR: "An error has occurred."
    },
    
    /*
      Constructor: initialize
      
      Initializes the validator class.
      
      Parameters:
        form - form id or object
        options - object that contains options
        
      Returns: Validator instance
    */
    initialize: function (form, options) {
      this.setOptions(options);
      this.form = $(form);
      this.message = new Element('p', {'class':'error'}).inject(form, 'before');
      this.submitBtn = this.form.getElement('input[type=submit]');
      this.submitBtn.orgVal = this.submitBtn.value;
      this.typing = false;
      this.req = new Request({
        url: this.form.get('action'),
        onRequest: this.onBeforeSubmit.bind(this),
        onComplete: this.onSubmitted.bind(this),
        onSuccess: function (text) {
          var response = JSON.decode.attempt(text);
          if (response) {
            if (response.type.toInt() > 1) {
              this.onFormInvalidate(response);
            } else {
              this.onFormValidate(response);
            }
          } else this.onError();
        }.bind(this),
        onFailure: this.onError.bind(this)
      });
      
      this.periodical = new Request({
        url: this.form.get('action'),
        onSuccess: function (text) {
          var response = JSON.decode.attempt(text);
          if (response) {
            if (response.type.toInt() > 1) {
              this.onFieldInvalidate(response);
            } else {
              this.onFieldValidate(response);
            }
          }
        }.bind(this)
      });
      
      this.form.addEvent('submit', function(){
        this.req.send(this.form.toQueryString() + '&_ajax=1');
        return false;
      }.bind(this));
      
      if (this.options.validateOnKeyUp) {
        this.form.addEvent('keyup', function(event) {
          var target = $(event.target);
          var type = target.get('type');
          if ((type == 'text' || type == 'password' || target.get('tag') == 'textarea') && event.code != 9) {
            this.typing = $time();
            (function(){
              if ($time() - this.typing >= 750) {
                this.periodical.send(this.form.toQueryString() + '&_periodical=' + target.get('id'));
              }
            }).delay(800, this);
          }
        }.bind(this));
      }
      
      this.fireEvent('onInitialize', this);
    },
    
    onFormValidate: function (data) {
      this.form.reset();
      return this.fireEvent('onFormValidate', [data.content.message, this]);
    },
    
    onFormInvalidate: function (data) {
      return this.fireEvent('onFormInvalidate', [data.content, this]);
    },
    
    onFieldValidate: function (data) {
      return this.fireEvent('onFieldValidate', [data.content.id, this]);
    },
    
    onFieldInvalidate: function (data) {
      return this.fireEvent('onFieldInvalidate', [data.content, this]);
    },
    
    onError: function () {
      return this.fireEvent('onError', this);
    },
    
    onBeforeSubmit: function () {
      this.submitBtn.set({value: 'Loading...', disabled: true});
      return this.fireEvent('onBeforeSubmit', this);
    },
    
    onSubmitted: function () {
      this.submitBtn.set({value: this.submitBtn.orgVal, disabled: false});
      return this.fireEvent('onSubmitted', this);
    }
    
});
