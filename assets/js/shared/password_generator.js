/*
* @Author: Stephan <srandriamahenina@gmail.com>
* @Date:   2017-07-07 13:29:33
* @Last Modified by:   stephan
* @Last Modified time: 2018-09-18 14:52:55
*/
import $ from 'jquery';

function PasswordGenerator () {
	var _this = this;
	
	_this.defaults = {
		btn_txt : "Générer le mot de passe"
	};

	_this.initElements = function () {
		_this.$elems = {};
		_this.$elems.generateBtn = $('<button class="btn btn-default btn-sm" type="button"></button>');
		var generateBtn = _this.$elems.generateBtn;
		generateBtn.html('<i class="fa fa-key"></i> ' + _this.param.btn_txt);


		_this.$elems.container = $(_this.param.container);
		_this.$elems.container.append(_this.$elems.generateBtn);

		console.info($(_this.param.target).length);
		_this.$elems.target = $(_this.param.target);
	}

	_this.bindElements = function () {
		_this.$elems.generateBtn.on('click', handlePasswordGeneration);
	}

	function handlePasswordGeneration() {
		var password = generatePassword();
		console.info(_this.$elems);
		console.info(_this);

		_this.$elems.target.val(password);
	}

	function generatePassword() {
		return Math.random().toString(36).slice(-8);
	}

	_this.init = function (param) {
		_this.param = $.extend(true, _this.defaults, param);
		_this.initElements();
		_this.bindElements();
	}
}

export {
	PasswordGenerator
}