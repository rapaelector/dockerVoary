OrderBookController.$inject = [
    '$scope', 
    '$element',
    '$mdDialog',
    '$q', 
    'projectSchedulerService',
    'resolverService',
    'mdPanelRef',
    'options',
    'resource',
    'column'
];

function OrderBookController(
    $scope, 
    $element,
    $mdDialog,
    $q, 
    projectSchedulerService,
    resolverService,
    mdPanelRef,
    options, 
    resource, 
    column
) {
    $scope.oderBookModalTitle = '';
    $scope.data = {
        marketTypes: [],
        selectedProject: null,
    };
    $scope.form = {
        project: null,
        marketType: null,
        depositeDateEdit: null,
    };
    $scope.projectCanceller = null;

    this.$onInit = () => {
        $scope.oderBookModalTitle = options.modalTitle;
        $scope.data.marketTypes = options.marketTypes;
        $scope.projectCanceller = $q.defer();
        if (resource && column && resource.id) {
            projectSchedulerService.getProject(resource.id).then((response) => {
                $scope.form = response.data;
                $scope.data.selectedProject = {
                    id: $scope.form.id, 
                    name: $scope.form.name, 
                    prospect: {
                        clientNumber: resolverService.resolve([$scope, 'form', 'prospect', 'clientNumber'], ''),
                    }
                };
            })
        }
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
        mdPanelRef.close();
    };
};

export default OrderBookController;