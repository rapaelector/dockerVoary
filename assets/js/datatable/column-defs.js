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
     * EMAIL FIELD
     */
    {
        width: '150px',
        cols: [
            'user_email',
            'user_firstName',
            'user_lastName',
            'project_user_email'
        ],
    },

    /**
     * PHONE FIELDS
     */
    {
        width: '100px',
        cols: ['user_phone', 'user_fax'],
    },

    /**
     * JOB
     */
    {
        width: '150px',
        cols: ['user_job'],
    },

    /**
     * DATE
     */
    {
        width: '75px',
        cols: ['created_at', 'updated_at', 'deleted_at'],
        className: 'text-center',
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
            'project_global_amount',
            'archivement_pourcentage',
        ],
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
     * - DATE COLUMNS
     * - DATE FIELDS
     */
    {
        width: '80px',
        cols: [
            'client_created_at',
            'last_relaunch',
        ],
        className: 'text-center',
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
            'global_amount',
            'business_charge',
            'code_postal',
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
            'market_type',
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
            'comment',
            'current_case_folder_name_on_the_server',
            'planning_project',
        ],
        className: 'text-left dynamic-nowrap',
    },
];
