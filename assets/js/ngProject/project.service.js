angular.module('projectApp').factory('ProjectService', ProjectService);

ProjectService.$inject = ['$http', 'fosJsRouting'];

function ProjectService($http, fosJsRouting) {
    var _this = this;

    _this.getProject = function(projectId) {
        return $http.get(fosJsRouting.generate('project.ng.get_project', { id: projectId }))
    };

    _this.getFormAutoCompleteData = function() {
        return $http.get(fosJsRouting.generate('project.ng.form_autocomplete_data'));
    };

    return _this;
};