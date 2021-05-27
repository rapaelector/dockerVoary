angular.module('dashboardApp').factory('dashboardService', ['$http', 'fosJsRouting', function ($http, fosJsRouting) {
    var _this = {};

    _this.getBoxStatisticsData = function () {
        return $http.get(fosJsRouting.generate('dashboard.get.box_stats'));
    };

    return _this;
}])