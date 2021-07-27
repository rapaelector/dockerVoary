angular.module('projectScheduleApp').factory('projectSchedulerService', ['$http', 'fosJsRouting', function ($http, fosJsRouting) {
    var _this = this;

    _this.getResources = function () {
        return $http.get(fosJsRouting.generate('project_schedule.get_resources'));
    }
    
    return _this;    
}])