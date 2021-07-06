import Swal from 'sweetalert2/dist/sweetalert2.min.js'
import 'inputmask/dist/jquery.inputmask';

window.initProjectCaseList = function({
    /**
     * @var String
     */
    containerSelector,

    /**
     * @var String
     */
    filtersContainerSelector,

    /**
     * @var Int
     */
    columnsCount,

    /**
     * @var Object
     */
    settings,

    /**
     * completion input selector
     * 
     * @var String
     */
    completionInputSelector = '.completion-input',
}) {
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

    var dtPromise = window.App.DataTable.initAppDataTables({
        columnNames: [
            'current_case_folder_name_on_the_server',
            'project_business_charge',
            'project_market_type',
            'project_postal_code',
            'project_site_address',
            'project_global_amount',
            'project_completion',
            'project_last_relaunch',
            'project_work_schedule',
            'project_pc_deposit',
            'project_architect',
            'project_1090',
            'project_contact_name',
            'actions'
        ],
        excludedColumns: [13],
        containerSelector: containerSelector,
        filtersContainerSelector: filtersContainerSelector,
        columnsCount: columnsCount,
        settings: settings,
        debug: false,
        enableFormatter: true,
        redrawEvent: 'user.deleted',
        config: {
            "onAjaxSuccess": function (data) {
                var formattedTotal = window.App.Utils.numberFormat(data.extras.total, 0, ',', ' ');

                $('#total').text(formattedTotal);
            }
        }
    });
    dtPromise.then(function (dtInstance) {
        const tableId = dtInstance.table().node().id;
        var $table = $('#' + tableId);
        let modal = $('#businessChargeModal');
        let modalEconomist = $('#economistModal');
        let idProject = null;
        let idPreset = null;
        $table.on('draw.dt', function () {
            var $completionInputSelector = $(completionInputSelector);

            $completionInputSelector.inputmask('remove');
            $completionInputSelector.inputmask({
                alias: 'percentage',
                max: 100,
                digits: 2,
                digitsOptional: false,
            });

            $('body').on('change', '.completion-input', function () {
                var inputValue = parseFloat($(this).val());
                var dataValue = $(this).data('value');
                var id = $(this).data('id');
                var $completionLoader = $(this).siblings('.spinner-border.completion-loader');

                if (!isNaN(inputValue) && (dataValue != inputValue)) {
                    $(this).data('value', inputValue);
                    updateFieldRequest({
                        spinnerElem: $completionLoader,
                        field: 'completion',
                        data: {completionValue: inputValue, id: id},
                        dtInstance: dtInstance,
                    })
                } else {
                    $(this).val(dataValue);
                }
            });

            $('body').on('click', '.business-charge-change', (e) => {
                e.preventDefault()
                var values = $(e.target); // Button that triggered the modal
                idProject = values.data('id');
                idPreset = values.data('id-business-charge');
                modal.modal('show');
                $('form.form-change-business-charge').attr('data-id', idProject)
                $("#project_business_charge_businessCharge").val(idPreset).trigger('change')
            })

            $('body').on('click', '.economist-change', (e) => {
                e.preventDefault()
                var values = $(e.target); // Button that triggered the modal
                idProject = values.data('id');
                idPreset = values.data('id-economist');
                modalEconomist.modal('show');
                $('form.form-economist').attr('data-id', idProject)
                $("#economist_form_economist").val(idPreset).trigger('change')
            })

            $('body').on('change', '.pc-deposit-select', function () {
                var id = $(this).data('id');
                var value = parseInt($(this).val());
                var $pcDeposeLoader = $(this).siblings('.spinner-border.pc-deposit-loader');
                var currentValue = $(this).data('value');

                if (!isNaN(value) && (value == 1 || value == 0) && (currentValue != value)) {
                    $(this).data('value', value);
                    updateFieldRequest({
                        spinnerElem: $pcDeposeLoader,
                        field: 'pc_deposit',
                        data: {pcDepositValue: value, id: id},
                        dtInstance: dtInstance,
                    })
                }
            })

            $('body').on('change', '.architect-select', function () {
                var id = $(this).data('id');
                var value = parseInt($(this).val());
                var $architectLoader = $(this).siblings('.spinner-border.architect-loader');
                var currentValue = $(this).data('value');

                if (!isNaN(value) && (value == 1 || value == 0) && (currentValue != value)) {
                // if (!isNaN(value) && (value == 1 || value == 0)) {
                    $(this).data('value', value);
                    updateFieldRequest({
                        spinnerElem: $architectLoader,
                        field: 'architect',
                        data: {architectValue: value, id: id},
                        dtInstance: dtInstance,
                    })
                }
            })

            $('body').on('click', '.save-project-comment', function () {
                var $commentField = $(this).siblings('.form-control.comment-field');
                var $commentLoader = $(this).siblings('.spinner-border.comment-loader');
                var id = $commentField.data('id');
                var value = $.trim($commentField.val());
                var currentValue = $.trim($commentField.data('value'));

                if (currentValue != value) {
                    updateFieldRequest({
                        spinnerElem: $commentLoader,
                        field: 'comment',
                        data: {commentValue: value, id: id},
                        dtInstance: dtInstance,
                    })
                }
            })
        })
        //
        // // then save the business charge
        $('body').on('click', '.business-charge-change-save', (e) => {
            let form = $('form.form-change-business-charge');
            let values = form.serialize();
            // then made some ajax there
            $.ajax({
                url: Routing.generate('project.edit.business.charge', {id: idProject}),
                method: "POST",
                accepts: "application/json; charset=utf-8",
                data: values,
                success: (data) => {
                    modal.modal('hide');
                    dtInstance.draw();
                    Toast.fire({
                        icon: data.type,
                        title: data.message,
                    });
                    $("#project_business_charge_businessCharge").val(null).trigger('change')
                },
                error: function (error) {
                    Toast.fire({
                        icon: error.type,
                        title: error.message,
                    });
                    $("#project_business_charge_businessCharge").val(null).trigger('change')
                },
            });
            return false;
        })
        $('body').on('click', '.business-economist-save', (e) => {
            let form = $('form.form-change-economist');
            let values = form.serialize();
            // then made some ajax there
            $.ajax({
                url: Routing.generate('project.edit.economist', {id: idProject}),
                method: "POST",
                accepts: "application/json; charset=utf-8",
                data: values,
                success: (data) => {
                    modalEconomist.modal('hide');
                    dtInstance.draw();
                    Toast.fire({
                        icon: data.type,
                        title: data.message,
                    });
                    $("#economist_form_economist").val(null).trigger('change')
                },
                error: function (error) {
                    Toast.fire({
                        icon: error.type,
                        title: error.message,
                    });
                    $("#economist_form_economist").val(null).trigger('change')
                },
            });
            return false;
        })
    });

    function updateFieldRequest({
        /**
         * spinner element
         *
         * @var String
         */
        spinnerElem,

        /**
         * field to update
         *
         * @var String
         */
        field,

        /**
         * data to send
         *
         * @var Object
         */
        data,

        /**
         * dtInstance
         *
         * @var Object
         */
        dtInstance,
    })
    {
        var $spinner = spinnerElem;
        $spinner.removeClass('d-none');

        $.ajax({
            type: 'POST',
            url: Routing.generate('project.case.update_project_field', {field: field, id: data.id}),
            data: data,
            dataType: "json",
            success: function (data) {
                $spinner.addClass('d-none');
                dtInstance.draw();
                Toast.fire({
                    icon: data.type,
                    title: data.message,
                });
                $('.modal-backdrop').remove();
                if ($('body').hasClass('modal-open')) {
                    $('body').removeClass('modal-open');
                }
            },
            error: function (error) {
                $spinner.addClass('d-none');
                Toast.fire({
                    icon: error.type,
                    title: error.message,
                });
                $('.modal-backdrop').remove();
            },
        })
    }
};