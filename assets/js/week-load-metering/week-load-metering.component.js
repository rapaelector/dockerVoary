import weekLoadMeteringTemplate from './week-load-metering-template.html';

function WeekLoadMeteringController ($scope, $mdDialog, $mdToast, weekLoadMeteringService) {
    $scope.data = {
        loadMetering: [],
    };
    $scope.loading = null;

    this.$onInit = () => {
        $scope.updateLoadMetering();
    };

    $scope.$watch('$ctrl.loadMeteringDate', function () {
        $scope.updateLoadMetering($scope.$ctrl.loadMeteringDate);
    });

    $scope.updateLoadMetering = (date) => {
        $scope.loading = true;
        weekLoadMeteringService.getWeekLoadMetering(date).then((response) => {
            $scope.data.loadMetering = response.data;
            $scope.loading = false;
        }, errors => {
            console.info({errors});
            $scope.loading = false;
        })
    };
};

WeekLoadMeteringController.$inject = ['$scope', '$mdDialog', '$mdToast', 'weekLoadMeteringService'];

angular.module('weekLoadMeteringModule').component('appWeekLoadMetering', {
    template: weekLoadMeteringTemplate,
    controller: WeekLoadMeteringController,
    bindings: {
        /**
         * Date
         */
        loadMeteringDate: '=',
    },
});