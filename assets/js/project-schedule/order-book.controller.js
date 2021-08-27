OrderBookController.$inject = [
    '$scope', 
    '$element',
    '$mdDialog',
    '$mdToast',
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
    $mdToast,
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
        types: [],
        errors: [],
    };
    $scope.form = {
        project: null,
        marketType: null,
        depositeDateEdit: null,
        id: null,
        type: null,
    };
    $scope.projectCanceller = null;
    $scope.saveOrderBookLoader = false;
    $scope.types = {
        typeWorkDuration: 'type_work_duration',
        typeDeliveryDate: 'type_delivery_date',
    };
    $scope.formName = 'projectOrderBook';

    this.$onInit = () => {
        $scope.oderBookModalTitle = options.modalTitle;
        $scope.data.marketTypes = options.marketTypes;
        $scope.data.types = options.types;

        $scope.projectCanceller = $q.defer();
        if (resource && column && resource.id) {
            $scope.form.id = resource.id;
            projectSchedulerService.getProject(resource.id).then((response) => {
                $scope.form = response.data;
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
        $scope.saveOrderBookLoader = true;

        if ($scope.form.id) {
            if ($scope.form.type === $scope.types.typeDeliveryDate) {
                $scope.form.workDuration = null;
            }
            
            if ($scope.form.type === $scope.types.typeWorkDuration) {
                $scope.form.deliveryDate = null;
            }

            projectSchedulerService.updateProject($scope.form.id, $scope.form).then((response) => {
                mdPanelRef.close();
                $scope.saveOrderBookLoader = false;
                $scope.showNotification(response.data.message, 'toast-success');
            }, errors => {
                console.info({errors});
                console.warn(errors);
                mdPanelRef.close();
                $scope.showNotification(errors.data.message, 'toast-danger');
                $scope.saveOrderBookLoader = false;
            });
        } else {
            projectSchedulerService.createProject($scope.form).then((response) => {
                mdPanelRef.close();
                $scope.saveOrderBookLoader = false;
                $scope.showNotification(response.data.message, 'toast-success');
            }, errors => {
                console.info({errors});
                console.warn(errors);
                mdPanelRef.close();
                $scope.showNotification(response.data.message, 'toast-error');
                $scope.saveOrderBookLoader = false;

            })
        }
    };

    $scope.cancel = () => {
        mdPanelRef.close();
    };

    $scope.showNotification = (message, toastClass) => {
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

    $scope.isTypeDeliveryDate = () => {
        return $scope.form.type === $scope.types.typeDeliveryDate;
    };

    $scope.isTypeWorkDuration = () => {
        return $scope.form.type == $scope.types.typeWorkDuration;
    };
};

export default OrderBookController;