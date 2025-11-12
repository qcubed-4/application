<?php
    /* Form State Handler. Determines which class is used to serialize the form in-between Ajax callbacks.
     *
     * Possible values are:
     * "DefaultHandler": This is the "standard" FormState handler, storing the base64 encoded session data
     * (and if requested by QForm, encrypted) as a hidden form variable on the page itself.
     *
     * "SessionHandler": A simple session-based FormState handler. Uses PHP Sessions, so it's very simple
     * and easy, using PHP's own session management and cleanup functions.
     * The downside is that for long-running sessions, each individual session file can get
     * very, very large, storing all the different form state data. Eventually (if individual
     * session files are larger than 10 MB), you can theoretically observe a geometric performance degradation.
     *
     * "FileHandler": This will store the formstate in a pre-specified directory (FILE_FORM_STATE_HANDLER_PATH)
     * on the file system. This offers a significant speed advantage over PHP SESSION because EACH
     * form state is saved in its own file, and only the form state that is needed for loading will
     * be accessed (as opposed to with a session, ALL the form states are loaded into memory
     * every time).
     * The downside is that because it doesn't utilize PHP's session management subsystem,
     * this class must take care of its own garbage collection/deleting of old/outdated
     * formstate files.
     *
     * "DatabaseHandler": This will store the formstate in a predefined table in one of the DBs in the array above.
     *    It provides a way to maintain the FormStates without creating too many files on the server.
     *    It also makes sure that the application remains fast and provides all the features of FileHandler.
     *    The algorithm to periodically clean up the DB is also provided (just like FileHandler).
     *
     *    To use the DatabaseHandler, the following two constants must be defined:
     *       1. DB_BACKED_FORM_STATE_HANDLER_DB_INDEX: It is the numerical index of the DB from the list of DBs defined
     *             above where the table to store the FormStates is present. Note, it is the numerical Index, not the DB name.
     *             e.g., If it is present in the DB_CONNECTION_1, then the value must be defined as '1'.
     *       2. DB_BACKED_FORM_STATE_HANDLER_TABLE_NAME: It is the name of the table where the FormStates must be stored
     *              It must have the following 4 columns:
     *                  i) page_id: varchar(80) - It must be the primary key.
     *                 ii) save_time: integer - This column should be indexed for performance reasons
     *                iii) session_id: varchar(32) - This column should be indexed for performance reasons
     *                 iv) state_data: text - This column must NOT be indexed otherwise it will degrade the performance.
     *
     * NOTE: Formstates can be large, depending on the complexity of your forms.
     *       For MySQL, you might have to increase the max_allowed_packet variable in your my.cnf file to the maximum size of a formstate.
     *       Also for MySQL, you should choose a MEDIUMTEXT type of column, rather than TEXT. TEXT is limited to 64KB,
     *       which will not be big enough for moderately complex forms and will result in data errors.
     */

    const FORM_STATE_HANDLER = '\\QCubed\\FormState\\SessionHandler';
    //const FORM_STATE_HANDLER = '\\QCubed\\FormState\\DefaultHandler';
    //const FORM_STATE_HANDLER = '\QCubed\FormState\FileHandler';


    // If using the FileHandler, specify the path where QCubed will save the session state files (has to be writeable!)
    const FILE_FORM_STATE_HANDLER_PATH = QCUBED_PROJECT_DIR . '/tmp/cache/qc_formstate';

    // If using the SessionHandler, define the DB index where the table to store the formstates is present
    const DB_BACKED_FORM_STATE_HANDLER_DB_INDEX = 1;
    // If using SessionHandler, specify the table name which would hold the formstates (must meet the requirements laid out above)
    const DB_BACKED_FORM_STATE_HANDLER_TABLE_NAME = 'qc_formstate';

    // If using the RedisHandler, set the constants to the correct data
    // Note! Please correct the RedisHandler according to the Redis user guide!

    const REDIS_BACKED_FORM_STATE_HANDLER_CONFIG = null;
    const REDIS_BACKED_FORM_STATE_HANDLER_ENCRYPTION_KEY = null;
    const REDIS_BACKED_FORM_STATE_HANDLER_IV_HASH_KEY = null;



