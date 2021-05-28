angular.module('dashboardApp').controller('DashboardController', ['$scope', 'dashboardService', function($scope, dashboardService) {
    $scope.data = {
        hello: 'Hello world without errors'
    };

    this.$onInit = function () {
        $('#calendar').datetimepicker({
            format: 'L',
            locale: window._locale,
            inline: true
        });

        dashboardService.getBoxStatisticsData().then(function (response) {
            $scope.data.boxStats = response.data;
        })
    };
}]);