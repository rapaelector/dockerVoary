angular.module('projectApp').factory('projectService', ProjectService);

ProjectService.$inject = ['$http', 'fosJsRouting', 'PROJECT_ID'];

function ProjectService($http, fosJsRouting, PROJECT_ID) {
    var _this = this;

    _this.getProject = function(projectId) {
        return $http.get(fosJsRouting.generate('project.ng.get_project', { id: projectId }))
    };

    _this.getFormData = function() {
        return $http.get(fosJsRouting.generate('project.ng.form_data'));
    };

    _this.saveProject = function(projectId, formData) {
        const addresses = ['billingAddres', 'siteAddress'];
        for (const i in addresses) {
            if (formData[addresses[i]] && formData[addresses[i]].id) {
                delete(formData[addresses[i]].id);
            }
        }

        return $http.post(fosJsRouting.generate('project.ng.project_follow_up', { id: projectId }), formData);
    };

    /**
     * 
     * @param {object} project 
     * @returns {object} parsedProject
     */
    _this.parseProject = function(project) {
        var res = {...project };
        var map = ['businessCharge', 'contact', 'economist', 'ocbsDriver', 'prospect', 'recordAssistant', 'tceDriver'];
        var dateTypes = ['quoteValidatedMDEDate', 'depositeDateEdit'];
        var booleanTypes = ['roadmap', 'architect', 'paymentPercentage'];

        for (var item in res) {
            if (map.indexOf(item) > -1) {
                if (project[item] && project[item].id) {
                    res[item] = project[item].id;
                }
            }
            if (dateTypes.indexOf(item) > -1) {
                if (project[item]) {
                    res[item] = moment(project[item]).toDate();
                }
            }
            if (booleanTypes.indexOf(item) > -1) {
                if (project[item]) {
                    res[item] = '1';
                } else if (!project[item] && project[item] != null) {
                    res[item] = '0';
                }
            }
        }

        return res;
    };

    _this.parseEvents = function (events) {
        var res = [...events];
        
        if (res.length > 0) {
            for (var item in res) {
                if (res[item] && res[item].start) {
                    res[item].start = moment(res[item].start);
                }
                if (res[item] && res[item].end) {
                    res[item].end = moment(events[item].end);
                }
            }
        }

        console.info({res});
        return res;
    };

    _this.saveProjectPiloting = function(exchangeHistoryData) {
        var formData = _this.formatProjectPiloting(exchangeHistoryData);

        return $http.post(fosJsRouting.generate('project.ng.save_exchange_history', { id: PROJECT_ID }), formData);
    }

    _this.deleteProjectPiloting = function(id) {
        return $http.post(fosJsRouting.generate('project.ng.delete_exchange_history', { id: PROJECT_ID, exchangeHistory: id }));
    }

    _this.formatProjectPiloting = function(data) {
        var res = {...data };
        var dateType = ['date', 'relaunchDate', 'nexStepDate'];

        for (var item in res) {
            if (dateType.indexOf(item) > -1) {
                if (res[item]) {
                    res[item] = moment(res[item]).format('YYYY-MM-DD');
                }
            }
        }

        if (res.relaunchDate == null || res.relaunchDate == undefined) {
            delete(res.relaunchDate);
        }
        if (res.nextStepDate == null || res.nextStepDate == undefined) {
            delete(res.nextStepDate);
        }

        return res;
    }

    _this.getExchangeHistories = function(projectId) {
        var projectId = projectId ? projectId : PROJECT_ID;

        return $http.get(fosJsRouting.generate('project.ng.get_exchange_history', { id: projectId }));
    };

    /**
     * @param {int} projectId 
     * @param {string} route
     */
    _this.changeFolderStatus = function(route, projectId) {
        var projectId = projectId ? projectId : PROJECT_ID;

        return $http.post(fosJsRouting.generate(route, { id: projectId }));
    };

    return _this;
};