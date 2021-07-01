function ProjectPilotingController($scope, projectService) {
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
        console.info('init project-piloting-component');
        // projectService.getExchangeHistory().then((response) => {
        //     $scope.data.exchangeHistory = response.data.exchangeHistory;
        // }, error => {
        //     console.info('failed to load exchangeHistory');
        // });
        projectService.getFormData().then((response) => {
            console.info('nothing fetch or error occuered');
            $scope.data.users = response.data.users;
        }, error => {
            console.info('failed to laodd');
        });
    }
}

ProjectPilotingController.$inject = ['$scope', 'projectService'];

angular.module('projectApp').component('appProjectPiloting', {
    templateUrl: 'project-piloting.html',
    controller: ProjectPilotingController,
});