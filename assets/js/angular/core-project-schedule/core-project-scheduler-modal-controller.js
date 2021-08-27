CoreProjectSchedulerModalController.$inject = [
    '$scope', 
    '$element',
    '$mdDialog',
    '$mdToast',
    '$q', 
    'coreProjectScheduleService',
    'resolverService',
    'mdPanelRef',
    'options',
    'resource',
    'column'
];

function CoreProjectSchedulerModalController(
    $scope, 
    $element,
    $mdDialog,
    $mdToast,
    $q, 
    coreProjectScheduleService,
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
    $scope.projectOrderBookForm = {};
    $scope.excludeField = [];

    this.$onInit = () => {
        $scope.oderBookModalTitle = options.modalTitle;
        $scope.data.marketTypes = options.marketTypes;
        $scope.data.types = options.types;
        console.info('options : ', {options});

        $scope.projectCanceller = $q.defer();
        if (resource && column && resource.id) {
            $scope.form.id = resource.id;
            coreProjectScheduleService.getProject(resource.id).then((response) => {
                $scope.form = response.data;
            })
        }
    };

    $scope.$watch('form', function (newValue, oldValue) {
        var changedFields = [];
        for (var field in newValue) {
            try {
                if (newValue[field] !== oldValue[field]) {
                    changedFields.push(field);
                }
            } catch (e) {
                console.warn(e);
            }
        }
        $scope.setFormValidity(changedFields, true);

    }, true);

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

        return coreProjectScheduleService.getProjects(query, config);
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

            coreProjectScheduleService.updateProject($scope.form.id, $scope.form).then((response) => {
                var fields = [
                    'name',
                    'marketType',
                    'startingDate',
                    'provisionalAmount',
                    'type',
                    'deliveryDate',
                    'workDuration',
                    'depositeDateEdit',
                    'paymentPercentage',
                ];

                mdPanelRef.close();
                $scope.saveOrderBookLoader = false;
                $scope.showNotification(response.data.message, 'toast-success');
                $scope.setFormValidity(fields, true);
            }, errors => {
                console.info({errors});
                console.warn(errors.data.message);
                $scope.showNotification(errors.data.message, 'toast-error');
                $scope.saveOrderBookLoader = false;
                $scope.data.errors[$scope.formName] = errors.data.errors;
                $scope.setFormValidity(Object.keys(errors.data.errors), false);
            });
        } else {
            coreProjectScheduleService.createProject($scope.form).then((response) => {
                mdPanelRef.close();
                $scope.saveOrderBookLoader = false;
                $scope.showNotification(response.data.message, 'toast-success');
            }, errors => {
                console.info({errors});
                console.warn(errors);
                $scope.showNotification(response.data.message, 'toast-error');
                $scope.saveOrderBookLoader = false;
                $scope.data.errors = errors.data.errors;
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

    /**
     * Check if form is valid
     * 
     * @param {array} fields 
     * @param {boolean} isValid
     */
    $scope.setFormValidity = (fields, isValid) => {
        for (var field of fields) {
            try {
                if ($scope.excludeField.indexOf(field) < 0) {
                    $scope.projectOrderBookForm[field].$setValidity('serverErrors', isValid);
                }
            } catch (e) {
                console.warn(e);
            }
        }
    };
};

export default CoreProjectSchedulerModalController;