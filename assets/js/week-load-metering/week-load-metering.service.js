angular.module('weekLoadMeteringModule').factory('weekLoadMeteringService', ['$http', 'fosJsRouting', 'moment', function ($http, fosJsRouting, moment)  {
    var _this = {};

    _this.getWeekLoadMetering = (date) => {
        var selectedDate = date ? moment(date).format('YYYY-MM-DD') : moment().format('YYYY-MM-DD');
        
        return $http.get(fosJsRouting.generate('load_plan.week_load_metering', {date: selectedDate}));
    };

    return _this;
}])