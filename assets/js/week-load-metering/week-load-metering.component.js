import weekLoadMeteringTemplate from './week-load-metering-template.html';

function WeekLoadMeteringController ($scope, $mdDialog, $mdToast, weekLoadMeteringService) {
    $scope.data = {
        loadMetering: [],
    };

    this.$onInit = () => {
        weekLoadMeteringService.getWeekLoadMetering().then((response) => {
            $scope.data.loadMetering = response.data;
        }, errors => {
            console.info({errors});
        })
    };
};

WeekLoadMeteringController.$inject = ['$scope', '$mdDialog', '$mdToast', 'weekLoadMeteringService'];

angular.module('weekLoadMeteringModule').component('appWeekLoadMetering', {
    template: weekLoadMeteringTemplate,
    controller: WeekLoadMeteringController,
});