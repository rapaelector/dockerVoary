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
                },
                bubbleHtml: {
                    zIndex: 9999999,
                    width: '300px',
                },
                titleFormatter:  function (title, event) {
                    return (event.group == 'payment') ? '▮▮' : title;
                },
                bubbleDelay: {
                    payment: 100,
                    default: 400,
                },
            }
        },
        headerYearClassName: 'year-class text-center',
        headerMonthClassName: 'month-class text-center',
        headerWeekClassName: 'week-class text-center',
    };

    this.$onInit = function() {
        $scope.loadingResources = true;
        $scope.loadingEvents = true;
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
            $scope.loadingResources = false;
        });
        $scope.updateEvents();
    };

    $scope.$watch('data.date', function () {
        $scope.updateEvents();
    }, true);

    $scope.updateEvents = function () {
        $scope.loadingEvents = true;
        projectSchedulerService.getEvents($scope.data.date).then(function (events) {
            $scope.data.events = events;
            $scope.loadingEvents = false;
        });
    };

    /**
     * Row click event
     * 
     * @param {object} resource 
     * @param {object} column 
     * @param {number} columnIndex 
     */
    $scope.onRowClick = function (resource, column, columnIndex) {}

    /**
     * Column header click event
     * 
     * @param {object} column 
     * @param {number} columnIndex 
     */
    $scope.onColumnHeaderClick = function (column, columnIndex) {}

    /**
     * Header year click event
     * 
     * @param {object} yearObject 
     * @param {number} yearIndex 
     */
    $scope.onHeaderYearClick = function (yearObject, yearIndex) {}

    /**
     * Header month click event
     * 
     * @param {object} monthObject 
     * @param {number} monthIndex 
     */
    $scope.onHeaderMonthClick = function (monthObject, monthIndex) {}

    /**
     * Header week click event
     * 
     * @param {object} weekObject 
     * @param {number} weekIndex 
     */
    $scope.onHeaderWeekClick = function (weekObject, weekIndex) {}

    /**
     * week cell click event
     * 
     * @param {object} resource 
     * @param {object} week 
     * @param {number} weekIndex 
     * @param {number} resourceIndex 
     */
    $scope.onCellClick = function (resource, week, weekIndex, resourceIndex) {}

    /**
     * Handle event click
     * 
     * @param {object} event 
     * @param {number} eventIndex 
     * @param {object} jsEvent 
     */
    $scope.onEventClick = function (event, eventIndex, jsEvent) {}
}]);