import ColumnsVisibilityController from './columns-visibility.controller';

angular.module('loadPlanPlanningApp').controller('loadPlanPlanningController', [
    '$scope', 
    '$mdPanel', 
    'loadPlanPlanningService', 
    function($scope, $mdPanel, loadPlanPlanningService) {
    
    $scope.data = {
        date: {
            startDate: moment().startOf('year'),
            endDate: moment().endOf('year'),
        },
        resources: [],
        events: [],
        columns: [],
    };
    $scope.options = {
        dateRangePicker: {},
    };
    $scope.loadingResources = false;
    $scope.loadingEvents = false;

    const loadPlanPlanningColumns = [
        {
            label: 'Project',
            field: 'folderNameOnTheServer',
            headerClassName: 'text-uppercase',
            className: 'dynamic-nowrap text-uppercase',
            width: 200,
        },
        {
            label: 'Commer',
            field: 'businessCharge.name',
            headerClassName: 'text-uppercase',
            className: 'dynamic-nowrap text-uppercase',
            width: 200,
        },
        {
            label: 'Eco',
            field: 'economist.name',
            headerClassName: 'text-uppercase',
            className: 'dynamic-nowrap text-uppercase',
            width: 200,
        }
    ];

    this.$onInit = () => {
        $scope.loadingResources = true;
        $scope.data.columns = loadPlanPlanningColumns;
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

        loadPlanPlanningService.getResources().then((response) => {
            $scope.data.resources = response.data.resources;
            $scope.loadingResources = false;
        });

        $scope.updateEvents();
    };

    $scope.$watch('data.date', function () {
        $scope.updateEvents();
    }, true);

    $scope.updateEvents = () => {
        $scope.loadingEvents = true;
        var start = moment($scope.data.date.startDate).format('YYYY-MM-DD');
        var end = moment($scope.data.date.endDate).format('YYYY-MM-DD');

        loadPlanPlanningService.getEvents({start: start, end: end}).then(function (events) {
            $scope.data.events = events;
            $scope.loadingEvents = false;
        });
    };

    /**
     * Show visibility columns dialog
     * @param {object} ev 
     */
    $scope.showVisibilityModal = (ev) => {
        console.info('showVisibilityModal');
        
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