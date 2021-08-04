function ProjectPilotingController($scope, $mdToast, projectService) {
    $scope.onLoading = false;
    $scope.data = {
        isLastRelauch: '1',
        users: [],
        exchangeFlags: [],
        errors: [],
        exchangeHistories: [],
        exchangeHistoryCount: 0,
    };
    $scope.exchangeHistory = {
        relaunchDate: '',
        nextStepDate: '',
        flag: '',
        description: '',
        percentage: '',
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
        projectService.getExchangeHistories().then((response) => {
            $scope.data.exchangeHistories = response.data.data.exchangeHistories;
            $scope.data.exchangeHistoryCount = response.data.data.exchangeHistoryCount;
        }, error => {
            console.info(error);
        })
    }

    $scope.fns = {};
    $scope.$watch('exchangeHistory.flag', function() {
        if ($scope.exchangeHistory.flag == $scope.data.exchangeFlags[1]) {
            $scope.exchangeHistory.relaunchDate = null;
            $scope.exchangeHistory.nextStepDate = new Date();
        } else if ($scope.exchangeHistory.flag == $scope.data.exchangeFlags[0]) {
            $scope.exchangeHistory.nextStepDate = null;
        }
    }, true);
    $scope.$watch('exchangeHistory.archi', function() {
        if ($scope.exchangeHistory.archi) {
            $scope.exchangeHistory.archiUser = null;
        }
    })
    $scope.$watch('exchangeHistory', function() {

    }, true);
    $scope.fns.saveProjectPiloting = function() {
        $scope.onLoading = true;
        $scope.data.errors = {};
        if ($scope.data.isLastRelauch == '1') {
            $scope.exchangeHistory.date = null;
        }

        projectService.saveProjectPiloting($scope.exchangeHistory).then((response) => {
            $scope.onLoading = false;
            $scope.exchangeHistory = {};
            $scope.data.exchangeHistories.push(response.data.data.exchangeHistory);
            $scope.data.exchangeHistoryCount += 1;
            $scope.fns.showNotification(response.data.message);
        }, error => {
            $scope.onLoading = false;
            $scope.data.errors = error.data.errors;
            $scope.fns.showNotification(error.data.message, { toastClass: 'toast-error' });
        })
    };

    $scope.fns.deleteProjectPiloting = function(id) {
        $scope.onLoading = true;

        projectService.deleteProjectPiloting(id).then((response) => {
            $scope.onLoading = false;
            $scope.data.exchangeHistories = $scope.data.exchangeHistories.filter(x => {
                return x.id != id;
            })
            $scope.data.exchangeHistoryCount -= 1;
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
    $scope.fns.canSubmit = function() {
        if (!$scope.exchangeHistory || Object.keys($scope.exchangeHistory).length === 0) {
            return false;
        }
        if (
            ($scope.exchangeHistory.flag == $scope.data.exchangeFlags[0] && ($scope.exchangeHistory.relaunchDate == null || $scope.exchangeHistory.relaunchDate == '')) ||
            ($scope.exchangeHistory.flag == $scope.data.exchangeFlags[1] && ($scope.exchangeHistory.nextStepDate == null || $scope.exchangeHistory.nextStepDate == ''))
        ) {
            return false;
        }

        return true;
    }

    $scope.fns.cancel = function() {
        $scope.exchangeHistory = {};
    }
}

ProjectPilotingController.$inject = ['$scope', '$mdToast', 'projectService'];

angular.module('projectApp').component('appProjectPiloting', {
    templateUrl: 'project-piloting.html',
    controller: ProjectPilotingController,
});