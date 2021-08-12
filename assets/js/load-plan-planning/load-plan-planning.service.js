angular.module('loadPlanPlanningApp').factory('loadPlanPlanningService', ['$http', 'fosJsRouting', 'moment', function ($http, fosJsRouting, moment) {
    var _this = this;

    _this.getResources = () => {
        return $http.get(fosJsRouting.generate('load_plan_planning.resources'));
    };

    _this.getEvents = (queryParams) => {
        return $http.get(fosJsRouting.generate('load_plan_planning.events', queryParams)).then((response) => {
            return response.data.events.map(event => ({
                ...event,
                start: moment(event.start),
                end: moment(event.end),
            }))
        });
    };

    return _this;
}]);