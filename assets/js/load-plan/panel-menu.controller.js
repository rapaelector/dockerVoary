PanelMenuController.$inject = [
    '$scope',
    '$q',
    '$element',
    '$mdToast',
    'projectId',
    'loadPlanService',
    'mdPanelRef'
];

function PanelMenuController($scope, $q, $element, $mdToast, projectId, loadPlanService, mdPanelRef) {
    $scope.economistCanceller = null;
    
    this.$onInit = () => {
        $scope.economistCanceller = $q.defer();
    };
    
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
};

export default PanelMenuController;