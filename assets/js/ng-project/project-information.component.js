import ContactCreationController from './contact-creation.controller.js';
import ArchiveProjectDialogController from './archive-project-dialog.controller.js';

function ProjectInformationController(
    $scope,
    $mdToast,
    $mdDialog,
    $mdPanel,
    projectService,
    resolverService,
    CASE_TYPES,
    PRIORIZATION_FILE_TYPES,
    MARKET_TYPES,
    SECOND_MARKET_TYPES,
    TYPE_BONHOME,
    PROJECT_ID,
    APP_MESSAGES,
    PROJECT_EVENT_TYPES,
) {
    $scope.onLoading = true;
    $scope.project = {
        caseType: [],
        disaSheetValidation: [],
    };
    $scope.data = {
        users: [],
        caseType: [],
        disaSheetsValidation: [],
        project: null,
        errors: null,
        allowedActions: [],
        statusLabel: '',
        statusBg: '',
        events: [],
    };
    $scope.eventTypes = PROJECT_EVENT_TYPES,
    $scope.searchTerm = {
        users: '',
        clients: '',
        economists: '',
        contact: '',
        businessCharge: '',
        countries: '',
        recordAssistant: '',
        ocbsDriver: '',
        tceDriver: '',
    };
    $scope.options = {
        dateRangePicker: {},
    };

    this.$onInit = function() {
        $scope.onLoading = true;
        $scope.caseTypes = CASE_TYPES;
        $scope.priorizationFileTypes = PRIORIZATION_FILE_TYPES;
        $scope.marketTypes = MARKET_TYPES;
        $scope.secondMarketTypes = SECOND_MARKET_TYPES;
        $scope.typeBonhome = TYPE_BONHOME;

        projectService.getProject(PROJECT_ID).then((response) => {
            $scope.data.project = response.data;
            $scope.project = projectService.parseProject(response.data.project);
            $scope.data.events = projectService.parseEvents(resolverService.resolve([response, 'data', 'project', 'events'], []));
            $scope.helpers.updateStatus($scope.project)
            if (response.data && response.data.project && response.data.project.allowedActions) {
                $scope.data.allowedActions = response.data.project.allowedActions;
            }
        }, error => {
            console.info(error);
        })
        projectService.getFormData().then((response) => {
            $scope.data.clients = response.data.clients;
            $scope.data.users = response.data.users;
            $scope.data.economists = response.data.economists;
            $scope.data.businessCharge = response.data.businessCharge;
            $scope.data.countries = response.data.countries;
            $scope.data.caseTypes = response.data.caseTypes;
            $scope.data.disaSheetsValidation = response.data.disaSheetsValidation;
            $scope.data.priorizationOfFileFormatted = response.data.priorizationOfFileFormatted;
            $scope.onLoading = false;
        }, error => {
            console.info(error);
            $scope.onLoading = false;
        });

        $scope.options.dateRangePicker = {
            autoApply: true,
            autoClose: true,
            locale: {
                format: 'DD/MM/YYYY'
            },
            ranges: {
                "Aujourd'hui": [moment(), moment()],
                "Hier": [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                "Les 7 derniers jours": [moment().subtract(6, 'days'), moment()],
                "Les 30 derniers jours": [moment().subtract(29, 'days'), moment()],
                "Ce mois": [moment().startOf('month'), moment().endOf('month')],
                "Le mois dernier": [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
        };
    };

    $scope.$watch('data.newContact', function() {
        if ($scope.data.newContact) {
            projectService.createContact().then((response) => {
                $scope.project.contact = response.id;
            }, error => {
                console.info(error);
            })
        };
    });

    $scope.$watch('project', function() {}, true);

    $scope.$watch('data.caseTypes', function() {}, true);

    $scope.$watch('data.status', function() {
        if ($scope.data.status == 'submitted') {
            $scope.data.statusBg = 'bg-app-secondary';
        } else if ($scope.data.status == 'validated') {
            $scope.data.statusBg = 'bg-success';
        } else if ($scope.data.status == 'lost') {
            $scope.data.statusBg = 'bg-danger';
        };
    }, true);

    $scope.$watch('data.events', function () {}, true);
    
    $scope.$watch('event.start', function () {}, true);
    
    $scope.$watch('event.end', function () {}, true);

    $scope.helpers = {};
    $scope.helpers.getUsers = function() {
        $scope.data.users = projectService.getFormData().then((response) => {
            return response.data.users;
        }, error => {
            console.info(error);
        })
    };
    $scope.helpers.handleUserChanged = function() {};

    $scope.helpers.getClents = function() {
        $scope.data.users = projectService.getFormData().then((response) => {
            return response.data.clients;
        }, error => {
            console.info(error);
        })
    };
    $scope.helpers.handleClientChanged = function() {};

    $scope.helpers.getEconomists = function() {
        $scope.data.users = projectService.getFormData().then((response) => {
            return response.data.economists;
        }, error => {
            console.info(error);
        })
    };
    $scope.helpers.handleEconomistsChanged = function() {};
    $scope.helpers.getBusinessCharge = function() {
        $scope.data.users = projectService.getFormData().then((response) => {
            return response.data.economists;
        }, error => {
            console.info(error);
        })
    };
    $scope.helpers.handleBusinessChargeChanged = function() {};
    $scope.helpers.getCountries = function() {
        $scope.data.countries = projectService.getFormData().then((response) => {
            return response.data.countries;
        }, error => {
            console.info(error);
        });
    };
    $scope.helpers.handleCountriesChanged = function() {};
    $scope.helpers.getRecordAssistant = function() {
        $scope.data.countries = projectService.getFormData().then((response) => {
            return response.data.users;
        }, error => {
            console.info(error);
        });
    };
    $scope.helpers.handleRecordAssistantChanged = function() {};
    $scope.helpers.getOcbsDriver = function() {
        $scope.data.countries = projectService.getFormData().then((response) => {
            return response.data.users;
        }, error => {
            console.info(error);
        });
    };
    $scope.helpers.handleOcbsDriverChanged = function() {};

    $scope.helpers.getTceDriver = function() {
        $scope.data.countries = projectService.getFormData().then((response) => {
            return response.data.users;
        }, error => {
            console.info(error);
        });
    };
    $scope.helpers.handleTceDriverChanged = function() {};
    $scope.helpers.showSimpleToast = function(message, options) {
        if (options == null || options == undefined) {
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
        ).then(function() {
            console.info('Toast dismissed');
        }).catch(function() {
            console.info('failed to laod md toast');
        });
    };
    $scope.toggle = function(item) {
        if ($scope.project.caseType == undefined || $scope.project.caseType == null) {
            $scope.project.caseType = [];
        }
        var idx = $scope.project.caseType.indexOf(item.value);
        if (idx > -1) {
            $scope.project.caseType.splice(idx, 1);
        } else {
            $scope.project.caseType.push(item.value);
        }
    };
    $scope.toggleDisaFiles = function(item) {
        if ($scope.project.disaSheetValidation == undefined || $scope.project.disaSheetValidation == null) {
            $scope.project.disaSheetValidation = [];
        }
        var idx = $scope.project.disaSheetValidation.indexOf(item.value);
        if (idx > -1) {
            $scope.project.disaSheetValidation.splice(idx, 1);
        } else {
            $scope.project.disaSheetValidation.push(item.value);
        }
    };
    $scope.helpers.updateStatus = function(data) {
        if (data) {
            $scope.data.statusLabel = data.statusLabel;
            $scope.data.status = data.status;
        }
    };

    $scope.fns = {};
    $scope.fns.submit = function() {
        $scope.onLoading = true;
        $scope.data.errors = {};
        $scope.project.events = $scope.data.events;
        projectService.saveProject(PROJECT_ID, $scope.project).then((response) => {
            $scope.onLoading = false;
            $scope.data.statusLabel = response.data.data.statusLabel;
            $scope.data.status = response.data.data.status;
            $scope.helpers.showSimpleToast(response.data.message);
        }, error => {
            $scope.onLoading = false;
            $scope.data.errors = error.data.errors;
            $scope.helpers.showSimpleToast(error.data.message, { toastClass: 'toast-error' });
        })
    };
    $scope.fns.showDialog = function(jsEvent) {
        $mdDialog.show({
            controller: ContactCreationController,
            templateUrl: 'contact-dialog-form.html',
            disableParentScroll: true,
            parent: angular.element(document.body),
            targetEvent: jsEvent,
            clickOutsideToClose: true,
            bindToController: false,
        }).then((response) => {
            if (response) {
                $scope.data.users.push(response);
                $scope.project.contact = response.id;
            }
        }, error => {
            console.error({ error });
        });
    }
    $scope.fns.submitProject = function(jsEvent) {
        var confirm = $mdDialog.confirm()
            .title(APP_MESSAGES.folderValidationTitle)
            .textContent(APP_MESSAGES.folderValidationMessage)
            .ariaLabel('folder validation')
            .targetEvent(jsEvent)
            .ok(APP_MESSAGES.action.send)
            .cancel(APP_MESSAGES.action.cancel);

        $mdDialog.show(confirm).then(function() {
            $scope.onLoading = true;
            projectService.changeFolderStatus('project.ng.submit_project').then((response) => {
                $scope.helpers.showSimpleToast(response.data.message);
                $scope.helpers.updateStatus(response.data.data);
                $scope.data.allowedActions = response.data.data.allowedActions;
                $scope.onLoading = false;
            }, error => {
                $scope.onLoading = false;
                $scope.helpers.showSimpleToast(error.data.message, { toastClass: 'toast-error' });
            })
        }, function(error) {
            $scope.onLoading = false;
        });
    }
    $scope.fns.archive = function(jsEvent) {
        $mdDialog.show({
            controller: ArchiveProjectDialogController,
            templateUrl: 'archive-project-dialog.html',
            disableParentScroll: true,
            parent: angular.element(document.body),
            targetEvent: jsEvent,
            clickOutsideToClose: true,
            bindToController: false,
        }).then((response) => {
            if (response) {
                $scope.project = response.data;
                $scope.helpers.updateStatus(response.data);
                $scope.helpers.showSimpleToast(response.message);
                $scope.data.allowedActions = response.data.allowedActions;
            }
        }, error => {
            // $scope.onLoading = false;
            $scope.helpers.showSimpleToast(error.data.message, { toastClass: 'toast-error' });
        });
    };
    $scope.fns.validate = function(jsEvent) {
        var confirm = $mdDialog.confirm()
            .title(APP_MESSAGES.validateFolderTitle)
            .textContent(APP_MESSAGES.validateFolderMessage)
            .ariaLabel('folder validation')
            .targetEvent(jsEvent)
            .ok(APP_MESSAGES.action.validate)
            .cancel(APP_MESSAGES.action.cancel);

        $mdDialog.show(confirm).then(function() {
            $scope.onLoading = true;
            projectService.changeFolderStatus('project.ng.validate_project').then((response) => {
                $scope.helpers.showSimpleToast(response.data.message);
                $scope.data.statusLabel = response.data.data.statusLabel;
                $scope.data.status = response.data.data.status;
                $scope.data.allowedActions = response.data.data.allowedActions;
                $scope.onLoading = false;
            }, error => {
                $scope.onLoading = false;
                $scope.helpers.showSimpleToast(error.data.message, { toastClass: 'toast-error' });
            })
        }, function(error) {
            $scope.onLoading = false;
        });
    }

    $scope.addNewEvent = function () {
        $scope.data.events.push({});
    }

    $scope.removeEvent = function (eventIndex) {
        $scope.data.events.splice(eventIndex, 1);
    };
};

ProjectInformationController.$inject = [
    '$scope',
    '$mdToast',
    '$mdDialog',
    '$mdPanel',
    'projectService',
    'resolverService',
    'CASE_TYPES',
    'PRIORIZATION_FILE_TYPES',
    'MARKET_TYPES',
    'SECOND_MARKET_TYPES',
    'TYPE_BONHOME',
    'PROJECT_ID',
    'APP_MESSAGES',
    'PROJECT_EVENT_TYPES',
];

angular.module('projectApp').component('appProjectInformation', {
    templateUrl: 'project-information.html',
    controller: ProjectInformationController,
});