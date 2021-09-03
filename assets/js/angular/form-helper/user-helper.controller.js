UserHelperController.$inject = ['$scope', '$element', '$q', 'mdPanelRef', 'options', 'userHelperService'];

function UserHelperController($scope, $element, $q, mdPanelRef, options, userHelperService) {
    $scope.userCanceller = null;
    $scope.data = {
        users: {},
        user: null,
        pageTitle: '',
        inputSearchLabel: '',
    };
    $scope.userSearchTerm = '';

    this.$onInit = () => {
        $scope.userCanceller = $q.defer();

        var query = '';
        if (options) {
            if (options.userName) {
                query = options.userName ? options.userName : '';
                $scope.userSearchTerm = query;
                $scope.data.user = query;
            }

            if (options.pageTitle) {
                $scope.data.pageTitle = options.pageTitle;
            }

            if (options.inputSearchLabel) {
                $scope.data.inputSearchLabel = options.inputSearchLabel;
            }
        }

        userHelperService.getUsers(query, {}).then((response) => {
            $scope.data.users = response;
        })
    };

    $scope.$watch('data.user', function () {
        $scope.queryUserSearch($scope.data.user).then((response) => {
            $scope.data.users = response;
        }, errors => {
            console.warn({errors});
        });
    })
    /**
     * Stop propagation while writting
     */
    $element.find('input').on('keydown', (ev) => {
        ev.stopPropagation();
    });
    
    /**
     * search for economist
     * remote dataservice call.
     */
    $scope.queryUserSearch = (query) => {
        $scope.userCanceller.resolve();
        $scope.userCanceller = $q.defer();
        var config = {
            timeout: $scope.userCanceller.promise,
        };
        if (query == undefined) {
            query = '';
        }

        return userHelperService.getUsers(query, config);
    }

    $scope.userChanged = (item) => {
        if (item) {
        }
    };

    $scope.userChangedHandler = () => {
        if ($scope.data.user) {
        }
    };

    $scope.onUserClicked = (user) => {
        $scope.loading = true;
        if (options.onUserSave) {
            options.onUserSave(user, mdPanelRef).then((response) => {
                $scope.loading = false;
            }, errors => {
                $scope.loading = false;
            })
        }
    };
};

export default UserHelperController;