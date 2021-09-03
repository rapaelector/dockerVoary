import UserHelperController from './user-helper.controller';
import userHelperTemplate from './user-helper-template.html';

angular.module('formHelperModule').factory('userHelperService', ['$http', '$mdPanel', function ($http, $mdPanel) {
    var _this = {};

    _this.selectUser = (ev, options) => {
        var position = $mdPanel.newPanelPosition()
            .relativeTo('.' + options.targetClass)
            .addPanelPosition($mdPanel.xPosition.ALIGN_START, $mdPanel.yPosition.BELOW);

        var config = {
            attachTo: angular.element(document.body),
            controller: UserHelperController,
            controllerAs: 'ctrl',
            template: userHelperTemplate,
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

    return _this;
}])