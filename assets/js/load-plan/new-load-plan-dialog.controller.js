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
    'moment',
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
    moment
) {
    $scope.data = {
        projects: [],
        errors: [],
        selectedProject: null,
    };
    $scope.loading = false;
    $scope.form = {
        project: null,
        natureOfTheCosting: null,
        estimatedStudyTime: null,
        estimatedStudyTime: null,
        effectiveStudyTime: null,
        // Date type
        start: null,
        deadline: null,
        realizationQuotationDate: null,
    };
    $scope.config = {
        taskTypes: [],
        studyTime: [],
        modalTitle: '',
        mode: '',
    };
    $scope.projectCanceller = null; 

    this.$onInit = () => {
        $scope.config.modalTitle = MESSAGES ? MESSAGES.modalTitle.new : 'Créer une plan de charge économiste';
        $scope.config.mode = 'create';

        loadPlanService.getConfig().then((response) => {
            $scope.config.taskTypes = response.data.taskTypes;
            $scope.config.studyTime = response.data.studyTime;
        });
        if (options && options.mode === 'edit' && options.id) {
            $scope.config.mode = options.mode;
            $scope.config.modalTitle = MESSAGES.modalTitle.edit;
            $scope.loading = true;
            loadPlanService.getLoadPlan(options.id).then((response) => {
                $scope.loading = false;
                $scope.form = response.data;
                // Restore the project when edit the load plan
                $scope.data.selectedProject = $scope.form.project;
                // $scope.selectedProjectChange(response.data.project);
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
        // start = N° semaine pour remise de l'etude
        $scope.form.start = moment($scope.form.start).startOf('week').format('YYYY-MM-DD');
        $scope.form.end = moment($scope.form.start).endOf('week').format('YYYY-MM-DD');
        $scope.form.deadline = moment($scope.form.deadline).format('YYYY-MM-DD');
        $scope.form.realizationQuotationDate = moment($scope.form.realizationQuotationDate).format('YYYY-MM-DD');

        console.info($scope.form.start);
        console.info($scope.form.end);

        loadPlanService.saveLoadPlan($scope.form, $scope.config.mode).then((response) => {
            $mdDialog.hide();
            $scope.loading = false;
            $scope.showNotification(response.data.message);
            window.location.reload();
        }, errors => {
            $scope.loading = false;
            console.warn(errors);
            $scope.data.errors = errors.data.errors;
            $scope.showNotification(errors.data.message, {toastClass: 'toast-error'});
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
        console.info($scope.data.selectedProject);
		if (item) {
			$scope.form.project = item.id;
			// $scope.data.selectedProject = item.name + ' ' + resolverService.resolve([item, 'prospect', 'clientNumber'], null);
		}
    };
};

export default LoadPlanDialogController;