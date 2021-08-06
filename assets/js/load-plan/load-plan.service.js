angular.module('loadPlanApp').factory('loadPlanService', ['$http', 'fosJsRouting', 'resolverService', 'DATE_FORMAT', function ($http, fosJsRouting, resolverService, DATE_FORMAT) {
    var _this = this;

    _this.saveLoadPlan = (formData, mode) => {
        var formattedData = _this.formatFormData(formData);

        return mode === 'edit' && formattedData.id ? $http.post(fosJsRouting.generate('load_plan.edit', {id: formattedData.id}), formattedData) : $http.post(fosJsRouting.generate('load_plan.new'), formattedData);
    };

    /**
     * Format date in form data to be a valid date
     * Change moment to date
     * 
     * @param {object} formData 
     * @returns {object} formattedData
     */
    _this.formatFormData = (formData) => {
        var formattedData = formData;
        formattedData.start = moment(formattedData.start);
        formattedData.end = moment(formattedData.end);

        return formattedData;
    };

    _this.getConfig = () => {
        return $http.get(fosJsRouting.generate('load_plan.config'));
    };

    /**
     * 
     * @param {number} projectId 
     * @returns 
     */
    _this.getLoadPlan = (projectId) => {
        return $http.get(fosJsRouting.generate('load_plan.get_load_plan', {id: projectId}));
    };

    /**
     * 
     * @param {string} projectName 
     * @param {object} config 
     * @param {any} key 
     * @returns promise
     */
    _this.getProjects = function (projectName, config, key) {
        var timers = [];

		return new Promise(function (resolve, reject) {
			timers[key] = setTimeout(function () {
                $http.get(Routing.generate('load_plan.projects', {
                    q: projectName
                }), config).then(function (response) {
                    resolve(response.data);
                }).catch(function (error) {
                    resolve([]);
                });
            }, 500);
        });
	};

    return _this;
}]);