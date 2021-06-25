function ProjectInformationController($scope) {
    $scope.project = {};

    $scope.fns = {};
    $scope.fns.submit = function() {
        console.info($scope.project);
    };
};

ProjectInformationController.$inject = ['$scope'];

angular.module('projectApp').component('appProjectInformation', {
    templateUrl: 'project-information.html',
    controller: ProjectInformationController,
});