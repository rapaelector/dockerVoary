ContactCreationController.$inject = ['$scope', '$mdDialog', '$http', '$mdToast', 'fosJsRouting'];

function ContactCreationController($scope, $mdDialog, $http, $mdToast, fosJsRouting) {
    $scope.newContact = {
        lastName: '',
        firstName: '',
        email: '',
        phone: '',
        job: '',
        fax: '',
    };
    $scope.onLoading = false;
    $scope.data = {
        errors: {},
    };

    $scope.$init = function() {};

    $scope.fns = {};
    $scope.fns.cancelContactCreation = function() {
        $mdDialog.hide();
    }
    $scope.fns.saveContact = function() {
        $scope.onLoading = true;
        $scope.data.errors = {};
        $http.post(fosJsRouting.generate('project.ng.create_contact'), $scope.newContact).then((response) => {
            $scope.onLoading = false;
            $scope.fns.showSimpleToast(response.data.message);
            $mdDialog.hide(response.data.data);
        }, error => {
            $scope.onLoading = false;
            $scope.data.errors = error.data.errors;
            $scope.fns.showSimpleToast(error.data.message, { toastClass: 'toast-error' });
        });
    }

    $scope.fns.showSimpleToast = function(message, options) {
        if (options == null || options == undefined) {
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
    };
}

export default ContactCreationController;