import numberFormat from './../utils/number_format';
import ColumnsVisibilityController from './columns-visibility.controller';

angular.module('projectScheduleApp').controller('projectScheduleController', [
    '$scope', 
    '$mdPanel',
    '$mdDialog', 
    'moment',
    'projectSchedulerService',
    'resolverService', 
    'DEFAULT_CELL_WIDTH', function(
        $scope, 
        $mdPanel,
        $mdDialog, 
        moment,
        projectSchedulerService, 
        resolverService, 
        DEFAULT_CELL_WIDTH
    ) {

    $scope.data = {
        resources: [],
        columns: [],
        start: null,
        end: null,
        date: {
            startDate: moment().startOf('year'),
            endDate: moment().endOf('year'),
        },
        events: [],
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
                    'payment': 100,
                    _default: 50,
                },
                bubbleHtml: {
                    zIndex: 1000,
                    width: '300px',
                },
                titleFormatter:  function (title, event) {
                    return (event.group == 'payment') ? '▮▮' : title;
                },
                bubbleDelay: {
                    payment: 500,
                    default: 1000,
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
                "Année courante": [moment().startOf('year'), moment().endOf('year')],
                "Les 6 dérnier mois": [moment(moment().endOf('month').add(-6, 'month').startOf('month')), moment(moment().endOf('month').add(-1, 'month'))],
                "Année dérniere": [moment(moment().add(-1, 'year').format('YYYY') + '-01-01'), moment(moment().add(-1, 'year').format('YYYY') + '-12-31')],
                "6 prochains mois": [moment(), moment().add(6, 'month')],
                "12 prochains mois": [moment(), moment().add(12, 'month').endOf('month')],
                "12 mois glissant": [moment(), moment().add(1, 'year').endOf('month')],
            },
        };
        
        $scope.data.columns = projectSchedulerService.buildColumns(numberFormat);
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

    $scope.showVisibilityModal = function (ev) {
        var position = $mdPanel.newPanelPosition()
            .relativeTo('.button-target')
            .addPanelPosition($mdPanel.xPosition.ALIGN_START, $mdPanel.yPosition.BELOW);

        var config = {
            attachTo: angular.element(document.body),
            controller: ColumnsVisibilityController,
            controllerAs: 'ctrl',
            templateUrl: 'columns-visibility.html',
            panelClass: 'demo-menu-example',
            position: position,
            locals: {
                columns: $scope.data.columns,
            },
            openFrom: ev,
            clickOutsideToClose: true,
            escapeToClose: true,
            focusOnOpen: false,
            zIndex: 1000,
            onCloseSuccess: (mdPanelRef, columns) => {
                if (Array.isArray(columns)) {
                    $scope.data.columns = columns;
                }
            },
        };

        $mdPanel.open(config);
    };
}]);