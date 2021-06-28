function ProjectInformationController($scope, CASE_TYPES) {
    $scope.project = {};
    $scope.data = {
        caseType: [],
    };
    $scope.caseTypes = {};

    this.$onInit = function() {
        $scope.caseTypes = CASE_TYPES;
    };

    $scope.toggle = function(item) {
        var idx = $scope.data.caseType.indexOf(item);
        if (idx > -1) {
            $scope.data.caseType.splice(idx, 1);
        } else {
            $scope.data.caseType.push(item);
        }
        console.info($scope.data.caseType);
    };

    $scope.fns = {};
    $scope.fns.submit = function() {
        console.info($scope.project);
    };
};

ProjectInformationController.$inject = ['$scope', 'CASE_TYPES'];

angular.module('projectApp').component('appProjectInformation', {
    templateUrl: 'project-information.html',
    controller: ProjectInformationController,
});