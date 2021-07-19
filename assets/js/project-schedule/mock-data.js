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

const buildColumns = function(numberFormat) {
    return [{
            label: 'Cdt Trx',
            field: 'cdtTrx',
        },
        {
            label: 'Chantier',
            field: 'constructionSite',
            className: 'chantier-class',
            headerClassName: 'text-uppercase text-center',
            formatter: function(res, resource, index) {
                return res ? '<b>' + res + '</b>' : '';
            },
        },
        {
            label: 'Type de travaux',
            field: 'workType',
            headerClassName: 'text-uppercase',
            classNameFormatter: function(res, resource, index) {
                return 'lorem-ipsum-dolor';
            },
        },
        {
            label: 'Surface en m2',
            field: 'area',
            headerClassName: 'text-uppercase',
        },
        {
            label: "Chiffre d'affaire",
            field: 'turnover',
            className: 'text-right',
            headerClassName: 'text-uppercase',
            formatter: function(res, resource, index) {
                return res ? (numberFormat(res, 2, ',', ' ') + ' €') : '';
            }
        },
        {
            label: 'Deja facture',
            field: 'invoiced',
            className: 'text-right',
            headerClassName: 'text-uppercase',
            formatter: function(res, resource, index) {
                return res ? (numberFormat(res, 2, ',', ' ') + ' €') : '';
            }
        },
        {
            label: 'Reste a facturer',
            field: 'remainsToInvoice',
            className: 'text-right',
            headerClassName: 'text-uppercase',
            formatter: function(res, resource, index) {
                return res ? (numberFormat(res, 2, ',', ' ') + ' €') : '';
            }
        },
    ];
};

const yearFormatter = function (value, index) {
    console.info({value, index});

    return '<div class="text-center text-uppercase">'+ value +'</div>';
}

const monthFormatter = function (value, index) {
    console.info({value, index});

    return '<div class="text-center"> '+ value + ' </div>';
}

export {
    resources,
    buildColumns,
    yearFormatter,
    monthFormatter,
};