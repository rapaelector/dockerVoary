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
                label: 'Cdt Trx',
                field: 'prospect.clientNumber',
                width: 100,
                sticky: true,
            },
            {
                label: 'Chantier',
                field: 'siteCode',
                className: 'chantier-class',
                headerClassName: 'text-uppercase text-center',
                formatter: function(res, resource, index) {
                    return res ? '<div class="test" title="'+ res +'">' + res + '</div>' : '';
                },
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
                classNameFormatter: function(res, resource, index) {
                    return 'dynamic-nowrap';
                },
                width: 130,
                visible: true,
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
                width: 190,
                formatter: function (res, resource, index) {
                    return res ? `<div class="dynamic-nowrap text-center" title="` + res + `">` + res + `</div>` : '';
                },
                visible: true,
            },
            {
                label: 'Surface en m2',
                field: 'prospect.projectDescription.area',
                headerClassName: 'text-uppercase text-nowrap text-truncate',
                width: 150,
                // sticky: true,
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
            },
            {
                label: 'Deja facture',
                field: 'amountSubcontractedWork',
                className: 'text-right',
                headerClassName: 'text-uppercase text-nowrap text-truncate',
                formatter: function(res, resource, index) {
                    return res ? (numberFormat(res, 2, ',', ' ') + ' €') : '';
                },
                width: 100,
            },
            {
                label: 'Reste a facturer',
                field: 'amountBBISpecificWork',
                className: 'text-right',
                headerClassName: 'text-uppercase text-nowrap text-truncate',
                formatter: function(res, resource, index) {
                    return res ? (numberFormat(res, 2, ',', ' ') + ' €') : '';
                },
                width: 100,
                // sticky: true,
            },
        ];
    };

    return _this;    
}])