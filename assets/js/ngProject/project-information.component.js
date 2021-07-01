import ContactCreationController from './contact-creation.controller.js';

function ProjectInformationController(
    $scope,
    $mdToast,
    $mdDialog,
    projectService,
    CASE_TYPES,
    PRIORIZATION_FILE_TYPES,
    MARKET_TYPES,
    SECOND_MARKET_TYPES,
    TYPE_BONHOME,
    PROJECT_ID
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
    };
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

    $scope.fns = {};
    $scope.fns.submit = function() {
        $scope.onLoading = true;
        $scope.data.errors = {};
        projectService.saveProject(PROJECT_ID, $scope.project).then((response) => {
            $scope.onLoading = false;
            $scope.helpers.showSimpleToast(response.data.message);
        }, error => {
            $scope.onLoading = false;
            $scope.data.errors = error.data.errors;
            $scope.helpers.showSimpleToast(error.data.message, { toastClass: 'toast-error' });
        })
    };

    $scope.fns.showDialog = function(jsEvent) {
        // $mdDialog.alert().disableParentScroll(true);
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

        });
    }
};

ProjectInformationController.$inject = [
    '$scope',
    '$mdToast',
    '$mdDialog',
    'projectService',
    'CASE_TYPES',
    'PRIORIZATION_FILE_TYPES',
    'MARKET_TYPES',
    'SECOND_MARKET_TYPES',
    'TYPE_BONHOME',
    'PROJECT_ID'
];

angular.module('projectApp').component('appProjectInformation', {
    templateUrl: 'project-information.html',
    controller: ProjectInformationController,
});