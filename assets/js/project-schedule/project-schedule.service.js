angular.module('projectScheduleApp').factory('projectSchedulerService', ['$http', 'fosJsRouting', 'moment', function ($http, fosJsRouting, moment) {
    var _this = this;

    /**
     * 
     * @returns {promise}
     */
    _this.getResources = function () {
        return $http.get(fosJsRouting.generate('project_schedule.get_resources'));
    }
    
    /**
     * 
     * @param {object} date {startDate: moment, endDate: moment} 
     * @returns {promise}
     */
    _this.getEvents = function (date) {
        var start = moment().format('YYYY-MM-DD');
        var end = moment().format('YYYY-MM-DD');

        if (date && date.startDate) {
            start = moment(date.startDate).format('YYYY-MM-DD');
        }

        if (date && date.endDate) {
            end = moment(date.endDate).format('YYYY-MM-DD');
        }
        
        return $http.get(fosJsRouting.generate('project_schedule.get_events', {
            start: start,
            end: end,
        })).then(response => {
            return response.data.map(event => ({
                ...event,
                start: moment(event.start),
                end: moment(event.end),
            }))
        });
    };

    return _this;    
}])