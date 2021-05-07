import columnDefs from './column-defs';
import { initColumnFilters } from './datatable-column-filters';
import { appConsole } from '../utils';

const initAppDataTables = ({
    /**
     * Column names
     * @var String[]
     */
    columnNames=[],

    /**
     * Datatable table container selector
     * Example, in the following case :
     *      <div id="user-table">Chargement...</div>
     * The selector will be #user-table
     * @var String
     */
    containerSelector,

    /**
     * Filters container selector
     * @var String
     */
    filtersContainerSelector,

    /**
     * Datatable options from omines datatable instance
     * Example twig : {{ datatable_settings(datatable)|raw }}
     * @var Object
     */
    settings,

    /**
     * The number of columns the table will have
     * buildDataTableConfig columnsCount argument
     * @var Number
     */
    columnsCount,

    /**
     * buildDataTableConfig excludedColumns argument
     * @var String[]
     */
    excludedColumns = [],

    /**
     * Column filters flag, whether to enable it or not
     * Default value : true (enabled)
     * @var boolean
     */
    enableColumnFilters = true,

    /**
     * Whether to enable datatable formatter or not
     * Default value: true
     * @var boolean
     */
    enableFormatter = true,

    /**
     * Debug flag, if enabled, it will print variables
     */
    debug = false,
}) => {
    /**
     * Resolve column defs
     */
    const columnDefs = columnDefsResolver(columnNames);

    appConsole('info', debug, 'columnDefs', columnDefs);

    // const dtPromise = $('#user-table').initDataTables(
    const dtPromise = $(containerSelector).initDataTables(
        settings,
        buildDataTableConfig(columnsCount, excludedColumns, {
            columnDefs: columnDefs,
        })
    );

    /**
     * wait for the dtPromise to be resolved
     * The resolved value will be a datatable instance
     */
    dtPromise.then(dtInstance => {
        try {
            // get table id from the datatable instance
            const tableId = dtInstance.table().node().id;
            appConsole('info', debug, 'Table id : ' + tableId);

            if (enableFormatter) {
                appConsole('info', debug, 'Formatting table');
                // create a formater
                var formatter = new CellFormatter();
                // format table
                formatter.format($('#' + tableId));
            }

            if (enableColumnFilters) {
                // init column filters
                appConsole('info', debug, 'Init column filters')
                initColumnFilters(filtersContainerSelector, dtInstance);
            }
        } catch (error) {
            console.warn('Datatable init encountered an error ', error);
        }
    });
};

function CellFormatter (dtPromise, config) {
    var _this = this;

    var defaultConfig = {
        dynamicNoWrap: '.dynamic-nowrap',
        tooltipText: '.tooltip-text',
        popupConfig: {
            'data-toggle': 'tooltip',
            'data-container': 'body',
            'data-html': true,
        }
    };

    _this.buildConfig = function (config) {
        return $.extend(true, {}, defaultConfig, config);
    }

    _this.format = function ($table, config) {
        config = _this.buildConfig(config);

        $table.on('draw.dt', function () {
            $(config.dynamicNoWrap).each(function () {
                var $elem = $(this);
                var title = $elem.html();
                var $tooltipText = $elem.find(config.tooltipText);
                if ($tooltipText.length > 0) {
                    title = $tooltipText.html();
                }
                if (!$elem.is('th')) {
                    $elem.attr($.extend(true, {}, config.popupConfig, {
                        'title': title,
                    }));
                }
            });
        });
    }

    return _this;
}

function buildDataTableConfig (columnCount, excludedColumns, config) {
    var columns = buildColumnsFromCount(columnCount, excludedColumns);
    
    config = $.extend(true, {
        searching: true,
        dom: `
            <'row'<'col-sm-6'l><'col-sm-6'f>>
            <'row'<'col-sm-12 mt-table'<''tr>>>
            <'row mt-3'<'col-sm-5'i><'col-sm-7'p>>
        `,
        scrollX: true,
        clearButton: true,
        fixedColumns: {
            rightColumns: 1,
        },
        // paging:  true,
        buttons: [{
            extend: 'colvis',
            text: "Afficher/masquer les colonnes",
            columns: columns,
        }]
    }, config);

    return config;
}
function buildColumnsFromCount (columnCount, excludedColumns) {
    return new Array(columnCount).fill(0).map(function (a, i) {
        return i;
    }).filter(function (i) {
        return excludedColumns.indexOf(i) < 0;
    });
};

function buildExportConfig (columnCount, excludedColumns, config, exportOptions) {
    var columns = buildColumnsFromCount(columnCount, excludedColumns);

    config = $.extend(true, {
        buttons: [
            {
                extend: 'colvis',
                text: "Afficher/masquer les colonnes",
                columns: columns,
            },
            {
                extend: 'excelHtml5',
                text: 'Télécharger en excel',
                createEmptyCells: true,
                exportOptions: {
                    columns:  exportOptions.columns,
                    format: {
                        body: function (data, row, column, node) {
                            var res = data;

                            try {
                                if (exportOptions.dataValueColumns.indexOf(column) != -1) {
                                    var $elem = $(data);
                                    if ($elem.length > 0) {

                                        var value = $elem.data('value');
                                        if (value != undefined) {
                                            res = '' + value;
                                        }
                                    } 
                                }
                            } catch (e) {
                                console.error(e);
                            }
                            
                            return res;
                        }
                    }
                },
            }
        ]
    }, config);

    return buildDataTableConfig(columnCount, excludedColumns, config);
};

