/**
 * COLUMN DEFS
 */
export default [
    /**
     * LOGO, PROFILE IMAGE FIELS
     */
    {
        width: '50px',
        cols: ['user_profile_image'],
        className: 'text-center',
    },
    /**
     * 
     */
    {
        width: '40px',
        cols: ['client_country'],
        className: '',
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
        cols: ['user_email', 'user_firstName', 'user_lastName'],
    },

    /**
     * Phone fields
     */
    {
        width: '100px',
        cols: ['user_phone', 'user_fax'],
    },

    /**
     * Job
     */
    {
        width: '150px',
        cols: ['user_job'],
    },

    /**
     * Date
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
        cols: ['client_number'],
        className: '',
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
        cols: ['client_created_at'],
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
        ],
        className: ''
    },

    /**
     * - EXTRA FIELDS
     * - FIELDS WITH VERY LONG CONTENTS
     */
    {
        width: '200px',
        cols: [],
        className: 'text-left dynamic-nowrap'
    },
];
