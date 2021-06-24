/**
 * COLUMN DEFS
 */
// 'project_prospect',

export default [
    /**
     * LOGO, VERY LITLE LOGO (eg: flag, brand etc.)
     * WITH VERY SHORT LABEL
     */
    {
        width: '40px',
        cols: ['client_country'],
        className: '',
    },

    /**
     * LOGO, PROFILE IMAGE FIELS
     */
    {
        width: '50px',
        cols: [
            'user_profile_image',
            'user_can_login',
            'pc_deposit',
            'architect',
        ],
        className: 'text-center',
    },

    /**
     * ACTIONS FIELD
     */
    {
        width: '70px',
        cols: ['actions'],
        className: 'text-center',
    },

    /**
     * SHORT DATE FIELD
     */
    {
        width: '65px',
        cols: [
            'project_global_amount',
        ],
    },
    /**
     * DATE
     */
    {
        width: '75px',
        cols: [
            'created_at', 
            'updated_at', 
            'deleted_at',
            'last_relaunch',
        ],
        className: 'text-center',
    },

    /**
     * - VERY SHORT NUMBER OR TEXT COLUMNS
     * - NUMBER FIELDS WITH VERY SHORT DATA
     * - MAX 4 digit max (eg: 9999)
     */
    {
        width: '30px',
        cols: [],
    },

    /**
     * - MIDDLE NUMBER
     */
    {
        width: '60px',
        cols: [
            'global_amount',]
        ,
    },
    /**
     * - NUMBER COLUMNS
     * - NUMBER FIELDS
     */
    {
        width: '80px',
        cols: [
            'client_number',
            'project_amount_subcontracted_work',
            'project_amount_bbi_specific_work',
            'archivement_pourcentage',
        ],
    },

    /**
     * - DATE COLUMNS
     * - DATE FIELDS
     */
    {
        width: '80px',
        cols: [
            'client_created_at',
        ],
        className: 'text-center',
    },

    /**
     * PHONE FIELDS
     */
    {
        width: '100px',
        cols: ['user_phone', 'user_fax'],
    },

    /**
     * - VERY SHORT TEXT
     */
    {
        width: '50px',
        cols: [
            'project_site_code',
        ]
    },
    /**
     * - SHORT TEXT COLUMNS
     * - LONG TEXT COLUMNS
     * - VARCHAR COLUMNS (MAX 255 CARACTERE)
     */
    {
        width: '100px',
        cols: [
            'client_short_name',
            'client_activity',
            'client_tva_rate',
            'client_siret',
            'client_payment_method',
            'client_payment',
            'client_intra_community_tva',
            'client_type',
            'client_postal_code',
            'contact_name',
            'project_roadmap',
            'project_description_area',
            'project_site_address',
            'business_charge',
            'current_case_folder_name_on_the_server',
            'market_type',
            'project_market_type',
            'work_schedule',
            'comment',
            'project_business_charge',
            'project_economiste',
        ],
        className: ''
    },
    /**
     * - MIDDLE TEXT COLUMNS
     * - SOME NUMBER FIELDS ARE CONSIDERED TO A STRING IF ITS CONTENT IS TOO LONG
     */
    {
        width: '150px',
        cols: [
            'user_job',
            'user_email',
            'user_first_name',
            'user_last_name',
            'project_user_email',
        ]
    },
    /**
     * - EXTRA FIELDS
     * - FIELDS WITH VERY LONG CONTENTS
     */
    {
        width: '200px',
        cols: [
            'client_name',
            'project_prospect',
        ],
        className: 'text-left',
    },
];
