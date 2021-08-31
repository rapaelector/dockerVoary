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
     * LOGO, PROFILE IMAGE FIELDS
     */
    {
        width: '50px',
        cols: [
            'user_profile_image',
            'user_can_login',
            'pc_deposit',
            'project_pc_deposit',
            'architect',
            'project_architect',
            'project_visible_in_planning',
        ],
        className: '',
    },

    /**
     * ACTIONS FIELD
     */
    {
        width: '70px',
        cols: [
            'actions',
        ],
        className: 'text-center',
    },

    /**
     * SHORT DATE FIELD
     */
    {
        width: '65px',
        cols: [
            'project_global_amount',
            'economist',
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
            'project_last_relaunch',
            'deadline',
            'realization_quotation_date',
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
        cols: [
            'week_number_for_submission_of_the_study',
        ],
    },

    /**
     * - MIDDLE NUMBER
     */
    {
        width: '60px',
        cols: [
            'global_amount',
        ],
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
            'completion',
            'project_completion',
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
            'project_1090',
            'project_postal_code',
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
            'prospect_business_charge',
            'user_firstName',
            'user_lastName',
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
            'project_contact_name',
            'project_roadmap',
            'project_description_area',
            'project_site_address',
            'business_charge',
            'current_case_folder_name_on_the_server',
            'market_type',
            'project_market_type',
            'work_schedule',
            'project_work_schedule',
            'comment',
            'project_business_charge',
            'project_economiste',
            'nature_of_the_costing',
            'project_folder_name_on_the_server',
            'site_address_postal_code',
            'location',
            'activity',
            'area',
            'estimated_study_time',
            'effective_study_time',
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
        width: '100px',
        cols: [
            'client_name',
            'project_prospect',
        ],
        className: 'text-left',
    },
    /**
     * - EXTRA FIELDS
     * - FIELDS WITH VERY LONG CONTENTS
     */
    {
        width: '100px',
        cols: [
            'prospect_name',
            'project_prospect',
        ],
        className: 'text-left',
    },
];
