import { resources, buildColumns, events  } from './mock-data';
import numberFormat from './../utils/number_format';

angular.module('projectScheduleApp').controller('projectScheduleController', ['$scope', '$mdDialog', 'moment','projectSchedulerService','resolverService', 'DEFAULT_CELL_WIDTH', function($scope, $mdDialog, moment, projectSchedulerService, resolverService, DEFAULT_CELL_WIDTH) {

    $scope.data = {
        resources: [],
        columns: buildColumns(numberFormat),
        start: null,
        end: null,
        date: {
            startDate: moment().startOf('year'),
            endDate: moment().endOf('year'),
        },
        events: events,
    };
    $scope.options = {
        dateRangePicker: {},
        scheduler: {
            defaultCellWidth: DEFAULT_CELL_WIDTH,
            cell: {
                width: 24,
            },
            event: {
                zIndex: {
                    'payment': 9999,
                }
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
        
        projectSchedulerService.getResources($scope.data.date).then(function (response) {
            $scope.data.resources = resolverService.resolve([response, 'data', 'resources'], []);
        });
        $scope.updateEvents();
    };

    $scope.$watch('data.date', function () {
        $scope.updateEvents();
    }, true);

    $scope.updateEvents = function () {
        projectSchedulerService.getEvents($scope.data.date).then(function (events) {
            $scope.data.events = events;
        });
    };

    /**
     * Row click event
     * 
     * @param {object} resource 
     * @param {object} column 
     * @param {number} columnIndex 
     */
    $scope.onRowClick = function (resource, column, columnIndex) {
        console.info('onRowClick called !!', {resource, column, columnIndex});
    }

    /**
     * Column header click event
     * 
     * @param {object} column 
     * @param {number} columnIndex 
     */
    $scope.onColumnHeaderClick = function (column, columnIndex) {
        console.info('onColumnHeaderClick called !!', {column, columnIndex});
    }

    /**
     * Header year click event
     * 
     * @param {object} yearObject 
     * @param {number} yearIndex 
     */
    $scope.onHeaderYearClick = function (yearObject, yearIndex) {
        console.info('onHeaderYearClick called !!', {yearObject, yearIndex});
    }

    /**
     * Header month click event
     * 
     * @param {object} monthObject 
     * @param {number} monthIndex 
     */
    $scope.onHeaderMonthClick = function (monthObject, monthIndex) {
        console.info('onHeaderMonthClick called !!', {monthObject, monthIndex});
    }

    /**
     * Header week click event
     * 
     * @param {object} weekObject 
     * @param {number} weekIndex 
     */
    $scope.onHeaderWeekClick = function (weekObject, weekIndex) {
        console.info('onHeaderWeekClick called !!', {weekObject, weekIndex});
    }

    /**
     * week cell click event
     * 
     * @param {object} resource 
     * @param {object} week 
     * @param {number} weekIndex 
     * @param {number} resourceIndex 
     */
    $scope.onCellClick = function (resource, week, weekIndex, resourceIndex) {
        console.info('onCellClick called !!', {resource, week, weekIndex, resourceIndex});
    }

    $scope.onEventClick = function (event, eventIndex, jsEvent) {
        console.info('Hello you click the event');
    }
}]);