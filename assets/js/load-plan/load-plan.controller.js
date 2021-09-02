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
                var currentDate = $(this).data('realization-date');
                var additionalTitle = $(this).data('project-name');

                dateHelperService.updateDate(ev, {
                    targetClass,
                    pageTitle: 'Changer la date de devis',
                    additionalTitle,
                    currentDate,
                    onSave: (newDate, mdPanelRef) => {
                        if (newDate) {
                            newDate = moment(newDate).format('YYYY-MM-DD');
                        }

                        return loadPlanService.updateRealizationDate(loadPlanId, {realizationDate: newDate}).then((response) => {
                            loadPlanService.showNotification(response.data.message, 'toast-success');
                            mdPanelRef.close();
                            $('body').trigger('load_plan.redraw-dt');

                            return response;
                        }, errors => {
                            loadPlanService.showNotification(errors.data.message, 'toast-error');
                            mdPanelRef.close();
                            $('body').trigger('load_plan.redraw-dt');

                            return response;
                        });
                    },
                });
            });
            
            $('body').on('click', '.update-deadline', function (ev) {
                var loadPlanId = $(this).data('load-plan-id');
                var targetClass = $(this).data('target-class');
                var currentDate = $(this).data('current-date');
                var additionalTitle = $(this).data('project-name');

                dateHelperService.updateDate(ev, {
                    targetClass,
                    currentDate,
                    pageTitle: 'Changer la date butoir',
                    additionalTitle,
                    onSave: (newDate, mdPanelRef) => {
                        if (newDate) {
                            newDate = moment(newDate).format('YYYY-MM-DD');
                        }
                        
                        return loadPlanService.updateDeadlineDate(loadPlanId, {newDeadlineDate: newDate}).then((response) => {
                            loadPlanService.showNotification(response.data.message, 'toast-success');
                            mdPanelRef.close();
                            $('body').trigger('load_plan.redraw-dt');

                            return response;
                        }, errors => {
                            loadPlanService.showNotification(errors.data.message, 'toast-error');
                            mdPanelRef.close();
                            $('body').trigger('load_plan.redraw-dt');
                            return errors;
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