angular.module('loadPlanApp').factory('loadPlanService', [
    '$http', 
    '$mdToast', 
    'fosJsRouting', 
    'resolverService', 
    'DATE_FORMAT', 
    function ($http, $mdToast, fosJsRouting, resolverService, DATE_FORMAT) {

    var _this = this;

    _this.saveLoadPlan = (formData, mode) => {
        // var formattedData = _this.formatFormData(formData);
        var formattedData = formData;

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
    
    /**
     * Economist autocomplete
     * 
     * @param {string} economistName 
     * @param {object} config 
     * @param {any} key 
     * @returns promise
     */
    _this.getEconomists = function (economistName, config, key) {
        var timers = [];

		return new Promise(function (resolve, reject) {
			timers[key] = setTimeout(function () {
                $http.get(Routing.generate('load_plan.economists', {
                    q: economistName
                }), config).then(function (response) {
                    resolve(response.data);
                }).catch(function (error) {
                    resolve([]);
                });
            }, 500);
        });
	};

    /**
     * 
     * @param {object} item 
     * @param {number} projectId 
     */
    _this.saveProjectEconomist = (item, projectId) => {
        return $http.post(fosJsRouting.generate('load_plan.change_project_economist', {id: projectId}), item);
    };
        
    /**
     * 
     * @param {number} loadPlanId 
     * @param {object} formData 
     * @returns {promise}
     */
    _this.updateRealizationDate = (loadPlanId, formData) => {
        if (formData && formData.realizationDate) {
            formData.realizationDate = moment(formData.realizationDate, 'YYYY-MM-DD').format('YYYY-MM-DD');
        }
        
        return $http.post(fosJsRouting.generate('load_plan.update_realization_quotation_date', {id: loadPlanId}), formData);
    };

    /**
     * 
     * @param {number} loadPlanId 
     * @param {object} formData 
     * @returns {promise}
     */
    _this.updateDeadlineDate = (loadPlanId, formData) => {
        return $http.post(fosJsRouting.generate('load_plan.update_deadline_date', {id: loadPlanId}), formData);
    };

    /**
     * Show notification with given message and options
     * 
     * @param {string} message 
     * @param {object} options 
     */
    _this.showNotification = (message, toastClass) => {
        if (options == undefined || options == {}) {
            var options = {
                toastClass: 'toast-success',
            };
        }

        $mdToast.show(
            $mdToast.simple()
            .textContent(message)
            .position('top right')
            .hideDelay(5000)
            .toastClass(toastClass)
        ).then(() => {
            console.info('Toast dismissed');
        }).catch(() => {
            console.info('failed to laod md toast');
        });
    };

    _this.saveWeekNumber = (loadPlanId, formData) => {
        return $http.post(fosJsRouting.generate('load_plan.update_start_date', {id: loadPlanId}), formData);
    };

    return _this;
}]);