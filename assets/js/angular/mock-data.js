const resources = [
    {
        "id": 3,
        "siteCode": "qsdfqsdf",
        "marketType": "Travaux sur existant",
        "globalAmount": 29000,
        "amountSubcontractedWork": 98000,
        "amountBBISpecificWork": "32000",
        "caseType": [
            "Terrassement",
            "CVC / plomberie",
            "Charpente"
        ],
        "prospect": {
            "id": 1,
            "name": "Microsoft",
            "shortName": "Microsoft",
            "clientNumber": "PR0001",
            "projectDescription": {
                "area": "Lorem ipsum"
            }
        }
    },
    {
        "id": 5,
        "siteCode": null,
        "marketType": "Appel d’offre",
        "globalAmount": 76000,
        "amountSubcontractedWork": 329000,
        "amountBBISpecificWork": "32000",
        "caseType": [
            "Terrassement",
            "Gros œuvre",
            "CVC / plomberie",
            "Dossier administratif",
            "Serrurerie"
        ],
        "prospect": {
            "id": 1,
            "name": "Microsoft",
            "shortName": "Microsoft",
            "clientNumber": "PR0001",
            "projectDescription": {
                "area": "Lorem ipsum"
            }
        }
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
            visible: false,
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
            visible: false,
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
            width: 150,
            // sticky: true,
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
    {
        id: 14,
        start: moment('2020-12-30', 'YYYY-MM-DD'),
        end: moment('2021-01-27', 'YYYY-MM-DD'),
        type: "shade_house",
        project: 3,
        resource: 3,
        backgroundColor: "#1f497d",
        bubbleHtml: "\r\n            <div class=\"text-center\"> 2020/12/30 — 2021/01/27 </div>\r\n            "
    },
    {
        id: 15,
        start: moment('2021-04-30', 'YYYY-MM-DD'),
        end: moment('2021-02-20', 'YYYY-MM-DD'),
        type: "frame_assembly",
        project: 3,
        resource: 3,
        backgroundColor: "#00b050",
        bubbleHtml: "\r\n            <div class=\"text-center\"> 2021/04/30 — 2021/02/20 </div>\r\n            "
    },
    {
        id: 16,
        start: moment('2021-06-30', 'YYYY-MM-DD'),
        end: moment('2021-07-30', 'YYYY-MM-DD'),
        type: "isothermal_panels",
        project: 5,
        resource: 5,
        backgroundColor: "#f79646",
        bubbleHtml: "\r\n            <div class=\"text-center\"> 2021/06/30 — 2021/07/30 </div>\r\n            "
    },
    // {
    //     "id": 17,
    //     "start": "2021-10-18",
    //     "end": "2021-11-29",
    //     "type": "subcontracting",
    //     "project": {
    //         "id": 5
    //     },
    //     "resource": 5,
    //     "backgroundColor": "#da9694",
    //     "bubbleHtml": "\r\n            <div class=\"text-center\"> 2021/10/18 — 2021/11/29 </div>\r\n            "
    // },
    // {
    //     "id": 18,
    //     "start": "2021-07-13",
    //     "end": "2021-07-29",
    //     "type": "envelope",
    //     "project": {
    //         "id": 7
    //     },
    //     "resource": 7,
    //     "backgroundColor": "#ffc000",
    //     "bubbleHtml": "\r\n            <div class=\"text-center\"> 2021/07/13 — 2021/07/29 </div>\r\n            "
    // },
    // {
    //     "id": 19,
    //     "start": "2021-08-30",
    //     "end": "2021-09-15",
    //     "type": "frame_assembly",
    //     "project": {
    //         "id": 7
    //     },
    //     "resource": 7,
    //     "backgroundColor": "#00b050",
    //     "bubbleHtml": "\r\n            <div class=\"text-center\"> 2021/08/30 — 2021/09/15 </div>\r\n            "
    // },
    // {
    //     "id": 20,
    //     "start": "2021-04-30",
    //     "end": "2021-04-24",
    //     "type": "isothermal_panels",
    //     "project": {
    //         "id": 3
    //     },
    //     "resource": 3,
    //     "backgroundColor": "#f79646",
    //     "bubbleHtml": "\r\n            <div class=\"text-center\"> 2021/04/30 — 2021/04/24 </div>\r\n            "
    // },
    // {
    //     "id": 21,
    //     "start": "2021-06-02",
    //     "end": "2021-07-31",
    //     "type": "isothermal_panels",
    //     "project": {
    //         "id": 3
    //     },
    //     "resource": 3,
    //     "backgroundColor": "#f79646",
    //     "bubbleHtml": "\r\n            <div class=\"text-center\"> 2021/06/02 — 2021/07/31 </div>\r\n            "
    // },
    // {
    //     "id": 22,
    //     "start": "2020-12-31",
    //     "end": "2021-05-12",
    //     "type": "forecasts",
    //     "project": {
    //         "id": 7
    //     },
    //     "resource": 7,
    //     "backgroundColor": "#c5d9f1",
    //     "bubbleHtml": "\r\n            <div class=\"text-center\"> 2020/12/31 — 2021/05/12 </div>\r\n            "
    // },
    // {
    //     "start": "2021-01-25T00:00:00+00:00",
    //     "end": "2021-01-31T00:00:00+00:00",
    //     "id": "14611391d6f3d68",
    //     "resource": 3,
    //     "title": 9666.666666666666,
    //     "group": "payment",
    //     "backgroundColor": "transparent",
    //     "color": "#000",
    //     "bubbleHtml": "9.666,67 €"
    // },
    // {
    //     "start": "2021-07-26T00:00:00+00:00",
    //     "end": "2021-08-01T00:00:00+00:00",
    //     "id": "16611391d7009be",
    //     "resource": 5,
    //     "title": 38000,
    //     "group": "payment",
    //     "backgroundColor": "transparent",
    //     "color": "#000",
    //     "bubbleHtml": "38.000,00 €"
    // },
    // {
    //     "start": "2021-10-25T00:00:00+00:00",
    //     "end": "2021-10-31T00:00:00+00:00",
    //     "id": "17611391d700af2",
    //     "resource": 5,
    //     "title": 38000,
    //     "group": "payment",
    //     "backgroundColor": "transparent",
    //     "color": "#000",
    //     "bubbleHtml": "38.000,00 €"
    // },
    // {
    //     "start": "2021-07-26T00:00:00+00:00",
    //     "end": "2021-08-01T00:00:00+00:00",
    //     "id": "18611391d7015e5",
    //     "resource": 7,
    //     "title": 0,
    //     "group": "payment",
    //     "backgroundColor": "transparent",
    //     "color": "#000",
    //     "bubbleHtml": "0,00 €"
    // },
    // {
    //     "start": "2021-08-30T00:00:00+00:00",
    //     "end": "2021-09-05T00:00:00+00:00",
    //     "id": "19611391d70187b",
    //     "resource": 7,
    //     "title": 0,
    //     "group": "payment",
    //     "backgroundColor": "transparent",
    //     "color": "#000",
    //     "bubbleHtml": "0,00 €"
    // },
    // {
    //     "start": "2021-06-28T00:00:00+00:00",
    //     "end": "2021-07-04T00:00:00+00:00",
    //     "id": "21611391d7019e6",
    //     "resource": 3,
    //     "title": 9666.666666666666,
    //     "group": "payment",
    //     "backgroundColor": "transparent",
    //     "color": "#000",
    //     "bubbleHtml": "9.666,67 €"
    // },
    // {
    //     "start": "2021-07-26T00:00:00+00:00",
    //     "end": "2021-08-01T00:00:00+00:00",
    //     "id": "21611391d701b15",
    //     "resource": 3,
    //     "title": 9666.666666666666,
    //     "group": "payment",
    //     "backgroundColor": "transparent",
    //     "color": "#000",
    //     "bubbleHtml": "9.666,67 €"
    // },
    // {
    //     "start": "2021-01-25T00:00:00+00:00",
    //     "end": "2021-01-31T00:00:00+00:00",
    //     "id": "22611391d7024a3",
    //     "resource": 7,
    //     "title": 0,
    //     "group": "payment",
    //     "backgroundColor": "transparent",
    //     "color": "#000",
    //     "bubbleHtml": "0,00 €"
    // },
    // {
    //     "start": "2021-02-22T00:00:00+00:00",
    //     "end": "2021-02-28T00:00:00+00:00",
    //     "id": "22611391d7027af",
    //     "resource": 7,
    //     "title": 0,
    //     "group": "payment",
    //     "backgroundColor": "transparent",
    //     "color": "#000",
    //     "bubbleHtml": "0,00 €"
    // },
    // {
    //     "start": "2021-03-29T00:00:00+00:00",
    //     "end": "2021-04-04T00:00:00+00:00",
    //     "id": "22611391d702a97",
    //     "resource": 7,
    //     "title": 0,
    //     "group": "payment",
    //     "backgroundColor": "transparent",
    //     "color": "#000",
    //     "bubbleHtml": "0,00 €"
    // },
    // {
    //     "start": "2021-04-26T00:00:00+00:00",
    //     "end": "2021-05-02T00:00:00+00:00",
    //     "id": "22611391d702d96",
    //     "resource": 7,
    //     "title": 0,
    //     "group": "payment",
    //     "backgroundColor": "transparent",
    //     "color": "#000",
    //     "bubbleHtml": "0,00 €"
    // }
];

export {
    resources,
    buildColumns,
    events,
};