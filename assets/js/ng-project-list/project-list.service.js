angular.module('projectListApp').factory('projectListService', ['$http', 'fosJsRouting', function ($http, fosJsRouting) {
    var _this = this;

    _this.removeToPlanning = (projectId) => {
        return $http.post(fosJsRouting.generate('project.ng.remove_to_planning', {id: projectId}, {}));
    };

    return _this;
}])