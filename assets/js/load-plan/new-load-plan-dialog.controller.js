LoadPlanDialogController.$inject = [
    '$scope',
    '$mdDialog',
    '$mdToast',
    '$element',
    '$q',
    'loadPlanService',
    'resolverService',
    'options',
    'MESSAGES',
];

function LoadPlanDialogController (
    $scope, 
    $mdDialog, 
    $mdToast, 
    $element, 
    $q, 
    loadPlanService, 
    resolverService,
    options,
    MESSAGES,
) {
    $scope.data = {
        projects: [],
        errors: [],
    };
    $scope.loading = false;
    $scope.form = {
        project: null,
        natureOfTheCosting: null,
        weekNumber: null,
        start: null,
        end: null,
    };
    $scope.config = {
        taskTypes: [],
        modalTitle: '',
        mode: '',
    };
    $scope.projectCanceller = null; 

    this.$onInit = () => {
        $scope.config.modalTitle = MESSAGES ? MESSAGES.modalTitle.new : 'Créer une plan de charge économiste';
        $scope.config.mode = 'create';

        loadPlanService.getConfig().then((response) => {
            $scope.config.taskTypes = response.data.taskTypes;
        });
        if (options && options.mode === 'edit' && options.id) {
            $scope.config.mode = options.mode;
            $scope.config.modalTitle = MESSAGES.modalTitle.edit;
            $scope.loading = true;
            loadPlanService.getLoadPlan(options.id).then((response) => {
                $scope.loading = false;
                $scope.form = response.data;
                $scope.selectedProjectChange(response.data);
            }, error => console.warn(error));
        }

        $scope.projectCanceller = $q.defer();
    };

    /**
     * Save load plan
     * If form end dat is before form start date then force the end date to the start date
     * 
     * @param {jsEvent} event 
     */
    $scope.saveLoadPlan = (event) => {
        $scope.loading = true;
        if (moment($scope.form.end).isBefore(moment($scope.form.start))) {
            $scope.form.end = $scope.form.start;
        }

        loadPlanService.saveLoadPlan($scope.form, $scope.config.mode).then((response) => {
            $mdDialog.hide();
            $scope.loading = false;
            $scope.showNotification(response.data.message);
            window.location.reload();
        }, errors => {
            $mdDialog.hide();
            $scope.loading = false;
            $scope.data.errors = errors.errors;
            console.warn(errors);
            $scope.showNotification(response.data.message, {toastClass: 'toast-error'});
        });
    };

    $scope.cancel = (event) => {
        $mdDialog.hide();
    }

    /**
     * Show notification with given message and options
     * 
     * @param {string} message 
     * @param {object} options 
     */
    $scope.showNotification = (message, options) => {
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
            .toastClass(options.toastClass)
        ).then(() => {
            console.info('Toast dismissed');
        }).catch(() => {
            console.info('failed to laod md toast');
        });
    };

    $element.find('input').on('keydown', (ev) => {
        ev.stopPropagation();
    });
	
	/**
     * search for project
     * remote dataservice call.
     */
    $scope.queryProjectSearch = (query) => {
        $scope.projectCanceller.resolve();
        $scope.projectCanceller = $q.defer();
        var config = {
            timeout: $scope.projectCanceller.promise
        };
		if (query == undefined) {
			query = '';
		}

        return loadPlanService.getProjects(query, config);
    }

	$scope.selectedProjectChange = (item) => {
		if (item) {
			$scope.form.project = item.id;
			// $scope.data.selectedProject = item.name + ' ' + resolverService.resolve([item, 'prospect', 'clientNumber'], null);
		}
    };
};

export default LoadPlanDialogController;