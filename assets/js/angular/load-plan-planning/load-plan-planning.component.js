import loadPlanPlanningTemplate from './load-plan-planning-template.html';
import {resources, buildColumns, events} from './../mock-data.js';
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
        event: {
            boxShadowSticky: false,
            boxShadow: true,
        },
        positionsFix: {
            stickyColumnsLeft: 2,
            stickyColumnsFirstWeekWidth: 1,
            minusLeft: 0.5,
            startFirstWeekWidth: 1,
            endLastWeekWidth: 1,
        },
    };

    this.$onInit = () =>  {
        var textCenter = 'text-center';
        $scope.data.date.startDate = moment();
        $scope.data.date.endDate = moment().add(1, 'year');
        $scope.options.headerYearClassName = textCenter;
        $scope.options.headerMonthClassName = textCenter;
        $scope.options.headerWeekClassName = textCenter;
    }

    $scope.$watch('$ctrl.start', function() {
        $scope.data.date.startDate = $scope.$ctrl.start;
    });
    
    $scope.$watch('$ctrl.end', function() {
        $scope.data.date.endDate = $scope.$ctrl.end;
    });

    $scope.$watch('$ctrl.resources', function() {
        $scope.data.resources = $scope.$ctrl.resources;
    }, true);
    
    $scope.$watch('$ctrl.events', function() {
        $scope.data.events = $scope.$ctrl.events;
    }, true);

    $scope.$watch('$ctrl.columns', function () {
        $scope.data.columns = $scope.$ctrl.columns;
    }, true);
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

        /**
         * Resources:
         * Array of object:
         *      - [{...}, {...}, ...]
         * Data structure:
         *      [
         *          {
         *              id: number,
         *              economist: object,
         *                  struct : {name: string} 
         *              businessCharge: object,
         *                  struct: {name: string}
         *              projectId: number
         *          },
         *          {...},
         *      ]
         */
        resources: '=',

        /**
         * Columns:
         * Array of object:
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
         *      sticky: boolean,
         *      visible: boolean {true}
         * }
         * Oject argument explaination:
         *      - label: label to show in the table header
         *      - field: resource field to display in each cell
         *      - className: cell data class, bind to each data td cell
         *      - headerClassName: Bind to the column header th
         *      - sticky: boolean if the column should sticky
         *      - visible: boolean default valeu true; Make all columns visible
         *      - formatter: function to format the cell value (can be anything)
         *          args:
         *              - res: data of the cell
         *              - resource: resouce object
         *              - i: index of the resource
         * 
         *      @param {Object} res 
         *      @param {Object} resource 
         *      @param {any} index 
         *      @returns {string}
         *      - classNameFormatter: function to format the class
         *      
         *  
         *      Change resource header content to html
         *  
         *      @param {Object} column 
         *      @param {any} index 
         *      @returns {string} part of html
         *      - headerColumnFormatter: function (column, index) {
         */
        columns: '=',

        /**
         * - Array of object
         *      structure:
         *          [
         *              {
         *                  backgroundColor: "#1f497d"
         *                  end: "2021-01-23T00:00:00+00:00"
         *                  id: 14
         *                  project: {id: 3}
         *                  resource: 3
         *                  start: "2020-12-31T00:00:00+00:00"
         *                  type: "shade_house"
         *                  
         *              },
         *              {...}
         *          ]
         */
        events: '=',
    }
})
