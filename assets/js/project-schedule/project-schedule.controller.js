import { resources, buildColumns } from './mock-data';
import numberFormat from './../utils/number_format';

angular.module('projectScheduleApp').controller('projectScheduleController', ['$scope', '$mdDialog', function($scope, $mdDialog) {

    $scope.data = {
        resources: resources,
        columns: buildColumns(numberFormat),
    };

    this.$onInit = function() {}
}]);