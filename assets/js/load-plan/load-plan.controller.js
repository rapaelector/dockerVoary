import LoadPlanDialogController from './new-load-plan-dialog.controller';
import PanelMenuController from './panel-menu.controller';

angular.module('loadPlanApp').controller('loadPlanController', [
    '$scope', 
    '$mdDialog',
    '$mdPanel',
    '$q',
    '$element',
    'moment',
    'loadPlanService',
    'dateHelperService',
    'userHelperService',
    'weekNumberHelperService',
    function (
        $scope, 
        $mdDialog, 
        $mdPanel,
        $q,
        $element,
        moment,
        loadPlanService,
        dateHelperService,
        userHelperService,
        weekNumberHelperService
    ) {
        
        $scope.data = {
            selectedEconomist: null,
            date: null,
            loadMeteringDate: null,
        };
        $scope.economistCanceller = null;
        $scope.options = {
            dateRangePicker: {},
        };

        this.$onInit = () => {
            /**
             * Load metering datepicker
             */
            $scope.options.dateRangePicker = {
                autoApply: true,
                autoClose: true,
                locale: {
                    format: 'DD/MM/YYYY'
                },
                ranges: {
                    "Année courante": [moment().startOf('year'), moment().endOf('year')],
                    "Les 6 dérnier mois": [moment(moment().endOf('month').add(-6, 'month').startOf('month')), moment(moment().endOf('month').add(-1, 'month'))],
                    "Année dérniere": [moment(moment().add(-1, 'year').format('YYYY') + '-01-01'), moment(moment().add(-1, 'year').format('YYYY') + '-12-31')],
                    "6 prochains mois": [moment(), moment().add(6, 'month')],
                    "12 prochains mois": [moment(), moment().add(12, 'month').endOf('month')],
                    "12 mois glissant": [moment(), moment().add(1, 'year').endOf('month')],
                },
            };

            $('body').on('click', '.edit-load-plan', function (ev) {
                $scope.editLoadPlan({
                    mode: $(this).data('action'), 
                    id: $(this).data('id'),
                    projectId: $(this).data('project-id'),
                });
            });

            $('body').on('click', '.change-economist', function (ev) {
                var target = $(this).data('target');
                var loadPlanId = $(this).data('load-plan-id');
                var economistName = $(this).data('economist-name');
                var userId = $(this).data('economist-id');
                var additionalTitle = $(this).data('project-name');

                userHelperService.selectUser(ev, {
                    target,
                    pageTitle: 'Changer economiste',
                    inputSearchLabel: 'Rechercher',
                    userId,
                    additionalTitle,
                    onUserSave: (selectedUser, mdPanelRef) => {
                        return loadPlanService.saveProjectEconomist(loadPlanId, selectedUser).then((response) => {
                            loadPlanService.showNotification(response.data.message, 'toast-success');
                            mdPanelRef.close();
                            $('body').trigger('load_plan.redraw-dt');
                            return response;
                        }, errors => {
                            console.warn(errors.data.message);
                            loadPlanService.showNotification(errors.data.message, 'toast-error');
                            mdPanelRef.close();
                            $('body').trigger('load_plan.redraw-dt');
                            return errors;
                        });
                    }
                })
            });
            
            /**
             * Update realization date(DATE DE DEVIS)
             * Show calendar panel
             */
            $('body').on('click', '.realization-date', function (ev) {
                var loadPlanId = $(this).data('load-plan-id');
                var target = $(this).data('target');
                var currentDate = $(this).data('realization-date');
                var additionalTitle = $(this).data('project-name');

                dateHelperService.updateDate(ev, {
                    target,
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
            
            /**
             * Update deadline date in the table
             * Show calendar Panel
             */
            $('body').on('click', '.update-deadline', function (ev) {
                var loadPlanId = $(this).data('load-plan-id');
                var target = $(this).data('target');
                var currentDate = $(this).data('current-date');
                var additionalTitle = $(this).data('project-name');

                dateHelperService.updateDate(ev, {
                    target,
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
            
            /**
             * Update weekNumber
             * Show week number panel
             * Get date from week number
             */
            $('body').on('click', '.update-week-number', function(ev) {
                var currentValue = $(this).data('current-value');
                var id = $(this).data('load-plan-id');
                var target = $(this).data('target');
                var additionalTitle = $(this).data('project-name');
                
                weekNumberHelperService.selectWeek(ev, {
                    currentValue,
                    id,
                    target,
                    additionalTitle,
                    label: 'N° de semaine',
                    pageTitle: 'Changer n° semaine',
                    onWeekNumberSave: (weekDate, mdPanelRef) => {
                        if (weekDate) {
                            var startDate = moment(weekDate);

                            return loadPlanService.saveWeekNumber(id, {
                                startDate: startDate.format('YYYY-MM-DD'),
                            }).then((response) => {
                                mdPanelRef.close();
                                loadPlanService.showNotification(response.data.message, 'toast-success');
                                $('body').trigger('load_plan.redraw-dt');

                                return response;
                            }, errors => {
                                mdPanelRef.close();
                                loadPlanService.showNotification(errors.data.message, 'toast-error');
                                $('body').trigger('load_plan.redraw-dt');
                                
                                return errors;
                            });
                        }
                    }
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

        /**
         * 
         * @param {object} options 
         */
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
                disableParentScroll: true,
                parentScroll: false,
            }).then(function (answer) {
                $scope.status = 'You said the information was "' + answer + '".';
            }, function () {
                $scope.status = 'You cancelled the dialog.';
            });
        }

        /**
         * 
         * @param {jsEvent} ev 
         * @param {object} args 
         */
        $scope.showPanel = (ev, args) => {
            var position = $mdPanel.newPanelPosition()
                .relativeTo('.' + args.targetClass)
                .addPanelPosition($mdPanel.xPosition.ALIGN_START, $mdPanel.yPosition.BELOW);

            var config = {
                attachTo: angular.element(document.body),
                controller: PanelMenuController,
                controllerAs: 'ctrl',
                templateUrl: args.templateUrl,
                panelClass: 'demo-menu-example',
                position: position,
                locals: {
                    options: args,
                },
                openFrom: ev,
                clickOutsideToClose: true,
                escapeToClose: true,
                focusOnOpen: false,
                zIndex: 2,
                disableParentScroll: true,
                parentScroll: false,
            };

            $mdPanel.open(config);
        };
}]);