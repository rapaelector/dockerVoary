angular.module('projectListApp').controller('projectListController', [
    '$scope', 
    '$mdDialog',
    '$mdToast',
    'coreProjectScheduleService',
    'projectListService',
    function (
        $scope,
        $mdDialog,
        $mdToast,
        coreProjectScheduleService,
        projectListService
    ) {
    
    $scope.helloProjectList = null;
    $scope.loading = false;

    this.$onInit = () => {
        $scope.helloProjectList = 'project list app run successfull';
        $('body').on('valueChange', function (e, data) {
            var resource = {id: data.id};

            coreProjectScheduleService.showUpdateModal(resource, {}, e);
            $('body').trigger('redraw-datatable');
        });

        $('body').on('click',  '.add-to-planning', function (e, data) {
            var id = $(this).data('id');

            coreProjectScheduleService.showUpdateModal({id}, {}, e);
        })

        $('body').on('click', '.remove-to-planning', function (e, data) {
            var id = $(this).data('id');
            
            $scope.removeToPlanning(id, e);
        })
    };

    $scope.removeToPlanning = (projectId, ev) => {
        var confirm = $mdDialog.confirm()
            .title('Enlever le projet dans le planning')
            .textContent('Souhaitez-vous enlever ce projet au planning?')
            .ariaLabel('Lucky day')
            .targetEvent(ev)
            .ok('OK')
            .cancel('Annuler')
        ;

        $mdDialog.show(confirm).then(function () {
            $scope.loading = true;
            projectListService.removeToPlanning(projectId).then((response) => {
                $scope.showNotification(response.data.messages, 'toast-success');
                $scope.loading = false;
                /**
                 * Dispatch redraw-datatable when adding order book
                 */
                $('body').trigger('redraw-datatable');
                
            }, errors => {
                $scope.showNotification(errors.data.messages, 'toast-error');
                console.warn(errors.data.messages);
                $scope.loading = false;
            });
        }, function () {
            $scope.loading = false;
        });
    };

    $scope.showNotification = (message, toastClass) => {
        $mdToast.show(
            $mdToast.simple()
            .textContent(message)
            .position('top right')
            .hideDelay(5000)
            .toastClass(toastClass)
        ).then(() => {
            console.info('Toast dismissed');
        }).catch(() => {
            console.info('failed to laod md toast');
        });
    };

    $scope.confianceChanged = (projectId, confianceValue) => {
        console.info({projectId, confianceValue});
    };
}]);