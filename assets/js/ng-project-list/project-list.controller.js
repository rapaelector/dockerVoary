angular.module('projectListApp').controller('projectListController', [
    '$scope', 
    '$mdDialog',
    'coreProjectScheduleService',
    function (
        $scope,
        $mdDialog,
        coreProjectScheduleService
    ) {
    
    $scope.helloProjectList = null;

    this.$onInit = () => {
        $scope.helloProjectList = 'project list app run successfull';
        $('body').on('valueChange', function (e, data) {
            var resource = {id: data.id};

            coreProjectScheduleService.showUpdateModal(resource, {}, e);
        });
    };

    $scope.confianceChanged = (projectId, confianceValue) => {
        console.info({projectId, confianceValue});
    };
}]);