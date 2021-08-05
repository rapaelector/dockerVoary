NewLoadPlanDialogController.$inject = ['$scope', '$mdDialog', 'loadPlanService'];

function NewLoadPlanDialogController ($scope, $mdDialog, loadPlanService) {
    $scope.data = {
        projects: [],
        errors: [],
    };
    $scope.loading = false;
    $scope.form = {
        project: null,
        natureOfTheCosting: null,
        weekNumber: null,
    };

    this.$onInit = () => {
        loadPlanService.getProjects().then((response) => {
            console.info(response.data.projects);
            $scope.data.projects = response.data.projects;
        });
    };

    $scope.saveLoadPlan = (event) => {
        $scope.loading = true;
        loadPlanService.saveLoadPlan($scope.form).then((response) => {
            $mdDialog.hide();
            window.location.reload();
            $scope.loading = false;
        }, errors => {
            $mdDialog.hide();
            console.warn(errors)
            $scope.data.errors = errors.errors;
            $scope.loading = false;
        });
    };

    $scope.cancel = (event) => {
        $mdDialog.hide();
    }
};

export default NewLoadPlanDialogController;