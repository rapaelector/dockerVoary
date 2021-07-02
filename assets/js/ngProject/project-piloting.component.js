function ProjectPilotingController($scope, projectService) {
    $scope.onLoading = false;
    $scope.data = {
        isLastRelauch: '1',
        users: [],
        exchangeFlags: [],
    };
    $scope.exchangeHistory = {
        date: null,
        relaunchDate: '',
        nextStepDate: '',
        flag: '',
        description: '',
        projectConfidencePercentage: '',
        archi: '',
    };

    this.$onInit = function() {
        projectService.getFormData().then((response) => {
            $scope.data.users = response.data.users;
            $scope.data.exchangeHistory = response.data.exchangeHistory;
            $scope.data.exchangeFlags = response.data.exchangeFlags;
            $scope.exchangeHistory.flag = $scope.data.exchangeFlags[0];
        }, error => {
            console.info('failed to laodd');
        });
    }

    $scope.fns = {};
    $scope.fns.saveProjectPiloting = function() {
        $scope.onLoading = true;

        if ($scope.data.isLastRelauch == '1') {
            $scope.exchangeHistory.date = null;
        }

        projectService.saveProjectPiloting($scope.exchangeHistory).then((response) => {
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