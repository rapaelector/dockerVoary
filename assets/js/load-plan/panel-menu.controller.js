PanelMenuController.$inject = [
    '$scope',
    '$q',
    '$element',
    '$mdToast',
    'loadPlanService',
    'mdPanelRef',
    'options',
];

function PanelMenuController(
    $scope, 
    $q, 
    $element, 
    $mdToast, 
    loadPlanService, 
    mdPanelRef, 
    options,
) {
    $scope.economistCanceller = null;
    $scope.data = {
        economist: null,
        economists: [],
    };
    $scope.economistSearchTerm = null;
    
    this.$onInit = () => {

        if (options.type === 'economist') {
            var query = options.economistName ? options.economistName : '';
            $scope.economistSearchTerm = query;
            loadPlanService.getEconomists(query, {}).then((response) => {
                $scope.data.economists = response;
                $scope.data.economist = response;
            })
        }

        if (options.type === 'realizationDate') {};
        
        $scope.economistCanceller = $q.defer();
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
            loadPlanService.saveProjectEconomist(item, options.projectId).then((response) => {
                loadPlanService.showNotification(response.data.message, 'toast-success');
                mdPanelRef.close();
                $('body').trigger('load_plan.redraw-dt');
            }, errors => {
                console.warn(errors.data.message);
                loadPlanService.showNotification(errors.data.message, 'toast-error');
                mdPanelRef.close();
                $('body').trigger('load_plan.redraw-dt');
            });
        }
    };

    $scope.economistChangeHandler = () => {
        if ($scope.data.economist) {
            loadPlanService.saveProjectEconomist({id: $scope.data.economist}, projectId).then((response) => {
                $scope.showNotification(response.data.message, 'toast-success');
                mdPanelRef.close();
                $('body').trigger('load_plan.redraw-dt');
            }, errors => {
                console.warn(errors.data.message);
                $scope.showNotification(errors.data.message, 'toast-error');
                mdPanelRef.close();
                $('body').trigger('load_plan.redraw-dt');
            });
        }
    };
};

export default PanelMenuController;