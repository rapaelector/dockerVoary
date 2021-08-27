angular.module('projectListApp').controller('projectListController', [
    '$scope', 
    'coreProjectScheduleService', 
    function (
        $scope, 
        coreProjectScheduleService
    ) {
    
    $scope.helloProjectList = null;

    this.$onInit = () => {
        $scope.helloProjectList = 'project list app run successfull';
    };

    $scope.confianceChanged = (projectId, confianceValue) => {
        console.info({projectId, confianceValue});
    };

}]);