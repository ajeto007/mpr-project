/**
 * Live Form Validation for Nette 2.2
 *
 * @author Radek Ježdík, MartyIX, David Grudl, pavelplzak, Robyer, Jakub Barta
 *
 * @changelog
 *   Robyer, 8.8.2014:
 *     - update with netteForms.js code from Nette 2.2
 *     - add showAllErrors, showMessageClassOnParent, errorMessagePrefix options
 *     - don't start validation when pressing one of special keys
 *     - set defaults to use with Bootstrap 3 and AdminLTE template
 *     - mark changes in netteForms.js code to simplify updating
 *     - fix showValid() function
 *   Robyer, 14.12.2013:
 *     - fix focus/blur circular repeating
 *     - fix adding handlers (so toggle() will work)
 *   Robyer, 13.12.2013:
 *     - based on fork of pavelplzak (add showErrorApart functionality)
 *     - update with netteForms.js code from Nette 2.1
 *     - add alert() to notify errors for elements with disabled live validation
 *     - add ability to disable on keyup/keydown validation
 *     - add missing ";", use tabs for indentation
 */

var LiveForm = {
	options: {
		showMessageClassOnParent: 'form-group',              // CSS class of control's parent where error/valid class should be added; or "false" to use control directly
		controlErrorClass: 'form-control-error',                      // CSS class for an invalid control
		errorMessageClass: 'form-error-message',                  // CSS class for an error message
		validMessageClass: 'form-valid-message',                    // CSS class for a valid message
		noLiveValidation:  'no-live-validation',             // CSS class for a valid message
		showErrorApartClass: 'show-error-apart',             // control with this CSS class will display message in element with ID = errorApartDivPrefix+control's id
		showErrorApartElementPrefix: 'error-container_',     // prefix for id of div where to display error message
		dontShowWhenValidClass: 'dont-show-when-valid',      // control with this CSS class will not show valid message
		messageTag: 'span',                                  // tag that will hold the error/valid message
		messageIdPostfix: '_message',                        // message element id = control id + this postfix
		errorMessagePrefix: '<i class="fa fa-warning"></i> ',// show this html before error message itself
		showAllErrors: true,                                 // show all errors when submitting form; or use "false" to show only first error
		showValid: false,                                    // show message when valid
		wait: false,                                         // delay in ms before validating on keyup/keydown; or use "false" to disable it
		bootstrap: true										 // should be rendered in Twitter Bootstrap style?
	},

	forms: { }
};

LiveForm.isSpecialKey = function(k) {
	// http://stackoverflow.com/questions/7770561/jquery-javascript-reject-control-keys-on-keydown-event
	return (k == 20 /* Caps lock */
		|| k == 16 /* Shift */
		|| k == 9 /* Tab */
		|| k == 27 /* Escape Key */
		|| k == 17 /* Control Key */
		|| k == 91 /* Windows Command Key */
		|| k == 19 /* Pause Break */
		|| k == 18 /* Alt Key */
		|| k == 93 /* Right Click Point Key */
		|| (k >= 35 && k <= 40) /* Home, End, Arrow Keys */
		|| k == 45 /* Insert Key */
		|| (k >= 33 && k <= 34) /*Page Down, Page Up */
		|| (k >= 112 && k <= 123) /* F1 - F12 */
		|| (k >= 144 && k <= 145)); /* Num Lock, Scroll Lock */
}

/**
 * Handlers for all the events that trigger validation
 * YOU CAN CHANGE these handlers (ie. to use jQuery events instead)
 */
LiveForm.setUpHandlers = function(el) {
	if (this.hasClass(el, this.options.noLiveValidation)) return;

	var handler = function(event) {
		event = event || window.event;
		Nette.validateControl(event.target ? event.target : event.srcElement);
	};

	var self = this;

	Nette.addEvent(el, "change", handler);
	Nette.addEvent(el, "blur", handler);
	Nette.addEvent(el, "keydown", function (event) {
		if (!self.isSpecialKey(event.which) && (self.options.wait === false || self.options.wait >= 200)) {
			// Hide validation span tag.
			self.removeClass(self.getGroupElement(this), self.options.controlErrorClass);
			self.removeClass(self.getGroupElement(this), self.options.validMessageClass);

			var error = self.getMessageElement(this);
			error.innerHTML = '';
			error.className = '';

			// Cancel timeout to run validation handler
			if (self.timeout) {
				clearTimeout(self.timeout);
			}
		}
	});
	Nette.addEvent(el, "keyup", function(event) {
		if (self.options.wait !== false) {
			event = event || window.event;
			if (event.keyCode !== 9) {
				if (self.timeout) clearTimeout(self.timeout);
					self.timeout = setTimeout(function() {
					handler(event);
				}, self.options.wait);
			}
		}
	});
};

