import Swal from 'sweetalert2/dist/sweetalert2.min.js'
import 'inputmask/dist/jquery.inputmask';

window.initProjectList = function({
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
            'actions',
            'current_case_folder_name_on_the_server',
            'business_charge',
            'market_type',
            'project_postal_code',
            'project_site_address',
            'global_amount',
            'completion',
            'last_relaunch',
            'work_schedule',
            'contact_name',
            'comment'

        ],
        excludedColumns: [],
        containerSelector: containerSelector,
        filtersContainerSelector: filtersContainerSelector,
        columnsCount: columnsCount,
        settings: settings,
        debug: false,
        enableFormatter: true,
        redrawEvent: 'project.deleted'
    })
    dtPromise.then(function (dtInstance) {
        const tableId = dtInstance.table().node().id;
        var $table = $('#' + tableId);
        let modal = $('#businessChargeModal');
        let modalEconomist = $('#economistModal');
        let idProject = null;
        let idPreset = null;
        $table.on('draw.dt', function () {
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
        })
        // then save the business charge
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
}