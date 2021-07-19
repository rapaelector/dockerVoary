import schedulerTemplate from './template.html';

function SchedulerController(
    $scope, 
    $mdDialog, 
    moment, 
    calendarService, 
    resolverService, 
    DEFAULT_CELL_WIDTH,
    YEAR_BORDER_WIDTH,
    MONTH_BORDER_WIDTH,
    WEEK_BORDER_WIDTH,
    WEEK_EDGE_BORDER_WIDTH,
    CELL_BORDER_WIDTH,
    CELL_EDGE_BORDER_WIDTH,
) {
    $scope.weeks = null;
    $scope.months = null;
    $scope.years = null;
    $scope.dates = null;
    $scope.start = null;
    $scope.end = null;
    $scope.options = {};
    $scope.styles = {
        table: {},
    };

    this.$onInit = function () {};

    $scope.$watch('$ctrl.start', function () {
        var start = moment($scope.$ctrl.start);
        if (!calendarService.isValidDate($scope.$ctrl.start)) {
            throw 'Start date is not a moment valid date';
        }
        $scope.start = start;
        $scope.updateDates();
        $scope.updateTableStyles();
    });

    $scope.$watch('$ctrl.end', function () {
        var end = moment($scope.$ctrl.end);
        if (!calendarService.isValidDate($scope.$ctrl.end)) {
            throw 'End date is not a moment valid date';
        }
        $scope.end = end;
        $scope.updateDates();
        $scope.updateTableStyles();
    });

    $scope.$watch('$ctrl.columns', function () {
        $scope.updateTableStyles();
    });

    $scope.$watch('$ctrl.options', function () {
        var defaultOptions = {
            cell: {
                width: '28px',
            },
        };
        $scope.options = $.extend(true, defaultOptions, $scope.$ctrl.options);
        $scope.updateTableStyles();
    }, true);

    $scope.getResourceColumn = function(resource, column, i) {
        var res = null;

        if (column.field) {
            res = resource[column.field];
        }

        if (column.formatter) {
            res = column.formatter(res, resource, i);
        }

        return res;
    };

    $scope.getCellClassName = function(resource, column, i) {
        var res = ['scheduler-cell', 'scheduler-cell-' + column.field];

        if (column.className) {
            res.push(column.className);
        }

        if (column.classNameFormatter) {
            var className = column.classNameFormatter(res, resource, i);
            res.push(className);
        }

        return res;
    }

    $scope.getHeaderCellClassName = function(column) {
        var res = ['scheduler-header-cell', 'scheduler-header-cell-' + column.field];

        if (column.headerClassName) {
            res.push(column.headerClassName);
        }

        return res;
    }

    $scope.getColumnWidth = function (column) {
        return column.width ? {
            'width': parseInt(column.width) + 'px',
            'min-width': parseInt(column.width) + 'px',
        } : {};
    };

    $scope.updateDates = function () {
        if (!$scope.start || !$scope.end) {
            return;
        }
        $scope.dates = calendarService.getDates($scope.start, $scope.end);
        $scope.weeks = calendarService.getDatesWeeks($scope.dates);
        $scope.months = calendarService.getDatesMonths($scope.dates);
        $scope.years = calendarService.getDatesYears($scope.dates);
    };

    $scope.getHeaderWeeksStyle = function () {
        return {width: resolverService.resolve([$scope, 'options', 'cell', 'width'], DEFAULT_CELL_WIDTH + 'px')};
    };

    $scope.getCellStyle = function () {
        return {width: resolverService.resolve([$scope, 'options', 'cell', 'width'], DEFAULT_CELL_WIDTH + 'px')};
    };

    /**
     * Update table styles
     */
    $scope.updateTableStyles = function () {
        const tableStyles = {};
        let w = 0;
        if (!$scope.weeks || !$scope.$ctrl.columns) {
            return;
        }
        let OK = true;
        for (const i in $scope.$ctrl.columns) {
            const columnWidth = $scope.$ctrl.columns[i].width;
            if (isNaN(parseInt(columnWidth))) {
                console.info({columnWidth});
                OK = false;

                break;
            }
            w += +columnWidth;
        }
        const cellWidth = resolverService.resolve([$scope, 'options', 'cell', 'width'], DEFAULT_CELL_WIDTH);
        w += $scope.weeks.length * cellWidth;
        if (OK) {
            tableStyles.width = w + 'px';
            tableStyles['table-layout'] = 'fixed';
        } else {
            console.warn('NaN table style width');
        }

        $scope.styles.table = tableStyles;
    };

    $scope.columnMergeStyle = function () {
        var width = 0;
        if (!$scope.$ctrl.columns) {
            return;
        }

        for (const i in $scope.$ctrl.columns) {
            if (isNaN(parseInt($scope.$ctrl.columns[i].width))) {
                console.info('Invalid column width', $scope.$ctrl.columns[i]);
                return {width: 'auto'};
            }
            width += parseInt($scope.$ctrl.columns[i].width);
        }

        return { width: width };
    };

    /**
     * 
     * @param {Object} year 
     * @param {number} index 
     * @returns {Object}
     */
    $scope.getYearStyles = function (year, index) {
        var width = year.weeksCount * resolverService.resolve([$scope, 'options', 'cell', 'width'], DEFAULT_CELL_WIDTH);
        return {width: width + 'px'};
    };
    
    /**
     * 
     * @param {Object} month 
     * @param {number} index 
     * @returns {Object}
     */
    $scope.getMonthStyles = function (month, index) {
        var width = month.weeksCount * resolverService.resolve([$scope, 'options', 'cell', 'width'], DEFAULT_CELL_WIDTH);
        return {width: width + 'px'};
    };

    /**
     * 
     * @returns {array}
     */
    $scope.getHeaderYearClassName = function (year, index) {
        var res = [];

        if ($scope.$ctrl.headerYearClassName) {
            res.push($scope.$ctrl.headerYearClassName);
        }

        if (moment().format('YYYY') == year.name) {
            res.push('scheduler-header-year-current');
        }

        return res;
    }
    
    /**
     * 
     * @returns {array}
     */
    $scope.getHeaderMonthClassName = function (month, index) {
        var res = [];

        if ($scope.$ctrl.headerMonthClassName) {
            res.push($scope.$ctrl.headerMonthClassName);
        }
        if (moment(`${month.year}-${month.monthNumber}`, 'YYYY-M').isSame(moment(), 'month')) {
            res.push('scheduler-header-month-current');
        }

        return res;
    }

    $scope.getHeaderWeekClassName = function (week, index) {
        var res = [];

        if ($scope.$ctrl.headerWeekClassName) {
            res.push($scope.$ctrl.headerWeekClassName);
        }

        return res;
    }
};

