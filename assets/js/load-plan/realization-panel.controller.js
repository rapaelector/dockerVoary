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
            $scope.loading = false;
            $('body').trigger('load_plan.redraw-dt');
            $scope.closePanel();
        }, errors => {
            $scope.loading = false;
            loadPlanService.showNotification(errors.data.message, 'toast-error');
            $('body').trigger('load_plan.redraw-dt');
            $scope.closePanel();
        });
    };

    $scope.closePanel = () => {
        mdPanelRef.close();
    };
}

export default RealizationPanelController;