import LoadPlanDialogController from './new-load-plan-dialog.controller';
import PanelMenuController from './panel-menu.controller';

angular.module('loadPlanApp').controller('loadPlanController', [
    '$scope', 
    '$mdDialog',
    '$mdPanel',
    '$q',
    '$element',
    'loadPlanService', 
    function (
        $scope, 
        $mdDialog, 
        $mdPanel,
        $q,
        $element,
        loadPlanService
    ) {
        
        $scope.data = {
            selectedEconomist: null,
        };
        $scope.economistCanceller = null;

        this.$onInit = () => {
            $('body').on('click', '.edit-load-plan', function (ev) {
                $scope.editLoadPlan({mode: $(this).data('action'), id: $(this).data('id')});
            });

            $('body').on('click', '.change-economist', function (ev) {
                var targetClass = $(this).data('target-class');
                var projectId = $(this).data('project-id');

                $scope.showEconomistPanel(ev, targetClass, projectId);
            });

            $scope.economistCanceller = $q.defer();
        };

        $scope.addLoadPlan = () => {
            var options = {};
            $scope.showLoadPlanDialogModal(options);
        };

        /**
         * - Data structure
         *      {
         *          mode: string ("edit", "create", "delete"),
         *          id: number
         *      }
         * @param {object} options
         */
        $scope.editLoadPlan = (options) => {
            $scope.showLoadPlanDialogModal(options);
        };

        $scope.showLoadPlanDialogModal = (options) => {
            $mdDialog.show({
                controller: LoadPlanDialogController,
                templateUrl: 'new-load-plan-dialog.html',
                parent: angular.element(document.body),
                targetEvent: event,
                locals: {
                    options,
                },
                clickOutsideToClose: true,
            }).then(function (answer) {
                $scope.status = 'You said the information was "' + answer + '".';
            }, function () {
                $scope.status = 'You cancelled the dialog.';
            });
        }

        $scope.showEconomistPanel = (ev, targetClass, projectId) => {
            var position = $mdPanel.newPanelPosition()
                .relativeTo('.' + targetClass)
                .addPanelPosition($mdPanel.xPosition.ALIGN_START, $mdPanel.yPosition.BELOW);

            var config = {
                attachTo: angular.element(document.body),
                controller: PanelMenuController,
                controllerAs: 'ctrl',
                templateUrl: 'panel-menu.html',
                panelClass: 'demo-menu-example',
                position: position,
                locals: {
                    projectId: projectId,
                },
                openFrom: ev,
                clickOutsideToClose: true,
                escapeToClose: true,
                focusOnOpen: false,
                zIndex: 2
            };

            $mdPanel.open(config);
        };
}]);