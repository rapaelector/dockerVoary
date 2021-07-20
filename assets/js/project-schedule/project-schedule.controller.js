import { resources, buildColumns, yearFormatter, monthFormatter } from './mock-data';
import numberFormat from './../utils/number_format';

angular.module('projectScheduleApp').controller('projectScheduleController', [
    '$scope', 
    '$mdDialog', 
    'moment',
    'DEFAULT_CELL_WIDTH',
    function($scope, $mdDialog, moment, DEFAULT_CELL_WIDTH) {

    $scope.data = {
        resources: resources,
        columns: buildColumns(numberFormat),
        start: null,
        end: null,
        date: {
            startDate: moment().startOf('year'),
            endDate: moment().endOf('year'),
        },
    };
    $scope.options = {
        dateRangePicker: {},
        scheduler: {
            defaultCellWidth: DEFAULT_CELL_WIDTH,
            cell: {
                width: 24,
            }
        },
        headerYearClassName: 'year-class text-center',
        headerMonthClassName: 'month-class text-center',
        headerWeekClassName: 'week-class text-center',
    };

    this.$onInit = function() {
        $scope.options.dateRangePicker = {
            autoApply: true,
            autoClose: true,
            locale: {
                format: 'DD/MM/YYYY'
            },
            ranges: {
                "Aujourd'hui": [moment(), moment()],
                "Hier": [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                "Les 7 derniers jours": [moment().subtract(6, 'days'), moment()],
                "Les 30 derniers jours": [moment().subtract(29, 'days'), moment()],
                "Ce mois": [moment().startOf('month'), moment().endOf('month')],
                "Le mois dernier": [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
        };
    };

    $scope.$watch('data.date', function () {}, true);

    $scope.onRowClick = function (resource, column, columnIndex) {
        console.info('onRowClick called !!');
    }

    $scope.onColumnHeaderClick = function (column, columnIndex) {
        console.info('onColumnHeaderClick called !!');
    }

    $scope.onHeaderYearClick = function (yearObject, yearIndex) {
        console.info('onHeaderYearClick called !!');
    }

    $scope.onHeaderMonthClick = function (monthObject, monthIndex) {
        console.info('onHeaderMonthClick called !!');
    }

    $scope.onHeaderWeekClick = function (weekObject, weekIndex) {
        console.info('onHeaderWeekClick called !!');
    }

    $scope.onCellClick = function (resource, week, weekIndex, resourceIndex) {
        console.info('onCellClick called !!');
    }
}]);