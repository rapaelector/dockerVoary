import schedulerTemplate from './template.html';

angular.module('schedulerModule', [
    'ngMaterial',
    'ngMessages',
]);

function SchedulerController($scope, $mdDialog) {};

SchedulerController.$inject = ['$scope', '$mdDialog'];

angular.module('schedulerModule').component('appScheduler', {
    template: schedulerTemplate,
    controller: SchedulerController,
    bindings: {
        ressources: '=',
    }
});