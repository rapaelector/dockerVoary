import DateHelperController from './date-helper.controller';
import dateHelperTemplate from './date-helper-template.html';

angular.module('formHelperModule').factory('dateHelperService', ['$mdPanel', function ($mdPanel) {
    var _this = this;

    /**
     * 
     * @param {object} options 
     */
    _this.updateDate = (ev, options) => {
        console.info(options.target);
        var position = $mdPanel.newPanelPosition()
                .relativeTo(options.target)
                .addPanelPosition($mdPanel.xPosition.OFFSET_END, $mdPanel.yPosition.ABOVE);

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