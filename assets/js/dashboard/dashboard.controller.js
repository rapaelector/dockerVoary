angular.module('dashboardApp').controller('DashboardController', ['$scope', 'dashboardService', function($scope, dashboardService) {
    $scope.data = {
        hello: 'Hello world without errors'
    };

    this.$onInit = function () {
        console.info($);

        $('#calendar').datetimepicker({
            format: 'L',
            inline: true
        });

        dashboardService.getBoxStatisticsData().then(function (response) {
            $scope.data.boxStats = response.data;
            console.info($scope.data.boxStat);
        })
    };
}]);