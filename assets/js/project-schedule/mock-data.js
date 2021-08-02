const resources = [
    {
        id: 1,
        cdtTrx: 'Borisav',
        constructionSite: 'ADF 001',
        workType: 'T.C.E',
        area: '',
        turnover: 299862.32,
        invoiced: '',
        remainsToInvoice: 299862.32,
    },
    {
        id: 2,
        cdtTrx: 'Borisav',
        constructionSite: 'ADF 001',
        workType: 'Travaux sur existant',
        area: '',
        turnover: 9786.99,
        invoiced: '',
        remainsToInvoice: 9786.99,
    },
    {
        id: 3,
        cdtTrx: 'Drago',
        constructionSite: 'ADAPEI 03',
        workType: 'Travaux sur existant',
        area: '',
        turnover: 3527.4,
        invoiced: '',
        remainsToInvoice: 3527.4,
    },
    {
        id: 4,
        cdtTrx: 'J.B.V',
        constructionSite: 'ATHENA 001',
        workType: 'Oss Couv Bard Serr',
        area: '',
        turnover: 615000,
        invoiced: 504890.59,
        remainsToInvoice: 110109.41,
    },
    {
        id: 5,
        cdtTrx: 'Jéremy',
        constructionSite: '2GBSIC 01',
        workType: 'Oss Couv Bard',
        area: '',
        turnover: 210976.32,
        invoiced: 194154.65,
        remainsToInvoice: 16821.67,
    },
];

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
const buildColumns = function(numberFormat) {
    return [
        {
            label: 'Cdt Trx',
            field: 'prospect.clientNumber',
            width: 145,
            // sticky: true,
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
            width: 150,
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
            }
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
            width: 200,
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
            width: 180,
        },
    ];
};

/**
 * Array of object
 * Structure
 *      [
 *          {
 *              resource: number,
 *              title: string,
 *              start: Moment,
 *              end: Moment,
 *              backgroundColor: string,
 *              color: string,
 *         },
 *         ...
 *      ]
 */
var bg = 'rgba(197, 217, 241, .5)';
var color ='#000';
var group = 'group-test';

const events = [
    // {
    //     id: 1,
    //     resource: 1,
    //     title: 'Madagascar',
    //     start: moment('2021-01-01', 'YYYY-MM-DD').startOf('week'),
    //     end: moment('2021-03', 'YYYY-MM').endOf('month'),
    //     backgroundColor: bg,
    // },
    // {
    //     id: 2,
    //     resource: 5,
    //     title: '*',
    //     start: moment('2021-01-01', 'YYYY-MM-DD').startOf('week'),
    //     end: moment('2021-01-01', 'YYYY-MM-DD').endOf('week'),
    //     // start: moment('2021-01-01', 'YYYY-MM-DD').startOf('week'),
    //     // end: moment('2021-01-01', 'YYYY-MM-DD').endOf('week'),
    //     backgroundColor: 'red',
    // },
    // {
    //     id: 6,
    //     resource: 3,
    //     title: 'test',
    //     start: moment('2021-03-01', 'YYYY-MM-DD'),
    //     end: moment('2021-05-01', 'YYYY-MM-DD'),
    //     backgroundColor: 'brown',
    //     color: '#fff',
    //     className: 'scheduler-overflow-test',
    //     group: string,
    //     bubbleHtml: `
    //         <div>lorem ipsum dolor </div>
    //         <div>lorem ipsum dolor </div>
    //         <div>lorem ipsum dolor </div>
    //         <div>lorem ipsum dolor </div>
    //         <div>lorem ipsum dolor </div>
    //         <div>lorem ipsum dolor </div>
    //         <div>lorem ipsum dolor </div>
    //         <div>lorem ipsum dolor </div>
    //     `,
    // },
];

export {
    resources,
    buildColumns,
    events,
};