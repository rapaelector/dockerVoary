/*
* @Author: Stephan <srandriamahenina@bocasay.com>
* @Date:   2018-02-08 14:05:22
* @Last Modified by:   stephan
* @Last Modified time: 2018-07-11 15:23:54
*/

/**
 * Reset user password using an Ajax Post request
 * Required options
 * - triggerBtn : a jquery selecto
 * - confirmModal : a jQuery selector for the confirm modal
 */
import $ from 'jquery';
import Swal from 'sweetalert2/dist/sweetalert2.min.js'
import Routing from './app-routing';

function ResetPassword () {
	var _this = this;

	var processing = false;
	_this.defaults = {
		triggerBtn : '[data-action="reset-password"][data-id]',
		confirmModal : '#confirm-password-reset-modal',
	};

	function initElems() {
		_this.$elems = {
			triggerBtn: $(_this.options.triggerBtn),
			confirmModal: $(_this.options.confirmModal),
		};
	}

	function bindElems() {
		$('body').on('click', '[data-action="reset-password"]', onTriggerClick);
		// _this.$elems.triggerBtn.click(onTriggerClick);
		_this.$elems.confirmModal.on('hide.bs.modal', onConfirmModalHide);
	}

	function confirmModal(action) {
		_this.$elems.confirmModal.modal(action);
	}

	function onConfirmModalHide(e) {
		if (processing) {
			return false;
		}
	}

	function onTriggerClick(e) {
		var id = $(this).data('id');
		console.info(id);
		initConfirmAlert(id);
	}

	function initConfirmAlert (id) {
		Swal.fire({
			title: 'Réinitialisation mot de passe',
			html: `<div>Souhaitez-vous réinitialiser le mot de passe de cet utilisateur</div>`,
			input: false,
			inputAttributes: {
				autocapitalize: 'off'
			},
			showCancelButton: true,
			cancelButtonText: 'Annuler',
			confirmButtonText: 'Ok',
			showLoaderOnConfirm: true,
			preConfirm: () => {
				return new Promise(function (resolve, reject) {
					$.ajax({
						type: 'POST',
						url: Routing.generate('user.reset_password', {id, id}),
						dataType: 'json',
					}).done(function (data) {
						console.info(data);
						resolve(data);
					}).fail(function (jqxhr, status, error) {
						reject(jqxhr.responseJSON.error);
					})
				}).catch(function (error) {
					Swal.showValidationMessage(error);
				})
			},
			allowOutsideClick: () => !Swal.isLoading()
		}).then((result) => {
			if (result.isConfirmed) {
				Swal.fire({
					html: `<div>Mot de passe resetter</div>`,
				})
			}
		})
	}

	_this.init = function (options) {
		_this = this;
		_this.options = $.extend(true, {}, _this.defaults, options);
		initElems();
		bindElems();
	}

	return _this;
};

export {
	ResetPassword
}