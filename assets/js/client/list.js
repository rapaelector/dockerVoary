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
            'client_number', 'client_name', 'prospect_name', 'client_postal_code',
            'client_activity', 'prospect_business_charge',  'client_created_at', 'actions'
        ],
        excludedColumns: [7],
        containerSelector: containerSelector,
        filtersContainerSelector: filtersContainerSelector,
        columnsCount: columnsCount,
        settings: settings,
        debug: false,
        enableFormatter: true,
        redrawEvent: 'client.deleted',
    })
    dtPromise.then(function (dtInstance) {
        const tableId = dtInstance.table().node().id;
        var $table = $('#' + tableId);
        let modal = $('#businessChargeModal');
        let idProject = null;
        let idPreset = null;
        $table.on('draw.dt', function () {
            $('body').on('click', '.prospect-business-change', (e) => {
                e.preventDefault()
                var values = $(e.target); // Button that triggered the modal
                idProject = values.data('id');
                idPreset = values.data('id-business-charge');
                modal.modal('show');
                $('form.form-change-business-charge').attr('data-id', idProject)
                $("#client_business_charge_businessCharge").val(idPreset).trigger('change')
            })
        })
        // then save the business charge
        $('body').on('click', '.business-charge-change-save', (e) => {
            let form = $('form.form-change-business-charge');
            let values = form.serialize();
            // then made some ajax there
            $.ajax({
                url: Routing.generate('client.edit.business.charge', {id: idProject}),
                method: "POST",
                accepts: "application/json; charset=utf-8",
                data: values,
                success: (data) => {
                    $("#client_business_charge_businessCharge").val(null).trigger('change')
                    modal.modal('hide');
                    dtInstance.draw();
                    Toast.fire({
                        icon: data.type,
                        title: data.message,
                    });

                },
                error: function (error) {
                    $("#client_business_charge_businessCharge").val(null).trigger('change')
                    Toast.fire({
                        icon: error.type,
                        title: error.message,
                    });

                },
            });
            return false;
        })
    });
}