function columnDefsResolver (columns) {
    var defs = [];

    for (var i in columns) {
        var index = columnDefs.findIndex(function (columnDef) {
            return columnDef && columnDef.cols && (columnDef.cols.indexOf(columns[i]) > -1);
        });
        if (index > -1) {
            var def = $.extend(true, {}, columnDefs[index]);
            def.targets = +i;
            def.cols = columns[i];
            defs.push(def);
        }
    }

    return defs;
}

function initColvisGroups ($table, dt) {
    var $dropDownBtn = buildColvisGroupBtn();
    var $dropDownContent = buildColvisGroupContent();
    var stateKey = "DataTables-" + $table.attr('id') +  window.location.pathname + "-user-"+ userId + "-groupVisibility";

    $table.on('init.dt', function () {
        getGroupVisibility();
        var $colvisContainer = $('.colvis-groups');
        $colvisContainer.empty().append($dropDownBtn);
        $colvisContainer.append($dropDownContent);

        function getGroupsState() {
            var groups = {};
            $('.colvis-group-content > button[data-name]').each(function () {
                var $elem = $(this);
                groups[$elem.data('name')] = $elem.hasClass('active');
            });

            return groups;
        }

        function saveGroupVisibility() {
            $.ajax({
                type: "POST",
                count: 0,
                url: Routing.generate('flotte.datatable.state', {stateKey: stateKey}),
                data: {data: JSON.stringify(getGroupsState())},
                success: function () {
                    $('.colvis-group-content .colvis-group-loader').addClass('d-none');
                },
                dataType: "json",
                error: function () { 
                    if (this.count < 10) { 
                        $.ajax(this) 
                    } else {
                        console.info("state save error");
                    }
                },
            })
        }

        function getGroupVisibility() {
            $.ajax({
                type: "GET",
                count: 0,
                url: Routing.generate('flotte.datatable.state', {stateKey: stateKey}),
                success: function (response) {
                    for (var i in response.data) {
                        var $elem = $('.colvis-group-content > button[data-name="'+i+'"]');
                        if (response.data[i]) {
                            $elem.addClass('active');
                        } else {
                            $elem.removeClass('active');
                        }
                    }
                },
                dataType: "json",
                error: function () { 
                    if (this.count < 10) { 
                        this.count++; $.ajax(this) 
                    } else {
                        console.info("state load error");
                    }
                },
            })
        }

        var timeoutId = null;
        var groupCols = {};
        $('.colvis-group-content > button').each(function () {
            var btnName = $(this).data('name');
            $(this).addClass('active');

            $(this).on('click', function (e) {
                var state = getGroupsState();
                var colsNumber = [];
                var visibility = false;

                var groupsAllDisplayed = Object.values(state).filter(function (value) {
                    return !value;
                }).length === 0;

                if (groupsAllDisplayed) {
                    var clickedKey = $(this).data('name');
                    for (var key in state) {
                        var _colsNumber = $('[data-name="'+ key +'"').data('cols').split(';');
                        if (key != clickedKey) {
                            $('[data-name="'+ key +'"').removeClass('active');
                            colsNumber = colsNumber.concat(_colsNumber);
                        }
                    }
                    visibility = false;
                } else {
                    colsNumber = $(this).data('cols').split(';');
                    if ($(this).hasClass('active')) {  
                        visibility = false;
                        $(this).removeClass('active');
                    } else {
                        visibility = true;
                        $(this).addClass('active');
                    }
                }

                var newCols = {};
                for (var i in colsNumber) {
                    newCols[colsNumber[i]] = visibility;
                }
                groupCols = jQuery.extend({}, groupCols, newCols);
                if (timeoutId) {
                    clearTimeout(timeoutId);
                }
                
                timeoutId = setTimeout(function () {
                    var showCols = [];
                    var hideCols = [];
                    $('.colvis-group-content .colvis-group-loader').removeClass('d-none');
                    for (var colIndex in groupCols) {
                        if (groupCols[colIndex]) {
                            showCols.push(colIndex);
                        } else {
                            hideCols.push(colIndex);
                        }
                    }
                    
                    if (showCols.length > 0) {
                        dt.columns(showCols).visible(true);
                    }
                    if (hideCols.length > 0) {
                        dt.columns(hideCols).visible(false);
                    }
                    
                    saveGroupVisibility();
                    groupCols = {};
                    dt.draw();
                }, 1000);
            });
        });
    });
}

function buildColvisGroupBtn () {
    var $btn = $('<button>');
    $btn.attr({
        'class': 'colvis-dropdwon-btn dropdown-toggle',
        'id': 'colvis-dropdown', 
        'data-toggle': 'dropdown', 
        'role': 'button', 
        'aria-haspopup': 'true', 
        'aria-expanded': 'true',
    });
    $btn.html('Afficher/masquer');
    $btn.append('<span class="caret"></span>');

    return $btn;
}

/**
 * Create colvis show/hide button content
 */
function buildColvisGroupContent () {
    var $colvisContent = $('<div>');
    $colvisContent.attr({
        'class': 'colvis-group-content dropdown-menu',
        'aria-labelledby': 'colvis-dropdown'
    });

    for (var name in COLS_GROUP) {
        $colvisContent.append(
            '<button type="button" class="text-uppercase" data-name="'+ name +'" title="'+ LOCALE_FR[name] +'" data-cols="'+ COLS_GROUP[name].join(';') +'"><span>' + LOCALE_FR[name] + '</span></button>'
        );
    }
    $colvisContent.append('<div class="colvis-group-loader d-none"><i class="fa fa-spinner fa-spin"></i></div>');

    return $colvisContent;
}

export {
    CellFormatter,
    columnDefsResolver,
    buildDataTableConfig,
    initColumnFilters,
    initColvisGroups,
    buildExportConfig,
    initAppDataTables,
};
