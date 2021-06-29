function ProjectInformationController($scope, ProjectService, CASE_TYPES, PRIORIZATION_FILE_TYPES, PROJECT_ID) {
    $scope.project = {};
    $scope.data = {
        autoComplete: {},
        caseType: [],
        project: null,
        users: [],
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
        $scope.caseTypes = CASE_TYPES;
        $scope.priorizationFileTypes = PRIORIZATION_FILE_TYPES;
        ProjectService.getProject(PROJECT_ID).then((project) => {
            $scope.data.project = project.data;
            console.info($scope.data.project);
        }, error => {

        })
        ProjectService.getFormAutoCompleteData().then((response) => {
            $scope.data.autoComplete.clients = JSON.parse(response.data.clients);
            $scope.data.autoComplete.users = JSON.parse(response.data.users);
            $scope.data.autoComplete.economists = JSON.parse(response.data.economists);
            $scope.data.autoComplete.businessCharge = JSON.parse(response.data.businessCharge);
            $scope.data.autoComplete.countries = response.data.countries;
            console.info(response);
        }, error => {
            console.info(error);
        })
    };

    $scope.helpers = {};
    $scope.helpers.getUsers = function() {
        $scope.data.users = ProjectService.getFormAutoCompleteData().then((response) => {
            return JSON.parse(response.data.users);
        }, error => {
            console.info(error);
        })
    };
    $scope.helpers.handleUserChanged = function() {
        console.info('hello');
    };
    $scope.helpers.getClents = function() {
        $scope.data.users = ProjectService.getFormAutoCompleteData().then((response) => {
            return JSON.parse(response.data.clients);
        }, error => {
            console.info(error);
        })
    };
    $scope.helpers.handleClientChanged = function() {
        console.info('hello');
    };
    $scope.helpers.getEconomists = function() {
        $scope.data.users = ProjectService.getFormAutoCompleteData().then((response) => {
            return JSON.parse(response.data.economists);
        }, error => {
            console.info(error);
        })
    };
    $scope.helpers.handleEconomistsChanged = function() {
        console.info('hello');
    };
    $scope.helpers.getBusinessCharge = function() {
        $scope.data.users = ProjectService.getFormAutoCompleteData().then((response) => {
            return JSON.parse(response.data.economists);
        }, error => {
            console.info(error);
        })
    };
    $scope.helpers.handleBusinessChargeChanged = function() {
        console.info('hello');
    };
    $scope.helpers.getCountries = function() {
        $scope.data.countries = ProjectService.getFormAutoCompleteData().then((response) => {
            return response.data.countries;
        }, error => {
            console.info(error);
        });
    };
    $scope.helpers.handleCountriesChanged = function() {
        console.info('hello');
    };
    $scope.helpers.getRecordAssistant = function() {
        $scope.data.countries = ProjectService.getFormAutoCompleteData().then((response) => {
            return response.data.users;
        }, error => {
            console.info(error);
        });
    };
    $scope.helpers.handleRecordAssistantChanged = function() {
        console.info('hello handleRecordAssistantChanged');
    };
    $scope.helpers.getOcbsDriver = function() {
        $scope.data.countries = ProjectService.getFormAutoCompleteData().then((response) => {
            return response.data.users;
        }, error => {
            console.info(error);
        });
    };
    $scope.helpers.handleOcbsDriverChanged = function() {
        console.info('hello handleOcbsDriverChanged');
    };
    $scope.helpers.getTceDriver = function() {
        $scope.data.countries = ProjectService.getFormAutoCompleteData().then((response) => {
            return response.data.users;
        }, error => {
            console.info(error);
        });
    };
    $scope.helpers.handleTceDriverChanged = function() {
        console.info('hello handleTceDriverChanged');
    };

    $scope.toggle = function(item) {
        var idx = $scope.data.caseType.indexOf(item);
        if (idx > -1) {
            $scope.data.caseType.splice(idx, 1);
        } else {
            $scope.data.caseType.push(item);
        }
    };

    $scope.fns = {};
    $scope.fns.submit = function() {
        console.info($scope.project);
    };
};

ProjectInformationController.$inject = ['$scope', 'ProjectService', 'CASE_TYPES', 'PRIORIZATION_FILE_TYPES', 'PROJECT_ID'];

angular.module('projectApp').component('appProjectInformation', {
    templateUrl: 'project-information.html',
    controller: ProjectInformationController,
});