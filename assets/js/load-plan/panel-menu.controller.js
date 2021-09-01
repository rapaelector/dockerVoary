PanelMenuController.$inject = [
    '$scope',
    '$q',
    '$element',
    '$mdToast',
    'projectId',
    'loadPlanService',
    'mdPanelRef',
    'economistName',
    'economistId'
];

function PanelMenuController($scope, $q, $element, $mdToast, projectId, loadPlanService, mdPanelRef, economistName, economistId) {
    $scope.economistCanceller = null;
    $scope.data = {
        economist: null,
        economists: [],
    };
    $scope.economistSearchTerm = null;
    
    this.$onInit = () => {
        $scope.economistCanceller = $q.defer();
        var query = economistName ? economistName : '';
        $scope.economistSearchTerm = query;
        loadPlanService.getEconomists(query, {}).then((response) => {
            $scope.data.economists = response;
            $scope.data.economist = response;
        })
    };
    
    $scope.$watch('economistSearchTerm', function () {
        $scope.queryEconomistSearch($scope.economistSearchTerm).then((response) => {
            $scope.data.economists = response;
            $scope.data.economist = response;
        });
    }, true);

    $element.find('input').on('keydown', (ev) => {
        ev.stopPropagation();
    });
    
    /**
     * search for economist
     * remote dataservice call.
     */
    $scope.queryEconomistSearch = (query) => {
        $scope.economistCanceller.resolve();
        $scope.economistCanceller = $q.defer();
        var config = {
            timeout: $scope.economistCanceller.promise,
        };
        if (query == undefined) {
            query = '';
        }

        return loadPlanService.getEconomists(query, config);
    }

    $scope.selectEconomistChange = (item) => {
        if (item) {
            loadPlanService.saveProjectEconomist(item, projectId).then((response) => {
                $scope.showNotification(response.data.message, 'toast-success');
                mdPanelRef.close();
                location.reload();
            }, errors => {
                console.warn(errors.data.message);
                $scope.showNotification(errors.data.message, 'toast-error');
                mdPanelRef.close();
                location.reload();
            });
        }
    };

    /**
     * Show notification with given message and options
     * 
     * @param {string} message 
     * @param {object} options 
     */
    $scope.showNotification = (message, toastClass) => {
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
            .toastClass(toastClass)
        ).then(() => {
            console.info('Toast dismissed');
        }).catch(() => {
            console.info('failed to laod md toast');
        });
    };

    $scope.economistChangeHandler = () => {
        if ($scope.data.economist) {
            loadPlanService.saveProjectEconomist({id: $scope.data.economist}, projectId).then((response) => {
                $scope.showNotification(response.data.message, 'toast-success');
                mdPanelRef.close();
                location.reload();
            }, errors => {
                console.warn(errors.data.message);
                $scope.showNotification(errors.data.message, 'toast-error');
                mdPanelRef.close();
                location.reload();
            });
        }
    };
};

export default PanelMenuController;