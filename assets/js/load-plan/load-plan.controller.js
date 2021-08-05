import NewLoadPlanDialogController from './new-load-plan-dialog.controller';

angular.module('loadPlanApp').controller('loadPlanController', ['$scope', '$mdDialog', function ($scope, $mdDialog) {
    this.$onInit = () => {};

    $scope.addLoadPlan = (event) => {
        $mdDialog.show({
            controller: NewLoadPlanDialogController,
            templateUrl: 'new-load-plan-dialog.html',
            parent: angular.element(document.body),
            targetEvent: event,
            clickOutsideToClose: true,
        }).then(function (answer) {
            $scope.status = 'You said the information was "' + answer + '".';
        }, function () {
            $scope.status = 'You cancelled the dialog.';
        });
    };
}]);