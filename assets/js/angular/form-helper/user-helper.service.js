import UserHelperController from './user-helper.controller';
import userHelperTemplate from './user-helper-template.html';

angular.module('formHelperModule').factory('userHelperService', [
    '$http', 
    '$mdPanel', 
    'PANEL_ELEVATION_CLASS', 
    'PANEL_CLASS', 
    function (
        $http, 
        $mdPanel, 
        PANEL_ELEVATION_CLASS,
        PANEL_CLASS,
    ) {
    var _this = {};

    _this.selectUser = (ev, options) => {
        var position = $mdPanel.newPanelPosition()
            .relativeTo(options.target)
            .addPanelPosition($mdPanel.xPosition.OFFSET_END, $mdPanel.yPosition.ALIGN_BOTTOMS);
        var panelClass = 'user-helper-container-panel ' + PANEL_ELEVATION_CLASS + ' ' + PANEL_CLASS;

        var config = {
            attachTo: angular.element(document.body),
            controller: UserHelperController,
            controllerAs: 'ctrl',
            template: userHelperTemplate,
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

    _this.getUsers = (userName, config, key) => {
        var timers = [];

		return new Promise(function (resolve, reject) {
			timers[key] = setTimeout(function () {
                $http.get(Routing.generate('load_plan.economists', {
                    q: userName
                }), config).then(function (response) {
                    resolve(response.data);
                }).catch(function (error) {
                    resolve([]);
                });
            }, 500);
        });
    };

    _this.getUser = (UserId) => {
        return $htt.get(Routing.generate('user.api_user', {id: userId}));
    };

    return _this;
}])