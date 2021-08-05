angular.module('loadPlanApp').factory('loadPlanService', ['$http', 'fosJsRouting', function ($http, fosJsRouting) {
    var _this = this;

    _this.getProjects = () => {
        return $http.get(fosJsRouting.generate('load_plan.projects'));
    };

    _this.saveLoadPlan = (formData) => {
        return $http.post(fosJsRouting.generate('load_plan.new'), formData);
    };

    return _this;
}]);