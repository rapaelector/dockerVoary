import UserHelperController from './user-helper.controller';
import userHelperTemplate from './user-helper-template.html';

angular.module('formHelperModule').factory('userHelperService', ['$http', '$mdPanel', function ($http, $mdPanel) {
    var _this = {};

    _this.selectUser = (ev, options) => {
        var position = $mdPanel.newPanelPosition()
            .relativeTo(options.target)
            .addPanelPosition($mdPanel.xPosition.OFFSET_END, $mdPanel.yPosition.ABOVE);

        var config = {
            attachTo: angular.element(document.body),
            controller: UserHelperController,
            controllerAs: 'ctrl',
            template: userHelperTemplate,
            panelClass: 'user-helper-container-panel',
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