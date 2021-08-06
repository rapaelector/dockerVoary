import LoadPlanDialogController from './new-load-plan-dialog.controller';

angular.module('loadPlanApp').controller('loadPlanController', ['$scope', '$mdDialog', 'loadPlanService', function ($scope, $mdDialog, loadPlanService) {
    this.$onInit = () => {
        $('body').on('click', '.edit-load-plan', function (ev) {
            $scope.editLoadPlan({mode: $(this).data('action'), id: $(this).data('id')});
        });
    };

    $scope.addLoadPlan = () => {
        var options = {};
        $scope.showLoadPlanDialogModal(options);
    };

    /**
     * - Data structure
     *      {
     *          mode: string ("edit", "create", "delete"),
     *          id: number
     *      }
     * @param {object} options
     */
    $scope.editLoadPlan = (options) => {
        $scope.showLoadPlanDialogModal(options);
    };

    $scope.showLoadPlanDialogModal = (options) => {
        $mdDialog.show({
            controller: LoadPlanDialogController,
            templateUrl: 'new-load-plan-dialog.html',
            parent: angular.element(document.body),
            targetEvent: event,
            locals: {
                options,
            },
            clickOutsideToClose: true,
        }).then(function (answer) {
            $scope.status = 'You said the information was "' + answer + '".';
        }, function () {
            $scope.status = 'You cancelled the dialog.';
        });
    }
}]);