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
    BORDER_WEIGHT,
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

    $scope.$watch('$ctrl.events', function () {
        console.info($scope.$ctrl.events);
    }, true);
    /**
     * Get resources to loop and display in the table
     * 
     * @param {object} resource 
     * @param {object} column 
     * @param {number} index 
     * @returns {array} array of string
     */
    $scope.getResourceColumn = function(resource, column, index) {
        var res = null;

        if (column.field) {
            res = resource[column.field];
        }

        if (column.formatter) {
            res = column.formatter(res, resource, index);
        }

        return res;
    };

    /**
     * Get resource cell class
     * Each cell have its header columns name as class suffix
     *      e.g: constructionSite(chantier) column
     *              - each constructionSite cell have scheduler-cell-constructionSite class
     * 
     * @param {object} resource 
     * @param {object} column 
     * @param {number} index 
     * @returns {array} array of string
     */
    $scope.getCellClassName = function(resource, column, index) {
        var res = ['scheduler-cell', 'scheduler-cell-' + column.field];

        if (column.className) {
            res.push(column.className);
        }

        if (column.classNameFormatter) {
            var className = column.classNameFormatter(res, resource, index);
            res.push(className);
        }

        return res;
    }

    /**
     * Get resource header column class
     * 
     * @param {object} column 
     * @returns {array} array of string
     */
    $scope.getHeaderCellClassName = function(column) {
        var res = ['scheduler-header-cell', 'scheduler-header-cell-' + column.field];

        if (column.headerClassName) {
            res.push(column.headerClassName);
        }

        return res;
    }

    /**
     * 
     * @param {object} column 
     * @returns {object} object of style
     */
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

    /**
     * 
     * @param {object} week 
     * @param {number} index 
     * @returns {object} object of style
     */
    $scope.getCellStyle = function (resource, week, index, resourceIndex) {
        var cellBorderLeft = 0;
        var cellBorderRight = 0;
        var cellBorderBottom = 0;

        if (week.firstWeek) {
            cellBorderLeft = BORDER_WEIGHT;
        }

        if (week.lastWeek) {
            cellBorderRight = BORDER_WEIGHT;
        }

        return {
            width: resolverService.resolve([$scope, 'options', 'cell', 'width'], DEFAULT_CELL_WIDTH + 'px'),
            'border-left-width': cellBorderLeft + 'px',
            'border-left-color': '#999',
        };
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

    /**
     * Get column merge style : column above the resource columns
     * 
     * @returns {object} object of style
     */
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
     * Get header year style and bind to the year header with ng-style
     * 
     * @param {object} year 
     * @param {number} index 
     * @returns {object} object of style
     */
    $scope.getYearStyles = function (year, index) {
        var width = year.weeksCount * resolverService.resolve([$scope, 'options', 'cell', 'width'], DEFAULT_CELL_WIDTH);
        return {width: width + 'px'};
    };
    
    /**
     * Get header month style and bind to the month header with ng-style
     * 
     * @param {object} month 
     * @param {number} index 
     * @returns {object} object of style
     */
    $scope.getMonthStyles = function (month, index) {
        var width = month.weeksCount * resolverService.resolve([$scope, 'options', 'cell', 'width'], DEFAULT_CELL_WIDTH);
        return {width: width + 'px'};
    };

    /**
     * Get header year class
     * If the year is same as current year add additional class to the year
     *      - scheduler-header-year-current
     *  
     * @param {object} year 
     * @param {number} index 
     * @returns {array} array of string
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
     * Get header month class
     * If the current month and current year is same as current month and current year add additional class to the header
     *      - scheduler-header-month-current
     * 
     * @param {object} month 
     * @param {number} index 
     * @returns {array} array of string
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

    /**
     * Get week number number class
     * If the current week, month and year is same as week.startDay (moment) then add additional class to the current week
     *      - scheduler-header-week-current
     * 
     * @param {object} week 
     * @param {number} index 
     * @returns {array} array of string
     */
    $scope.getHeaderWeekClassName = function (week, index) {
        var res = [];

        if ($scope.$ctrl.headerWeekClassName) {
            res.push($scope.$ctrl.headerWeekClassName);
        }

        if (week.startDay.isSame(moment(), 'week')) {
            res.push('scheduler-header-week-current');
        }

        if (week.firstWeek) {
            res.push('scheduler-header-first-week');
        };

        if (week.lastWeek) {
            res.push('scheduler-header-last-week')
        }
        return res;
    }

    $scope.getDateCellClassName = function () {
        var res = ['scheduler-date-cell'];

        return res;
    };

    // SCHEDULER EVENT HANDLER
    /**
     * Handler for row click
     * 
     * @param {object} resource 
     * @param {column} column 
     * @param {number} columnIndex 
     */
    $scope.onRowClick = function (resource, column, columnIndex) {
        if ($scope.$ctrl.onRowClick) {
            $scope.$ctrl.onRowClick(resource, column, columnIndex);
        }
    }

    /**
     * Handler for column header click
     * 
     * @param {object} column 
     * @param {number} columnIndex 
     */
    $scope.onColumnHeaderClick = function (column, columnIndex) {
        if ($scope.$ctrl.onColumnHeaderClick) {
            $scope.$ctrl.onColumnHeaderClick(column, columnIndex);
        }
    }

    /**
     * Handler for header year click
     * 
     * @param {object} yearObject 
     * @param {number} yearIndex
     */
    $scope.onHeaderYearClick = function (yearObject, yearIndex) {
        if ($scope.$ctrl.onHeaderYearClick) {
            $scope.$ctrl.onHeaderYearClick(yearObject, yearIndex);
        }
    }

    /**
     * Header for header month click
     * 
     * @param {object} monthObject 
     * @param {number} monthIndex 
     */
    $scope.onHeaderMonthClick = function (monthObject, monthIndex) {
        if ($scope.$ctrl.onHeaderMonthClick) {
            $scope.$ctrl.onHeaderMonthClick(monthObject, monthIndex);
        }
    }

    /**
     * Handler for header week click
     * 
     * @param {object} weekObject 
     * @param {number} weekIndex 
     */
    $scope.onHeaderWeekClick = function (weekObject, weekIndex) {
        if ($scope.$ctrl.onHeaderWeekClick) {
            $scope.$ctrl.onHeaderWeekClick(weekObject, weekIndex);
        }
    }

    /**
     * Handler for date cell click
     * 
     * @param {object} resource 
     * @param {object} week 
     * @param {number} weekIndex 
     * @param {number} resourceIndex 
     */
    $scope.onCellClick = function (resource, week, weekIndex, resourceIndex) {
        if ($scope.$ctrl.onCellClick) {
            $scope.$ctrl.onCellClick(resource, week, weekIndex, resourceIndex);
        }
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
    'BORDER_WEIGHT',
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
        /**
         * Moment
         */
        start: '=',
        /**
         * Moment
         */
        end: '=',
        /**
         * Object
         */
        options: '=',
        /**
         * Header class name
         * String
         */
        headerYearClassName: '=',
        /**
         * Header month class name
         */
        headerMonthClassName: '=',
        /**
         * Header week class name
         */
        headerWeekClassName: '=',
        // SCHEDULER EVENTS HANDLER
        /**
         * Callback
         * Args:
         *  - resource (object)
         *      structure:
         *          {
         *              area: string,
         *              cdtTrx: string,
         *              constructionSite: string,
         *              invoiced: string
         *              remainsToInvoice: number,
         *              turnover: number,
         *              workType: number,
         *          }
         *  - column (object)
         *      structure:
         *          {
         *              field: string
         *              label: string
         *              width: string | number
         *          }
         *  - columnIndex (number)
         */
        onRowClick: '=',
        /**
         * Callback 
         * - column
         *      structure:
         *          {
         *              className: string,
         *              classNameFormatter: callback (Object res, Object resource, Number index),
         *              field: string,
         *              formatter: callback (Object res, Object resource, Number index)
         *              headerClass: string
         *              headerColumnFormatter: callback (Object column, Number index)
         *              label: string,
         *              width: string | number,
         *              columnIndex: number
         *          }
         * - columnIndex: number,
         */
        onColumnHeaderClick: '=',
        /**
         * Callback
         * - yearObject: object
         *      structure:
         *          {
         *              monthsCount: number,
         *              name: string,
         *              weeksCount: number,
         *          }
         * 
         * - yearIndex: number
         */
        onHeaderYearClick: '=',
        /**
         * Callback
         * - monthOject: object
         *      structure:
         *          {
         *              monthIndex: number,
         *              monthObject: object {name: string, monthNumber: string, year: string, weeksCount: number}
         *          }
         * - monthIndex: number
         */
        onHeaderMonthClick: '=',
        /**
         * Callback
         * - weekObject: object
         *      structure:
         *          {
         *              endDay: Moment,
         *              firstweek: boolean,
         *              lastweek: boolean,
         *              monthNumber: string
         *              startDay: Moment
         *              weekNumber: number
         *              year: string
         *          }
         * - weekIndex: number
         */
        onHeaderWeekClick: '=',
        /**
         * Callback
         * - resource: object
         *      structure:
         *          {
         *              area: string,
         *              cdtTrx: string,
         *              constructionSite: string,
         *              invoiced: string
         *              remainsToInvoice: number,
         *              turnover: number,
         *              workType: number,
         *          }
         * - week: object
         *      structure:
         *          {
         *              year: string,
         *              monthNumber: string,
         *              firstWeek: boolean,
         *              lastWeek: boolean,
         *              weekNumber: number,
         *              startDay: Moment,
         *              endDay: Moment,
         *          }
         * - weekIndex: number
         * - resourceIndex: number
         */
        onCellClick: '=',
        events: '=',
    }
});