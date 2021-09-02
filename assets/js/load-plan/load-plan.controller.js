import LoadPlanDialogController from './new-load-plan-dialog.controller';
import PanelMenuController from './panel-menu.controller';
import RealizationPanelController from './realization-panel.controller';

angular.module('loadPlanApp').controller('loadPlanController', [
    '$scope', 
    '$mdDialog',
    '$mdPanel',
    '$q',
    '$element',
    'loadPlanService',
    'dateHelperService',
    function (
        $scope, 
        $mdDialog, 
        $mdPanel,
        $q,
        $element,
        loadPlanService,
        dateHelperService
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
                var loadPlanId = $(this).data('load-plan-id');
                var economistName = $(this).data('economist-name');
                var economistId = $(this).data('economist-id');

                $scope.showPanel(ev, {
                    type: 'economist',
                    templateUrl: 'economist.html',
                    targetClass, 
                    loadPlanId, 
                    economistName, 
                    economistId,
                });
            });
            
            $('body').on('click', '.realization-date', function (ev) {
                var loadPlanId = $(this).data('load-plan-id');
                var targetClass = $(this).data('target-class');
                var realizationDate = $(this).data('realization-date');

                $scope.showRealizationPanel(ev, {
                    type: 'realizationDate',
                    templateURL: 'realization-date.html',
                    targetClass,
                    loadPlanId,
                    realizationDate,
                });
            });
            
            $('body').on('click', '.update-deadline', function (ev) {
                var loadPlanId = $(this).data('load-plan-id');
                var targetClass = $(this).data('target-class');
                var deadline = $(this).data('deadline');
                var currentDate = $(this).data('current-date');

                dateHelperService.updateDate(ev, {
                    targetClass,
                    loadPlanId,
                    deadline,
                    currentDate,
                    pageTitle: 'Changer la date butoir',
                    saveDate: (newDate) => {
                        var date = newDate;
                        if (newDate) {
                            date = moment(newDate).format('YYYY-MM-DD');
                        }

                        loadPlanService.updateDeadlineDate(loadPlanId, {newDeadlineDate: date}).then((response) => {
                            loadPlanService.showNotification(response.data.message, 'toast-success');
                            $('body').trigger('load_plan.redraw-dt');
                        }, errors => {
                            loadPlanService.showNotification(errors.data.message, 'toast-error');
                            $('body').trigger('load_plan.redraw-dt');
                        });
                    },
                });
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

        $scope.showPanel = (ev, args) => {
            console.info({ev, args, type: args.type});

            var position = $mdPanel.newPanelPosition()
                .relativeTo('.' + args.targetClass)
                .addPanelPosition($mdPanel.xPosition.ALIGN_START, $mdPanel.yPosition.BELOW);
            var options = {};

            if (args.type === 'economist') {
                options = {
                    type: args.type,
                    projectId: args.projectId,
                    economistName: args.economistName,
                    economistId: args.economistId
                }
            };

            var config = {
                attachTo: angular.element(document.body),
                controller: PanelMenuController,
                controllerAs: 'ctrl',
                templateUrl: args.templateUrl,
                panelClass: 'demo-menu-example',
                position: position,
                locals: {
                    options: options,
                },
                openFrom: ev,
                clickOutsideToClose: true,
                escapeToClose: true,
                focusOnOpen: false,
                zIndex: 2
            };

            $mdPanel.open(config);
        };
        
        $scope.showRealizationPanel = (ev, args) => {
            var position = $mdPanel.newPanelPosition()
                .relativeTo('.' + args.targetClass)
                .addPanelPosition($mdPanel.xPosition.ALIGN_START, $mdPanel.yPosition.BELOW);

            var config = {
                attachTo: angular.element(document.body),
                controller: RealizationPanelController,
                controllerAs: 'ctrl',
                templateUrl: 'realization-date.html',
                panelClass: 'demo-menu-example',
                position: position,
                locals: {
                    options: args,
                },
                openFrom: ev,
                clickOutsideToClose: true,
                escapeToClose: true,
                focusOnOpen: false,
                zIndex: 10
            };

            $mdPanel.open(config);
        };
}]);