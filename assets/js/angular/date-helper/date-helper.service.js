import DateHelperController from './date-helper.controller';
import dateHelperTemplate from './template.html';

angular.module('dateHelperModule').factory('dateHelperService', ['$mdPanel', function ($mdPanel) {
    var _this = this;

    /**
     * 
     * @param {object} options 
     */
    _this.updateDate = (ev, options) => {
        var position = $mdPanel.newPanelPosition()
                .relativeTo('.' + options.targetClass)
                .addPanelPosition($mdPanel.xPosition.ALIGN_START, $mdPanel.yPosition.BELOW);

        var config = {
            attachTo: angular.element(document.body),
            controller: DateHelperController,
            controllerAs: 'ctrl',
            template: dateHelperTemplate,
            panelClass: 'date-helper-panel',
            position: position,
            locals: {
                options,
            },
            openFrom: ev,
            clickOutsideToClose: true,
            escapeToClose: true,
            focusOnOpen: false,
            zIndex: 10
        };

        $mdPanel.open(config);
    };

    return _this;
}])