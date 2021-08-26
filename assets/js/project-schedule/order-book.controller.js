OrderBookController.$inject = [
    '$scope', 
    '$element', 
    '$q', 
    'projectSchedulerService', 
    'options'
];

function OrderBookController($scope, $element, $q, projectSchedulerService, options) {
    $scope.oderBookModalTitle = '';
    $scope.data = {
        marketTypes: [],
    };
    $scope.form = {
        marketType: null,
        depositeDateEdit: null,
    };
    $scope.projectCanceller = null;

    this.$onInit = () => {
        console.info({options});
        $scope.oderBookModalTitle = options.modalTitle;
        $scope.data.marketTypes = options.marketTypes;
        $scope.projectCanceller = $q.defer();
    };

    $element.find('input').on('keydown', (ev) => {
        ev.stopPropagation();
    });
	
	/**
     * search for project
     * remote dataservice call.
     */
    $scope.queryProjectSearch = (query) => {
        $scope.projectCanceller.resolve();
        $scope.projectCanceller = $q.defer();
        var config = {
            timeout: $scope.projectCanceller.promise
        };
		if (query == undefined) {
			query = '';
		}

        return projectSchedulerService.getProjects(query, config);
    }

    /**
     * 
     * @param {object} item 
     */
	$scope.selectedProjectChange = (item) => {
        if (item) {
			$scope.form.project = item.id;
		}
    };

    $scope.saveOrderBook = () => {
        console.info({formData: $scope.form});
    };

    $scope.cancel = () => {
        $mdDialog.hide();
    };
};

export default OrderBookController;