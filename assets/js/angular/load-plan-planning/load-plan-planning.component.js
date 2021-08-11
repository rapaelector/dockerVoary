import loadPlanPlanningTemplate from './load-plan-planning-template.html';
import {resources, buildColumns, events,} from './../mock-data.js';
import numberFormat from './../../utils/number_format';

function LoadPlanPlanningController($scope) {
    $scope.data = {
        resources: [],
        columns: [],
        events: [],
        date: {
            endDate: null,
            startDate: null,
        },
    };
    $scope.options = {
        headerYearClassName: null,
        headerMonthClassName: null,
        headerWeekClassName: null,
    };

    this.$onInit = () =>  {
        var textCenter = 'text-center';
        $scope.data.resources = resources;
        console.info($scope.data.resources);
        $scope.data.columns = buildColumns(numberFormat);
        $scope.data.date.startDate = moment();
        $scope.data.date.endDate = moment().add(1, 'year');
        $scope.data.events = events;
        console.info(events);
        $scope.options.headerYearClassName = textCenter;
        $scope.options.headerMonthClassName = textCenter;
        $scope.options.headerWeekClassName = textCenter;
    }

    $scope.$watch('$ctrl.start', () => {
        $scope.data.date.startDate = $scope.$ctrl.start;
        console.info($scope.$ctrl.start);
    });
    
    $scope.$watch('$ctrl.end', () => {
        $scope.data.date.endDate = $scope.$ctrl.end;
        console.info($scope.$ctrl.end);
    })
}

LoadPlanPlanningController.$inject = ['$scope'];

angular.module('loadPlanPlanningModule').component('appLoadPlanPlanning', {
    template: loadPlanPlanningTemplate,
    controller: LoadPlanPlanningController,
    bindings: {
        /**
         * moment()
         */
        start: '=',
        /**
         * moment()
         */
        end: '=',
    }
})
