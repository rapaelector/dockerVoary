UserHelperController.$inject = ['$scope', '$element', '$q', 'mdPanelRef', 'options', 'userHelperService', 'PANEL_ELEVATION_CLASS'];

function UserHelperController($scope, $element, $q, mdPanelRef, options, userHelperService, PANEL_ELEVATION_CLASS) {
    $scope.userCanceller = null;
    $scope.data = {
        users: {},
        user: null,
        pageTitle: '',
        inputSearchLabel: '',
        selectedUser: null,
        additionalTitle: '',
    };
    $scope.userId = null;
    $scope.userSearchTerm = '';

    this.$onInit = () => {
        $scope.userCanceller = $q.defer();
        $scope.loading = true;
        $scope.data.additionalTitle = options.additionalTitle;
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

            if (options.userId) {
                $scope.userId = options.userId;
            }
        }

        userHelperService.getUsers(query, {}).then((response) => {
            $scope.data.users = response;
            const currentUser = $scope.data.users.find((u, i) => u.id === $scope.userId);
            if (currentUser) {
                return Promise.resolve(currentUser);
            } else {
                return userHelperService.getUser($scope.userId);
            };
        }).then(user => {
            const currentUser = $scope.data.users.find((u, i) => u.id === $scope.userId);
            if (!currentUser) {
                $scope.data.users.unshift(currentUser);
            }
            $scope.selectedUser = user;
            $scope.loading = false;
        }, errors => {
            $scope.loading = false;
        });
    };

    $scope.$watch('data.user', function () {
        $scope.loading = true;
        $scope.queryUserSearch($scope.data.user).then((response) => {
            $scope.data.users = response;
            $scope.loading = false;
        }, errors => {
            console.warn({errors});
            $scope.loading = false;
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
        $scope.data.selectedUser = user;
        $scope.save()
    };

    $scope.saveUser = (user) => {
        $scope.loading = true;
        if (options.onUserSave) {
            options.onUserSave($scope.data.selectedUser, mdPanelRef).then((response) => {
                $scope.loading = false;
            }, errors => {
                $scope.loading = false;
            })
        }
    };

    $scope.save = () => {
        $scope.loading = true;
        $scope.saveUser();
    };

    $scope.closePanel = () => {
        mdPanelRef.close();
    };
};

export default UserHelperController;