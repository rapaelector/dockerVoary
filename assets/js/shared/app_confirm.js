/*
* @Author: stephan <m6ahenina@gmail.com>
* @Date:   2020-01-28 10:40:36
* @Last Modified by:   stephan <m6ahenina@gmail.com>
* @Last Modified time: 2020-02-07 07:19:59
*/

import $ from 'jquery';
import Swal from 'sweetalert2/dist/sweetalert2.min.js'

export function initFormConfirmation(options) {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 5000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    })

    $('body').on('submit', 'form[data-confirm]', function (e) {
        var $form = $(this);

        if ($form.attr('aria-confirmed') == "true") {
            if ($form.data('ajax') && $form.attr('action')) {
                $('#app-loader').show();
                $.ajax({
                    type: $form.attr('method'),
                    url: $form.attr('action'),
                    data: $form.serialize(),
                    dataType: 'json',
                    success: function (response) {
                        $('#app-loader').hide();
                        Toast.fire({
                            icon: 'success',
                            title: response.message,
                        });
                        if ($form.data('success-event')) {
                            $('body').trigger($form.data('success-event'));
                        }
                    },
                    error: function (err) {
                        console.warn(err);
                        $('#app-loader').hide();
                    }
                });

                return false;
            }

            return true;
        }

        var swalOptions = {
            title: $form.data('confirm-title') ? $form.data('confirm-title') : 'Confirmation',
            showCancelButton: true,
            confirmButtonText: $form.data('confirm-button-text') ? $form.data('confirm-button-text') : 'Confirmer',
            cancelButtonText: $form.data('confirm-cancel-text') ? $form.data('confirm-cancel-text') : 'Annuler',
            showLoaderOnConfirm: true,
            allowOutsideClick: function () {
                return !Swal.isLoading()
            },
        };
        if ($form.data('confirm-icon')) {
            swalOptions['icon'] = $form.data('confirm-icon');
        }
        if ($form.data('confirm-icon-html')) {
            swalOptions['iconHtml'] = $form.data('confirm-icon-html');
        }
        if ($form.data('confirm-html')) {
            swalOptions['html'] = $form.data('confirm-html');
        }
        if ($form.data('confirm-text')) {
            swalOptions['text'] = $form.data('confirm-text');
        }
        var confirmInput = $form.data('confirm-input');
        if (confirmInput && (confirmInput.length > 0)) {
            swalOptions['input'] = 'text';
            swalOptions['inputValidator'] = function (val) {
                if (val !== $form.data('confirm-input')) {
                    return 'Valeur invalide';
                }
            };
            swalOptions['onOpen'] = function () {
                Swal.disableButtons();
                $(Swal.getInput()).keyup(function () {
                    var val = $(this).val();
                    if (val !== confirmInput) {
                        Swal.disableButtons();
                    } else {
                        Swal.enableButtons();
                    }
                });
            };
            if ($form.data('confirm-input-attr')) {
                swalOptions['inputAttributes'] = $form.data('confirm-input-attr');
            }
        }
        Swal.fire(swalOptions).then((result) => {
            if (result.value) {
                $form.attr('aria-confirmed', true);
                $form.submit();
            }
        })

        return false;
    });
}