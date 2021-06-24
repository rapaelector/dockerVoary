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
            'project_siteCode',
            'project_prospect',
            'project_user_email',
            'project_roadmap',
            'project_amount_subcontracted_work',
            'project_amount_bbi_specific_work',
            'project_global_amount',
            'created_at',
            'actions',
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
        $table.on('draw.dt', function () {
            $('body').on('click', '.business-charge-change', (e) => {
                e.preventDefault()
                var values = $(e.target); // Button that triggered the modal
                idProject = values.data('id');
                modal.modal('show');
                $('form.form-change-business-charge').attr('data-id', idProject)
            })
            $('body').on('click', '.economist-change', (e) => {
                e.preventDefault()
                var values = $(e.target); // Button that triggered the modal
                idProject = values.data('id');
                modalEconomist.modal('show');
                $('form.form-economist').attr('data-id', idProject)
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
                },
                error: function (error) {
                    Toast.fire({
                        icon: error.type,
                        title: error.message,
                    });
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
                },
                error: function (error) {
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