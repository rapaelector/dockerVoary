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
    completionInputSelector='.completion-input',
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
            'current_case_folder_name_on_the_server', 'business_charge', 'market_type', 'project_description_area',
            'code_postal', 'project_site_address', 'global_amount', 'archivement_pourcentage', 'last_relaunch', 'planning_project',
            'contact_name', 'comment'
        ],
        excludedColumns: [7], // exclude when exporting excel file
        containerSelector: containerSelector,
        filtersContainerSelector: filtersContainerSelector,
        columnsCount: columnsCount,
        settings: settings,
        debug: true,
        enableFormatter: true,
        redrawEvent: 'project_case.initDt',
        config: {
            fixedColumns: false,
            "initComplete": function (data, row) {
                var formattedTotal = window.App.Utils.numberFormat(row.extras.total, 0, ',', ' ');

                $('#total').text(formattedTotal);
            }
        }
    });
    dtPromise.then(function (dtInstance) {
        const tableId = dtInstance.table().node().id;
        var $table = $('#' + tableId);
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

                if (!isNaN(inputValue)) {
                    $completionLoader.removeClass('d-none');

                    $.ajax({
                        type: 'POST',
                        url: Routing.generate('project.case.update_archivement'),
                        data: {archivementValue: inputValue, id: id},
                        dataType: "json",
                        success: function (data) {
                            $completionLoader.addClass('d-none');
                            Toast.fire({
                                icon: data.type,
                                title: data.message,
                            });
                        },
                        error: function (error) { 
                            console.error('lorem ipsum dolor');
                            $completionLoader.addClass('d-none');
                            Toast.fire({
                                icon: error.type,
                                title: error.message,
                            });
                        },
                    })
                } else {
                    $(this).val(dataValue);
                }
            });
        })
    });
};