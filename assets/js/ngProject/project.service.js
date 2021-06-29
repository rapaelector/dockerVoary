angular.module('projectApp').factory('projectService', ProjectService);

ProjectService.$inject = ['$http', 'fosJsRouting'];

function ProjectService($http, fosJsRouting) {
    var _this = this;

    _this.getProject = function(projectId) {
        return $http.get(fosJsRouting.generate('project.ng.get_project', { id: projectId }))
    };

    _this.getFormAutoCompleteData = function() {
        return $http.get(fosJsRouting.generate('project.ng.form_autocomplete_data'));
    };

    _this.saveProject = function(projectId, formData) {
        console.info({ projectId, formData });

        return $http.post(fosJsRouting.generate('project.ng.project_edit', { id: projectId }), formData);
    };

    return _this;
};