function BoxStatisticsController ($scope) {
}

BoxStatisticsController.$inject = ['$scope'];

angular.module('dashboardApp').component('appBoxStatistics', {
    templateUrl: 'box-statistics.html',
    controller: BoxStatisticsController,
    bindings: {
        label: '=',
        data: '=',
        unit: '=',
        icon: '=',
        iconBackgroundClass: '=',
        url: '=',
    }
});