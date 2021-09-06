import WeekNumberHelperController from './week-number-helper.controller';
import weekNumberHelperTemplate from './week-number-helper-template.html';

angular.module('formHelperModule').factory('weekNumberHelperService', [
    '$mdPanel', 
    'PANEL_ELEVATION_CLASS', 
    'PANEL_CLASS',
    function(
        $mdPanel, 
        PANEL_ELEVATION_CLASS,
        PANEL_CLASS
    ) {
    var _this = {};

    _this.selectWeek = (ev, options) => {
        var position = $mdPanel.newPanelPosition()
            .relativeTo(options.target)
            .addPanelPosition($mdPanel.xPosition.OFFSET_END, $mdPanel.yPosition.ALIGN_BOTTOMS);
        var panelClass = 'week-helper-panel ' + PANEL_ELEVATION_CLASS + ' ' + PANEL_CLASS;

        var config = {
            attachTo: angular.element(document.body),
            controller: WeekNumberHelperController,
            controllerAs: 'ctrl',
            template: weekNumberHelperTemplate,
            panelClass: panelClass,
            position: position,
            locals: {
                options,
            },
            openFrom: ev,
            clickOutsideToClose: true,
            escapeToClose: true,
            focusOnOpen: false,
            zIndex: 84
        };

        $mdPanel.open(config);
    };

    return _this;
}]);