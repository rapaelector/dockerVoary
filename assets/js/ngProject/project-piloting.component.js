function ProjectPilotingController($scope, projectService) {
    $scope.data = {};

    $scope.$init = function() {
        projectService.getExchangeHistory().then((response) => {
            $scope.data.exchangeHistory = response.data.exchangeHistory;
        }, error => {
            console.info('failed to load exchangeHistory');
        })
    }
}

ProjectPilotingController.$inject = ['$scope', 'projectService'];

angular.module('projectApp').component('appProjectPiloting', {
    templateUrl: 'project-piloting.html',
    controller: ProjectPilotingController,
});