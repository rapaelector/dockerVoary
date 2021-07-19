import schedulerTemplate from './template.html';

function SchedulerController($scope, $mdDialog, moment, calendarService) {
    this.$onInit = function () {};
    $scope.weeks = null;
    $scope.months = null;
    $scope.years = null;
    $scope.dates = null;
    $scope.start = null;
    $scope.end = null;

    $scope.$watch('$ctrl.start', function () {
        var start = moment($scope.$ctrl.start);
        if (!calendarService.isValidDate($scope.$ctrl.start)) {
            throw 'Start date is not a moment valid date';
        }
        $scope.start = start;
        $scope.updateDates();
    });

    $scope.$watch('$ctrl.end', function () {
        var end = moment($scope.$ctrl.end);
        if (!calendarService.isValidDate($scope.$ctrl.end)) {
            throw 'End date is not a moment valid date';
        }
        $scope.end = end;
        $scope.updateDates();
    });

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

    $scope.getHeaderColumnFormatter = function (column, index) {
        if (column.headerColumnFormatter) {
            return column.headerColumnFormatter(column, index);
        }

        return column.label;
    }

    $scope.getYearHeaderFormatter = function (value, index) {
        console.info($scope.$ctrl.yearFormatter);

        if ($scope.$ctrl.yearFormatter) {
            return $scope.$ctrl.yearFormatter(value, index);
        }

        return value;
    };

    $scope.getMonthHeaderFormatter = function (value, index) {
        console.info($scope.$ctrl.monthFormatter);

        if ($scope.$ctrl.monthFormatter) {
            return $scope.$ctrl.monthFormatter(value, index);
        }

        return value;
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
};

SchedulerController.$inject = ['$scope', '$mdDialog', 'moment', 'calendarService'];

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
    }
});