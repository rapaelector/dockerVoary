/**
 * COLUMN DEFS
 */
export default [
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
        cols: [],
        className: 'text-right',
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
        cols: [],
        className: 'text-center',
    },
    /**
     * - SHORT TEXT COLUMNS
     * - LONG TEXT COLUMNS
     * - VARCHAR COLUMNS (MAX 255 CARACTERE)
     */
    {
        width: '100px',
        cols: [],
        className: 'text-left dynamic-nowrap text-uppercase'
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
