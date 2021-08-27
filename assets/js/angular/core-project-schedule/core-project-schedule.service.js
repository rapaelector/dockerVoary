import coreProjectSchedulerTemplate from './template.html';
import CoreProjectSchedulerModalController from './core-project-scheduler-modal-controller';

angular.module('coreProjectScheduleModule').factory('coreProjectScheduleService', [
    '$http',
    '$mdPanel',
    'fosJsRouting',
    function (
        $http,
        $mdPanel,
        fosJsRouting,
    ) {
    var _this = this;

    /**
     * 
     * @param {Resource} resource 
     * @param {Column} column 
     * @param {jsEvent} ev 
     */
    _this.showUpdateModal = (resource, column, ev) => {
        _this.createModalConfig({resource, column, ev}).then((config) =>  {
            $mdPanel.open(config);
        });
    };

    /**
     * 
     * @param {jsEvent} ev 
     */
    _this.showCreateModal = (ev) => {
        _this.createModalConfig({ev}).then((config) => {
            $mdPanel.open(config);
        });

    };

    /**
     * 
     * @param {object} options
     * 
     * @returns {object} config
     */
    _this.createModalConfig = (options) => {
        var position = $mdPanel.newPanelPosition().absolute().center();

        return _this.getConfig().then((response) => {
            return {
                attachTo: angular.element(document.body),
                controller: CoreProjectSchedulerModalController,
                controllerAs: 'ctrl',
                template: coreProjectSchedulerTemplate,
                panelClass: 'order-book-panel',
                position: position,
                hasBackdrop: true,
                disableParentScroll: false,
                locals: {
                    options: {
                        modalTitle: (options && options.resource && options.resource.id) ? MESSAGES.orderBookModalEditTitle : MESSAGES.orderBookModalAddTitle,
                        marketTypes: response.marketTypes,
                        types: response.projectTypes,
                    },
                    resource: options.resource,
                    column: options.column,
                },
                openFrom: options.ev,
                clickOutsideToClose: true,
                escapeToClose: true,
                focusOnOpen: false,
                zIndex: 1000,
                onCloseSuccess: (mdPanelRef, columns) => {},
            };
        });
    };

    /**
     * 
     * @param {number} projectId 
     * @returns {promise}
     */
    _this.getProject = (projectId) => {
        return $http.get(fosJsRouting.generate('project.ng.project', {id: projectId}));
    };
    
    /**
     * 
     * @param {number} projectId
     * @param {object} formData 
     * @return {promise}
     */
    _this.updateProject = (projectId, formData) => {
        return $http.post(fosJsRouting.generate('project.ng.update_project', {id: projectId}), formData);
    };
    

    _this.createProject = (formData) => {
        return $http.post(fosJsRouting.generate('project.ng.create_project'), formData)
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
                $http.get(fosJsRouting.generate('load_plan.projects', {
                    q: projectName
                }), config).then(function (response) {
                    resolve(response.data);
                }).catch(function (error) {
                    resolve([]);
                });
            }, 500);
        });
	};

    _this.getConfig = function () {
        return $http.get(fosJsRouting.generate('project.ng.project_config')).then((response) => {
            return {
                marketTypes: response.data.marketTypes,
                projectTypes: response.data.projectTypes,
            };
        });
    };

    return _this;
}]);