LiveForm.addError = function(el, message) {
	this.forms[el.form.id].hasError = true;
	this.addClass(this.getGroupElement(el), this.options.controlErrorClass);

	if (this.options.bootstrap) {
            var div = $(el).closest("div");
            div.addClass("has-error has-feedback");
            $(el).closest("div").append("<span class=\"glyphicon glyphicon-remove form-control-feedback\"></span>");
	}

	if (!message) {
		message = '&nbsp;';
	} else {
		message = this.options.errorMessagePrefix + message;
	}
        var error = this.getMessageElement(el);

        if (this.options.bootstrap) {
            error.innerHTML = ("<span class=\"help-block error\" >"+message+"</span>");
        }
        else {
            error.innerHTML = message;
        }

};

LiveForm.removeError = function(el) {
	var groupEl = this.getGroupElement(el);

	this.removeClass(groupEl, this.options.controlErrorClass);
	var err_el = document.getElementById(el.id + this.options.messageIdPostfix);

	if (this.options.showValid && this.showValid(el)) {
		err_el = this.getMessageElement(el);
		this.addClass(groupEl, this.options.validMessageClass);
		return;
	}

	if (err_el) {
		err_el.parentNode.removeChild(err_el);
	}

	if (this.options.bootstrap && !$(el).is("input:hidden")) {
		var div = $(el).closest("div");
		div.removeClass("has-error has-feedback");
		div.find("span.glyphicon").remove();
		$(el).parent().find("span.error").remove();
	}
};

LiveForm.showValid = function(el) {
	if(el.type) {
		var type = el.type.toLowerCase();
		if(type == 'checkbox' || type == 'radio') {
			return false;
		}
	}

	var rules = Nette.parseJSON(el.getAttribute('data-nette-rules'));
	if (rules.length == 0) {
		return false;
	}

	if (this.hasClass(el, this.options.dontShowWhenValidClass)) {
		return false;
	}

	return true;
};

LiveForm.getGroupElement = function(el) {
	if (this.options.showMessageClassOnParent === false)
		return el;

	var groupEl = el;

	while (!this.hasClass(groupEl, this.options.showMessageClassOnParent)) {
		groupEl = groupEl.parentNode;

		if (groupEl === null) {
			return el;
		}
	}

	return groupEl;
}

LiveForm.getMessageElement = function(el) {
	var id = el.id + this.options.messageIdPostfix;
	var error = document.getElementById(id);

	if (!error) {
		error = document.createElement(this.options.messageTag);
		error.id = id;
		if(!this.hasClass(el, this.options.showErrorApartClass)) {
			el.parentNode.appendChild(error);
		} else {
			var showApartElement = document.getElementById(this.options.showErrorApartElementPrefix+el.id);
			showApartElement.appendChild(error);
		}
	}

	if ((el.style.display == 'none') && !($(el).is("select"))) {
		error.style.display = 'none';
	}

	error.className = this.options.errorMessageClass;
	error.innerHTML = '';

	return error;
};

LiveForm.addClass = function(el, className) {
	if (!el.className) {
		el.className = className;
	} else if (!this.hasClass(el, className)) {
		el.className += ' ' + className;
	}
};

LiveForm.hasClass = function(el, className) {
	if (el.className)
		return el.className.match(new RegExp('(\\s|^)' + className + '(\\s|$)'));
	return false;
};

LiveForm.removeClass = function(el, className) {
	if (this.hasClass(el, className)) {
		var reg = new RegExp('(\\s|^)'+ className + '(\\s|$)');
		var m = el.className.match(reg);
		el.className = el.className.replace(reg, (m[1] == ' ' && m[2] == ' ') ? ' ' : '');
	}
};