LoadPlanDialogController.$inject = [
    '$scope',
    '$mdDialog',
    '$mdToast',
    '$element',
    '$q',
    'loadPlanService',
    'resolverService',
    'notificationService',
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
    notificationService,
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
    $scope.onProjectLoding = false;
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
    $scope.project = '';
    $scope.formName = '';
    $scope.excludeField = [];
    $scope.projectSearchTerm = '';

    this.$onInit = () => {
        $scope.formName = 'loadPlanForm';
        $scope.config.modalTitle = MESSAGES ? MESSAGES.modalTitle.new : 'Ajouter un plan de charge';
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
            }, error => console.warn(error));
        }

        $scope.loading = true;
        loadPlanService.getProjects().then((response) => {
            $scope.loading = false;
            $scope.data.projects = response.data;
            $scope.form.project = options.id ? $scope.data.projects.find(item => item.id === 3).id : null;
        }, errors => {
            $scope.loading = false;
            notificationService.showToast('Erreur', {toastClass: 'toast-error'});
        });
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

    $scope.clearSearchTerm = function () {
        $scope.projectSearchTerm = '';
    };
    
    // The md-select directive eats keydown events for some quick select
    // logic. Since we have a search input here, we don't need that logic.
    $element.find('input').on('keydown', function (ev) {
        ev.stopPropagation();
    });

    /**
     * Save load plan
     * If form end dat is before form start date then force the end date to the start date
     * 
     * @param {jsEvent} event lo
     */
    $scope.saveLoadPlan = (event) => {
        $scope.loading = true;
        // start = N° semaine pour remise de l'etude
        $scope.form.start =  $scope.form.start ? moment($scope.form.start).startOf('week').format('YYYY-MM-DD') : null;
        $scope.form.end = $scope.form.start ? moment($scope.form.start).endOf('week').format('YYYY-MM-DD') : null;
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
            notificationService.showToast(response.data.message);
            $scope.setFormValidity(fields, true);
            $('body').trigger('load_plan.redraw-dt');
        }, errors => {
            $scope.loading = false;
            $scope.data.errors[$scope.formName] = errors.data.errors;
            console.info({merdeeeeeeeeeeeeeeeeeee: $scope.data.errors});
            notificationService.showToast(errors.data.message, {toastClass: 'toast-error'});
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

    /**
     * 
     * @param {jsEvent} event 
     */
    $scope.cancel = (event) => {
        $mdDialog.hide();
    }
};

export default LoadPlanDialogController;