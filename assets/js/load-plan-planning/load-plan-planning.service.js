angular.module('loadPlanPlanningApp').factory('loadPlanPlanningService', ['$http', 'fosJsRouting', function ($http, fosJsRouting) {
    var _this = this;

    _this.getResources = () => {
        return $http.get(fosJsRouting.generate('load_plan_planning.resources'));
    };

    return _this;
}]);