SchedulerController.$inject = [
    '$scope', 
    '$mdDialog', 
    'moment', 
    'calendarService', 
    'resolverService', 
    'DEFAULT_CELL_WIDTH',
    'YEAR_BORDER_WIDTH',
    'MONTH_BORDER_WIDTH',
    'WEEK_BORDER_WIDTH',
    'WEEK_EDGE_BORDER_WIDTH',
    'CELL_BORDER_WIDTH',
    'CELL_EDGE_BORDER_WIDTH',
];

angular.module('schedulerModule').component('appScheduler', {
    template: schedulerTemplate,
    controller: SchedulerController,
    bindings: {
        /**
         * Array of resources :
         *  - [{...}, {...}, ...]
         * Resource object structure:
         * {
         *      cdtTrx: 'Borisav',
         *      constructionSite: 'ADF 001',
         *      workType: 'Travaux sur existant',
         *      area: '',
         *      turnover: 9786.99,
         *      invoiced: '',
         *      remainsToInvoice: 9786.99,
         * }
         */
        resources: '=',
        /**
         * Data structure: array of object
         *      - [{...}, {...}, ...]
         * Object structure:
         * {
         *      label: 'Chantier',
         *      field: 'constructionSite',
         *      className: 'chantier-class',
         *      headerClassName: 'text-uppercase text-center',
         *      formatter: function(res, resource, index) {
         *          return res ? '<b>' + res + '</b>' : '';
         *      },
         * }
         * Oject argument explaination:
         *      - label: label to show in the table header
         *      - field: resource field to display in each cell
         *      - className: cell data class, bind to each data td cell
         *      - headerClassName: Bind to the column header th
         *      - formatter: function to format the cell value (can be anything)
         *          args:
         *              - res: data of the cell
         *              - resource: resouce object
         *              - i: index of the resource     
         */
        columns: '=',
        start: '=',
        end: '=',
        /**
         * Year formatter
         * Format year before rendering
         */
        yearFormatter: '=',
        /**
         * Month formatter
         * Format year before rendering
         */
        monthFormatter: '=',
        /**
         * Object
         */
        options: '=',
        /**
         * Header class name
         */
        headerYearClassName: '=',
        headerMonthClassName: '=',
        headerWeekClassName: '=',
    }
});