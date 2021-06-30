function ProjectInformationController(
    $scope,
    $mdToast,
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
    };
    $scope.data = {
        caseType: [],
        project: null,
        users: [],
        errors: null,
    };
    $scope.searchTerm = {
        users: '',
        clients: '',
        economists: '',
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
        projectService.getFormAutoCompleteData().then((response) => {
            $scope.data.clients = JSON.parse(response.data.clients);
            $scope.data.users = JSON.parse(response.data.users);
            $scope.data.economists = JSON.parse(response.data.economists);
            $scope.data.businessCharge = JSON.parse(response.data.businessCharge);
            $scope.data.countries = response.data.countries;
            $scope.onLoading = false;
            $scope.data.caseTypes = response.data.caseTypes;
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
        $scope.data.users = projectService.getFormAutoCompleteData().then((response) => {
            return JSON.parse(response.data.users);
        }, error => {
            console.info(error);
        })
    };
    $scope.helpers.handleUserChanged = function() {};

    $scope.helpers.getClents = function() {
        $scope.data.users = projectService.getFormAutoCompleteData().then((response) => {
            return JSON.parse(response.data.clients);
        }, error => {
            console.info(error);
        })
    };
    $scope.helpers.handleClientChanged = function() {};

    $scope.helpers.getEconomists = function() {
        $scope.data.users = projectService.getFormAutoCompleteData().then((response) => {
            return JSON.parse(response.data.economists);
        }, error => {
            console.info(error);
        })
    };
    $scope.helpers.handleEconomistsChanged = function() {};

    $scope.helpers.getBusinessCharge = function() {
        $scope.data.users = projectService.getFormAutoCompleteData().then((response) => {
            return JSON.parse(response.data.economists);
        }, error => {
            console.info(error);
        })
    };
    $scope.helpers.handleBusinessChargeChanged = function() {};

    $scope.helpers.getCountries = function() {
        $scope.data.countries = projectService.getFormAutoCompleteData().then((response) => {
            return response.data.countries;
        }, error => {
            console.info(error);
        });
    };
    $scope.helpers.handleCountriesChanged = function() {};
    $scope.helpers.getRecordAssistant = function() {
        $scope.data.countries = projectService.getFormAutoCompleteData().then((response) => {
            return response.data.users;
        }, error => {
            console.info(error);
        });
    };
    $scope.helpers.handleRecordAssistantChanged = function() {};
    $scope.helpers.getOcbsDriver = function() {
        $scope.data.countries = projectService.getFormAutoCompleteData().then((response) => {
            return response.data.users;
        }, error => {
            console.info(error);
        });
    };
    $scope.helpers.handleOcbsDriverChanged = function() {};

    $scope.helpers.getTceDriver = function() {
        $scope.data.countries = projectService.getFormAutoCompleteData().then((response) => {
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
        var idx = $scope.project.caseType.indexOf(item.value);
        if (idx > -1) {
            $scope.project.caseType.splice(idx, 1);
        } else {
            $scope.project.caseType.push(item.value);
        }
        console.info($scope.project.caseType);
    };

    $scope.fns = {};
    $scope.fns.submit = function() {
        $scope.onLoading = true;
        $scope.data.errors = {};
        projectService.saveProject(PROJECT_ID, $scope.project).then((response) => {
            console.info(response);
            $scope.onLoading = false;
            $scope.helpers.showSimpleToast(response.data.message);
        }, error => {
            $scope.onLoading = false;
            $scope.data.errors = error.data.errors;
            $scope.helpers.showSimpleToast(error.data.message, { toastClass: 'toast-error' });
        })
    };
};

ProjectInformationController.$inject = [
    '$scope',
    '$mdToast',
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