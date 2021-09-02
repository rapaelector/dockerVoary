RealizationPanelController.$inject = ['$scope', 'options', 'mdPanelRef', 'loadPlanService'];

function RealizationPanelController($scope, options, mdPanelRef, loadPlanService) {
    $scope.data = {
        realizationDate: null,
    };
    $scope.loading = false;

    this.$onInit = () => {
        $scope.data.realizationDate = new Date(options.realizationDate);
    };

    $scope.saveRealizationDate = (ev) => {
        $scope.loading = true;
        loadPlanService.updateRealizationDate(options.projectId, {realizationDate: $scope.data.realizationDate}).then((response) => {
            loadPlanService.showNotification(response.data.message, 'toast-success');
            location.reload();
            $scope.loading = false;
        }, errors => {
            loadPlanService.showNotification(errors.data.message, 'toast-error');
            location.reload();
            $scope.loading = false;
        });
    };

    $scope.closePanel = () => {
        mdPanelRef.close();
    };
}

export default RealizationPanelController;