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
		initSweetAlert(id);
		// App.Shared.modalConfirm(_this.options.confirmModal, function () {
		// 	processing = true;
		// 	_this.$elems.confirmModal.find('[data-action]').button('loading');
		// 	$.ajax({
		// 		type: 'POST',
		// 		url: App.generate('user.reset_password', {id: id}),
		// 		dataType: 'json',
		// 		tryCount: 0,
		// 		maxTry: 10,
		// 		success: function (response) {
		// 			processing = false;
		// 			var message = response.data.message;
		// 			$.notify({
		// 				'message': message,
		// 				'icon': 'glyphicon glyphicon-ok',
		// 			}, {
		// 				type: 'success',
		// 				offset: {y: 65, x: 20},
		// 			})
		// 			_this.$elems.confirmModal.find('[data-action]').button('reset');
		// 			confirmModal('hide');
		// 		},
		// 		error: function (err) {
		// 			this.tryCount++;
		// 			if (this.tryCount < this.maxTry) {
		// 				$.ajax(this);
		// 			} else {
		// 				_this.$elems.confirmModal.find('[data-action]').button('reset');
		// 				processing = false;
		// 				confirmModal('hide');
		// 			}
		// 		}
		// 	});
		// }, function () {
		// 	processing = false;
		// 	confirmModal('hide');
		// });
	}

	function initSweetAlert (id) {
		const swalWithBootstrapButtons = Swal.mixin({
			customClass: {
				confirmButton: 'btn btn-success',
				cancelButton: 'btn btn-danger'
			},
			buttonsStyling: false
		})
		
		swalWithBootstrapButtons.fire({
			title: 'Are you sure?',
			text: "You won't be able to revert this!",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Yes, delete it!',
			cancelButtonText: 'No, cancel!',
			reverseButtons: true
		}).then((result) => {
			console.info('ok confirmed');
			if (result.isConfirmed) {
				console.info('ok confirmed 11111111111');
				$.ajax({
					type: 'POST',
					url: Routing.generate('user.reset_password', {id, id}),
					dataType: 'json',
					success: function (response) {
						// processing = false;
						// var message = response.data.message;
						// $.notify({
						// 	'message': message,
						// 	'icon': 'glyphicon glyphicon-ok',
						// }, {
						// 	type: 'success',
						// 	offset: {y: 65, x: 20},
						// })
						// _this.$elems.confirmModal.find('[data-action]').button('reset');
						// confirmModal('hide');
						alert('password resetted', response);
					},
					error: function (err) {
						// this.tryCount++;
						// if (this.tryCount < this.maxTry) {
						// 	$.ajax(this);
						// } else {
						// 	_this.$elems.confirmModal.find('[data-action]').button('reset');
						// 	processing = false;
						// 	confirmModal('hide');
						// }
						alert('resetted failed');
					}
				});
			} else {
				
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