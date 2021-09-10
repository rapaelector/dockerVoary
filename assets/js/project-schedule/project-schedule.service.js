angular.module('projectScheduleApp').factory('projectSchedulerService', [
    '$http', 
    'fosJsRouting', 
    'moment',
    'MESSAGES',
    function (
        $http, 
        fosJsRouting,
        moment,
        MESSAGES
    ) {
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
            var events = response.data.events.map(event => ({
                ...event,
                start: moment(event.start),
                end: moment(event.end),
            }));
            var totals = response.data.totals;

            return {
                events,
                totals,
            };
        });
    };

    
    /**
     * Array of object represent the resources columns can have the following attributes
     * {
     *   label: {String},
     *   field: {String},
     *   headerStyle: {Object} list of style
     *      e.g: {'color': any, border: any, ...}
     *  
     *   Class to bind to the column header only
     *   Column cell are not affected by those classe
     *   headerClassName: 'text-uppercase text-nowrap text-truncate',
     *   Give class to each column cell
     *   Class no affected to the column header
     * 
     *   @param {Object} res 
     *   @param {Object} resource 
     *   @param {any} index 
     *   @returns {string}
     *  classNameFormatter: function(res, resource, index) {return 'lorem-ipsum-dolor';},
     *  
     *   Change resource header content to html
     *   @param {Object} column 
     *   @param {any} index 
     *   @returns {string} part of html
        headerColumnFormatter: function (column, index) {
            return column ? `
                <div 
                    class="dynamic-nowrap" 
                    title="` + column.label + `" 
                    data-toggle="tooltip" 
                    data-container="body" 
                    data-placement="top"
                >` + column.label + `</div>` : '';
        },
    *  formatter: function (res, resource, index),
        widht: {string, number}
    * }
    *
    * @param {callback} numberFormat 
    * @returns {array} array of object
    */
    _this.buildColumns = function(numberFormat) {
        return [
            {
                // label: 'Cdt Trx',
                label: MESSAGES.columns.businessCharge,
                field: 'businessCharge.name',
                width: 100,
                className: 'dynamic-nowrap',
                headerClassName: 'text-uppercase',
                sticky: true,
                isTotal: false,
            },
            {
                // label: 'Chantier',
                label: MESSAGES.columns.projectName,
                field: 'name',
                className: 'chantier-class',
                headerClassName: 'text-uppercase text-center dynamic-nowrap',
                formatter: function(res, resource, index) {
                    return res ? '<div class="test" title="'+ res +'">' + res + '</div>' : '';
                },
                headerColumnFormatter: function (column, index) {
                    return column ? `
                        <div class="dynamic-nowrap" title="`+ column.label +`">`
                            + column.label +
                        `</div>` : '';
                },
                classNameFormatter: function(res, resource, index) {
                    return 'dynamic-nowrap';
                },
                width: 100,
                visible: true,
                isTotal: false,
            },
            {
                label: 'Type de travaux',
                field: 'marketType',
                /**
                 * Class to bind to the column header only
                 * Column cell are not affected by those classe
                 */
                headerClassName: 'text-uppercase text-nowrap text-truncate',
                /**
                 * Give class to each column cell
                 * Class no affected to the column header
                 * 
                 * @param {Object} res 
                 * @param {Object} resource 
                 * @param {any} index 
                 * @returns {string}
                 */
                classNameFormatter: function(res, resource, index) {
                    return 'dynamic-nowrap';
                },
                /**
                 * Change resource header content to html
                 * 
                 * @param {Object} column 
                 * @param {any} index 
                 * @returns {string} part of html
                 */
                headerColumnFormatter: function (column, index) {
                    return column ? `
                        <div 
                            class="dynamic-nowrap" 
                            title="` + column.label + `" 
                            data-toggle="tooltip" 
                            data-container="body" 
                            data-placement="top"
                        >` + column.label + `</div>` : '';
                },
                width: 150,
                formatter: function (res, resource, index) {
                    return res ? `<div class="dynamic-nowrap text-center" title="` + res + `">` + res + `</div>` : '';
                },
                visible: true,
                isTotal: false,
            },
            {
                label: "Chiffre d'affaire",
                field: 'globalAmount',
                className: 'text-right',
                headerClassName: 'text-uppercase text-nowrap text-truncate',
                formatter: function(res, resource, index) {
                    return res ? (numberFormat(res, 2, ',', ' ') + ' €') : '';
                },
                width: 100,
                headerColumnFormatter: function (column, index) {
                    return `<div class="dynamic-nowrap" title="` + column.label + `">` + column.label + `</div>`;
                },
                isTotal: false,
            },
        ];
    };

    /**
     * 
     * @param {Resource} resource 
     * @param {Column} column 
     * @returns 
     */
    _this.generateUrl = function (resource, column) {
        return fosJsRouting.generate('project.ng.project_follow_up', {id: resource.id});
    };

    /**
     * Get market type
     * @returns {promise}
     */
    _this.getConfig = function () {
        return $http.get(fosJsRouting.generate('project.ng.project_config'));
    };

    /**
     * 
     * @param {string} projectName 
     * @param {object} config 
     * @param {any} key 
     * @returns promise
     */
    _this.getProjects = function (projectName, config, key) {
        var timers = [];

		return new Promise(function (resolve, reject) {
			timers[key] = setTimeout(function () {
                $http.get(fosJsRouting.generate('load_plan.projects', {
                    q: projectName
                }), config).then(function (response) {
                    resolve(response.data);
                }).catch(function (error) {
                    resolve([]);
                });
            }, 500);
        });
	};

    /**
     * 
     * @param {number} projectId 
     * @returns {promise}
     */
    _this.getProject = (projectId) => {
        return $http.get(fosJsRouting.generate('project.ng.project', {id: projectId}));
    };
    
    /**
     * 
     * @param {number} projectId
     * @param {object} formData 
     * @return {promise}
     */
    _this.updateProject = (projectId, formData) => {
        return $http.post(fosJsRouting.generate('project.ng.update_project', {id: projectId}), formData);
    };

    return _this;    
}])