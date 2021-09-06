import DateHelperController from './date-helper.controller';
import dateHelperTemplate from './date-helper-template.html';

angular.module('formHelperModule').factory('dateHelperService', [
    '$mdPanel', 
    'PANEL_ELEVATION_CLASS', 
    'PANEL_CLASS', 
    function (
        $mdPanel, 
        PANEL_ELEVATION_CLASS, 
        PANEL_CLASS
    ) {
    var _this = this;

    /**
     * 
     * @param {object} options 
     */
    _this.updateDate = (ev, options) => {
        var position = $mdPanel.newPanelPosition()
                .relativeTo(options.target)
                .addPanelPosition($mdPanel.xPosition.OFFSET_END, $mdPanel.yPosition.ALIGN_BOTTOMS);
        var panelClass = 'date-helper-panel ' + PANEL_ELEVATION_CLASS + ' ' + PANEL_CLASS;
        
        var config = {
            attachTo: angular.element(document.body),
            controller: DateHelperController,
            controllerAs: 'ctrl',
            template: dateHelperTemplate,
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
}])