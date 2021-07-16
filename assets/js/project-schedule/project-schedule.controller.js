import { resources, buildColumns } from './mock-data';
import numberFormat from './../utils/number_format';

angular.module('projectScheduleApp').controller('projectScheduleController', ['$scope', '$mdDialog', 'moment', function($scope, $mdDialog, moment) {

    $scope.data = {
        resources: resources,
        columns: buildColumns(numberFormat),
        start: null,
        end: null,
    };

    this.$onInit = function() {
        $scope.data.start = moment().startOf('year').add(-6, 'months');
        $scope.data.end = moment().endOf('year').add(3, 'months');
    };
}]);