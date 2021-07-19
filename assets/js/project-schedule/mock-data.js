const resources = [{
        cdtTrx: 'Borisav',
        constructionSite: 'ADF 001',
        workType: 'T.C.E',
        area: '',
        turnover: 299862.32,
        invoiced: '',
        remainsToInvoice: 299862.32,
    },
    {
        cdtTrx: 'Borisav',
        constructionSite: 'ADF 001',
        workType: 'Travaux sur existant',
        area: '',
        turnover: 9786.99,
        invoiced: '',
        remainsToInvoice: 9786.99,
    },
    {
        cdtTrx: 'Drago',
        constructionSite: 'ADAPEI 03',
        workType: 'Travaux sur existant',
        area: '',
        turnover: 3527.4,
        invoiced: '',
        remainsToInvoice: 3527.4,
    },
    {
        cdtTrx: 'J.B.V',
        constructionSite: 'ATHENA 001',
        workType: 'Oss Couv Bard Serr',
        area: '',
        turnover: 615000,
        invoiced: 504890.59,
        remainsToInvoice: 110109.41,
    },
    {
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
 *  formatter: function (res, resource, index)
 * }
 *
 * @param {callback} numberFormat 
 * @returns {array} array of object
 */
const buildColumns = function(numberFormat) {
    return [{
            label: 'Cdt Trx',
            field: 'cdtTrx',
            headerStyle:  {
                'color': 'red',
                'background': 'blue', 
                'width': '200px',
            },
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
                console.info(column);

                return column ? `
                    <div 
                        class="dynamic-nowrap" 
                        title="` + column.label + `" 
                        data-toogle="tooltip" 
                        data-container="body" 
                        data-placement="top"
                    >` + column.label + `</div>` : '';
            },
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
                return 'lorem-ipsum-dolor';
            },
            /**
             * Change resource header content to html
             * 
             * @param {Object} column 
             * @param {any} index 
             * @returns {string} part of html
             */
            headerColumnFormatter: function (column, index) {
                console.info(column);

                return column ? `
                    <div 
                        class="dynamic-nowrap" 
                        title="` + column.label + `" 
                        data-toogle="tooltip" 
                        data-container="body" 
                        data-placement="top"
                    >` + column.label + `</div>` : '';
            },
        },
        {
            label: 'Surface en m2',
            field: 'area',
            headerClassName: 'text-uppercase text-nowrap text-truncate',
        },
        {
            label: "Chiffre d'affaire",
            field: 'turnover',
            className: 'text-right',
            headerClassName: 'text-uppercase text-nowrap text-truncate',
            formatter: function(res, resource, index) {
                return res ? (numberFormat(res, 2, ',', ' ') + ' €') : '';
            }
        },
        {
            label: 'Deja facture',
            field: 'invoiced',
            className: 'text-right',
            headerClassName: 'text-uppercase text-nowrap text-truncate',
            formatter: function(res, resource, index) {
                return res ? (numberFormat(res, 2, ',', ' ') + ' €') : '';
            }
        },
        {
            label: 'Reste a facturer',
            field: 'remainsToInvoice',
            className: 'text-right',
            headerClassName: 'text-uppercase text-nowrap text-truncate',
            formatter: function(res, resource, index) {
                return res ? (numberFormat(res, 2, ',', ' ') + ' €') : '';
            }
        },
    ];
};

const yearFormatter = function (value, index) {
    return '<div class="text-center text-uppercase">'+ value +'</div>';
}

const monthFormatter = function (value, index) {
    return '<div class="text-center"> '+ value + ' </div>';
}

export {
    resources,
    buildColumns,
    yearFormatter,
    monthFormatter,
};