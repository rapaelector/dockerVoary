function ProjectPilotingController($scope, projectService) {
    $scope.onLoading = false;
    $scope.data = {
        users: [],
    };
    $scope.exchangeHistory = {
        lastRelaunch: '',
        date: '',
        description: '',
        projectConfidencePercentage: '',
        archi: '',
    };

    this.$onInit = function() {
        projectService.getFormData().then((response) => {
            $scope.data.users = response.data.users;
        }, error => {
            console.info('failed to laodd');
        });
    }

    $scope.fns = {};
    $scope.fns.saveProjectPiloting = function() {
        $scope.onLoading = true;
        projectService.saveProjectPiloting($scope.exchangeHistory).then((response) => {
            console.info(response);
            $scope.onLoading = false;
        }, error => {
            console.info(error);
            $scope.onLoading = false;
        })
    };
}

ProjectPilotingController.$inject = ['$scope', 'projectService'];

angular.module('projectApp').component('appProjectPiloting', {
    templateUrl: 'project-piloting.html',
    controller: ProjectPilotingController,
});