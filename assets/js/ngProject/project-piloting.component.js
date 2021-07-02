function ProjectPilotingController($scope, $mdToast, projectService) {
    $scope.onLoading = false;
    $scope.data = {
        isLastRelauch: '1',
        users: [],
        exchangeFlags: [],
        errors: [],
    };
    $scope.exchangeHistory = {
        relaunchDate: '',
        nextStepDate: '',
        flag: '',
        description: '',
        projectConfidencePercentage: '',
        archi: '',
        date: null,
        archiUser: null,
    };

    this.$onInit = function() {
        projectService.getFormData().then((response) => {
            $scope.data.users = response.data.users;
            $scope.data.exchangeHistory = response.data.exchangeHistory;
            $scope.data.exchangeFlags = response.data.exchangeFlags;
            $scope.exchangeHistory.flag = $scope.data.exchangeFlags[0];
        }, error => {
            console.info('failed to laodd');
        });
    }

    $scope.fns = {};
    $scope.$watch('exchangeHistory.flag', function() {
        if ($scope.exchangeHistory.flag == $scope.data.exchangeFlags[1]) {
            $scope.exchangeHistory.relaunchDate = null;
        } else if ($scope.exchangeHistory.flag == $scope.data.exchangeFlags[0]) {
            $scope.exchangeHistory.nextStepDate = null;
        }
    }, true);
    $scope.$watch('exchangeHistory.archi', function() {
        if ($scope.exchangeHistory.archi) {
            $scope.exchangeHistory.archiUser = null;
        }
    })
    $scope.fns.saveProjectPiloting = function() {
        $scope.onLoading = true;
        $scope.data.errors = {};

        if ($scope.data.isLastRelauch == '1') {
            $scope.exchangeHistory.date = null;
        }

        projectService.saveProjectPiloting($scope.exchangeHistory).then((response) => {
            $scope.onLoading = false;
            $scope.exchangeHistory = {};
            $scope.fns.showNotification(response.data.message);
        }, error => {
            $scope.onLoading = false;
            $scope.data.errors = error.data.errors;
            $scope.fns.showNotification(error.data.message, { toastClass: 'toast-error' });
        })
    };

    $scope.fns.showNotification = function(message, options) {
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
        ).then(function() {
            console.info('Toast dismissed');
        }).catch(function() {
            console.info('failed to laod md toast');
        });
    }
}

ProjectPilotingController.$inject = ['$scope', '$mdToast', 'projectService'];

angular.module('projectApp').component('appProjectPiloting', {
    templateUrl: 'project-piloting.html',
    controller: ProjectPilotingController,
});