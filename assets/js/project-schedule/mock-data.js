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
                data-toogle="tooltip" 
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
            field: 'cdtTrx',
            width: 145,
        },
        {
            label: 'Chantier',
            field: 'constructionSite',
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
                        data-toogle="tooltip" 
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
            field: 'workType',
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
                        data-toogle="tooltip" 
                        data-container="body" 
                        data-placement="top"
                    >` + column.label + `</div>` : '';
            },
            width: 190,
        },
        {
            label: 'Surface en m2',
            field: 'area',
            headerClassName: 'text-uppercase text-nowrap text-truncate',
            width: 150,
        },
        {
            label: "Chiffre d'affaire",
            field: 'turnover',
            className: 'text-right',
            headerClassName: 'text-uppercase text-nowrap text-truncate',
            formatter: function(res, resource, index) {
                return res ? (numberFormat(res, 2, ',', ' ') + ' €') : '';
            },
            width: 200,
        },
        {
            label: 'Deja facture',
            field: 'invoiced',
            className: 'text-right',
            headerClassName: 'text-uppercase text-nowrap text-truncate',
            formatter: function(res, resource, index) {
                return res ? (numberFormat(res, 2, ',', ' ') + ' €') : '';
            },
            width: 100,
        },
        {
            label: 'Reste a facturer',
            field: 'remainsToInvoice',
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
                title: string,
                start: Moment,
                end: Moment,
                backgroundColor: string,
                color: string,
 *          },
            ...
 *      ]
 */
const events = [
    {
        resource: 1,
        title: 'test',
        start: moment().add(1, 'month'),
        end: moment().add(2, 'month'),
        backgroundColor: 'orange',
        color: '#fff',
        bubbleHtml: `
            <div>lorem ipsum dolor </div>
            <div>lorem ipsum dolor </div>
            <div>lorem ipsum dolor </div>
            <div>lorem ipsum dolor </div>
            <div>lorem ipsum dolor </div>
            <div>lorem ipsum dolor </div>
            <div>lorem ipsum dolor </div>
            <div>lorem ipsum dolor </div>
        `,
        style: {
            textTransform: 'capitalize',
        },
        className: 'test-class-lorem lorem',
    },
    {
        resource: 2,
        title: '',
        start: moment().add(-3, 'month'),
        end: moment().add(2, 'month'),
        backgroundColor: 'rgba(0, 0, 255, .2)',
        color: '#000',
        bubbleHtml: `<div>------lorem ipsum dolor------</div>`,
        className: '------------lorem-------------',
    },
    {
        resource: 3,
        title: 'test',
        start: moment().add(-6, 'month'),
        end: moment().add(-5, 'month'),
        backgroundColor: 'red',
        color: '#fff',
        bubbleHtml: `<div>++++++++lorem ipsum dolor++++++</div>`,
        className: '++++lorem+++++++++',
    },
    {
        resource: 4,
        title: 'test',
        start: moment().add(-1, 'month'),
        end: moment().add(1, 'month'),
        backgroundColor: 'grey',
        color: '#fff',
        bubbleHtml: '<div>******** lorem ipsum dolor **************** </div>',
        className: '**********lorem*******',
    },
    {
        resource: 1,
        title: 'test',
        start: moment().add(2, 'month'),
        end: moment().add(3, 'month'),
        backgroundColor: 'green',
        color: '#fff',
        bubbleHtml: '<div> ///////// lorem <b>ipusm</b> dolor /////////////</div>',
        className: '//////// lorem ///////',
    },
    {
        resource: 3,
        title: 'test',
        start: moment().add(2, 'month'),
        end: moment().add(3, 'month'),
        backgroundColor: 'brown',
        color: '#fff',
        className: '\\\\\\\\\\\ lorem ///////',
    },
];

export {
    resources,
    buildColumns,
    events,
};