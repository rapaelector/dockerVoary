ArchiveProjectDialogController.$inject = ['$scope', '$mdDialog', '$http', '$mdToast', 'fosJsRouting', 'PROJECT_ID'];

function ArchiveProjectDialogController($scope, $mdDialog, $http, $mdToast, fosJsRouting, PROJECT_ID) {
    $scope.data = {
        projectLoseReasons: [],
    };
    $scope.projectLoseReason = {
        reasonLost: null,
        otherText: '',
    };

    this.$onInit = function() {
        $scope.fns.getProjectReasons().then((response) => {
            $scope.data.projectLoseReasons = response.data.reasons;
        }, error => {
            console.info(error);
        });
    }

    $scope.fns = {};
    $scope.fns.cancelArchiveProject = function() {
        $mdDialog.hide();
    }
    $scope.fns.getProjectReasons = function() {
        return $http.get(fosJsRouting.generate('project.ng.project_get_lose_reasons'));
    };
    $scope.fns.projectLoseReasonChange = function() {
        if ($scope.projectLoseReason.reasonLost != 'other') {
            $scope.projectLoseReason.otherText = '';
        }
    };
    $scope.fns.archiveProject = function() {
        $scope.onLoading = true;
        $http.post(fosJsRouting.generate('project.ng.archived_project', { id: PROJECT_ID }), $scope.projectLoseReason).then((response) => {
            $mdDialog.hide(response.data);
            $scope.onLoading = false;
        }, error => {
            console.info(error);
            $scope.onLoading = false;
        });
    }
}

export default ArchiveProjectDialogController