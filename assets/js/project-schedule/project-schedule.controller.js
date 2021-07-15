angular.module('projectScheduleApp').controller('projectScheduleController', ['$scope', '$mdDialog', function($scope, $mdDialog) {
    $scope.data = {
        ressources: [
            'hello',
            'hello 1',
            'hello 2',
            'hello 3',
            'hello 4',
        ],
    };

    this.$onInit = function() {}
}]);