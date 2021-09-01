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
    'TYPE_DEADLINE',
    'TYPE_STUDY_WEEK_SUBMISSION',
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
    TYPE_DEADLINE,
    TYPE_STUDY_WEEK_SUBMISSION,
    moment
) {
    $scope.data = {
        projects: [],
        errors: {},
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
    $scope.formName = '';
    $scope.excludeField = ['project', 'end'];

    this.$onInit = () => {
        $scope.formName = 'loadPlanForm';
        $scope.config.modalTitle = MESSAGES ? MESSAGES.modalTitle.new : 'Créer une plan de charge économiste';
        $scope.config.mode = 'create';
        loadPlanService.getConfig().then((response) => {
            $scope.config.taskTypes = response.data.taskTypes;
            $scope.config.studyTime = response.data.studyTime;
            $scope.config.types = response.data.types;
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
                console.info({selectedProject: $scope.data.selectedProject, project: $scope.form.project});
                // $scope.selectedProjectChange(response.data.project);
            }, error => console.warn(error));
        }

        $scope.projectCanceller = $q.defer();
    };

    $scope.$watch('form', function (newValue, oldValue) {
        var changedFields = [];
        for (var field in newValue) {
            try {
                if (newValue[field] !== oldValue[field]) {
                    changedFields.push(field);
                }
            } catch (e) {
                console.warn(e);
            }
        }
        $scope.setFormValidity(changedFields, true);

    }, true);

    /**
     * Save load plan
     * If form end dat is before form start date then force the end date to the start date
     * 
     * @param {jsEvent} event 
     */
    $scope.saveLoadPlan = (event) => {
        $scope.loading = true;
        // start = N° semaine pour remise de l'etude
        $scope.form.start =  $scope.form.start ? moment($scope.form.start).startOf('week').format('YYYY-MM-DD') : null;
        $scope.form.end = $scope.form.end ? moment($scope.form.start).endOf('week').format('YYYY-MM-DD') : null;
        $scope.form.deadline = $scope.form.deadline ? moment($scope.form.deadline).format('YYYY-MM-DD') : null;
        $scope.form.realizationQuotationDate = $scope.form.realizationQuotationDate ? moment($scope.form.realizationQuotationDate).format('YYYY-MM-DD') : null;

        if ($scope.form.type === TYPE_DEADLINE) {
            $scope.form.start = $scope.form.deadline;
        }

        if ($scope.form.type === TYPE_STUDY_WEEK_SUBMISSION) {
            $scope.form.deadline = null;
        }

        loadPlanService.saveLoadPlan($scope.form, $scope.config.mode).then((response) => {
            var fields = ['deadline', 'effectiveStudyTime', 'estimatedStudyTime', 'natureOfTheCosting', 'realizationQuotationDate', 'start'];
            $mdDialog.hide();
            $scope.loading = false;
            $scope.showNotification(response.data.message);
            $scope.setFormValidity(fields, true);
            window.location.reload();
        }, errors => {
            $scope.loading = false;
            $scope.data.errors[$scope.formName] = errors.data.errors;
            $scope.showNotification(errors.data.message, {toastClass: 'toast-error'});
            $scope.setFormValidity(Object.keys(errors.data.errors), false);
        });
    };

    $scope.setFormValidity = (fields, isValid) => {
        for (var field of fields) {
            try {
                if ($scope.excludeField.indexOf(field) < 0) {
                    $scope.loadPlanForm[field].$setValidity('serverErrors', isValid);
                    $scope.loadPlanForm[field].$setDirty();
                }
            } catch (e) {
                console.warn(e);
            }
        }
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