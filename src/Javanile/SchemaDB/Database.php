<?php
/**
 * Class that handle a connection with database.
 *
 * PHP version 5.4
 *
 * @author Francesco Bianco
 */

namespace Javanile\SchemaDB;

use Javanile\SchemaDB\Database\Socket;

class Database implements Notations
{
    use Database\ModelApi;
    use Database\ErrorApi;
    use Database\SocketApi;
    use Database\SchemaApi;

    /**
     * Release version number.
     * 
     * @var string
     */
    const VERSION = '0.3.0';

    /**
     * Constructor arguments passed.
     *
     * @var array
     */
    private $_args = null;

    /**
     * Connected for database socket.
     *
     * @var type
     */
    private $_socket = null;

    /**
     * Connected for database sql writer or composer.
     *
     * @var object
     */
    private $_writer = null;

    /**
     * Connected for database schema parser or interpreter.
     *
     * @var object
     */
    private $_parser = null;

    /**
     * Database status ready for queries.
     *
     * @var bool
     */
    private $_ready = null;

    /**
     * Trace for debugging.
     *
     * @var object
     */
    private $_trace = null;

    /**
     * Timestamp for benchmark.
     *
     * @var float
     */
    private $_ts = null;

    /**
     * Constant to enable debug print-out.
     *
     * @var bool
     */
    private $_debug = false;

    /**
     * Database instance for singleton or default implicit call.
     *
     * @var object
     */
    protected static $_defaultDatabase = null;

    /**
     * Construct and connect a SchemaDB drive
     * to mysql database best way to start use it.
     *
     * @param array $args Array with connection parameters
     */
    public function __construct($args)
    {
        //
        $this->_ts = microtime();

        //
        $this->_trace = debug_backtrace();

        // check arguments for connection
        foreach (['host', 'dbname', 'username'] as $attr) {
            if (!isset($args[$attr])) {
                $this->errorConnect("Required attribute: '{$attr}'");
            }
        }

        //
        $this->_args = $args;

        //
        $this->_socket = new Socket($this->_args);

        //
        $this->_parser = new Parser\Mysql();

        //
        $this->_writer = new Writer\Mysql();

        //
        $this->_ready = false;

        //
        static::setDefault($this);
    }
    
    /**
     * Retrieve default SchemaDB connection.
     *
     * @return type
     */
    public static function getDefault()
    {
        // return static $default
        return static::$_defaultDatabase;
    }

    /**
     * Set global context default database
     * for future use into model management.
     *
     * @param type $database
     */
    public static function setDefault($database)
    {
        // if no default SchemaDB connection auto-set then-self
        if (static::$_defaultDatabase === null) {

            // set current SchemaDB connection to default
            static::$_defaultDatabase = &$database;
        }
    }

    /**
     * Get the status of database connection.
     *
     * @return bool Database status flag.
     */
    public function isReady()
    {
        //
        if (!$this->_ready) {
            $this->enquire();
        }

        //
        return $this->_ready;
    }

    /**
     * Retrieve current parser.
     *
     * @return object Current parser
     */
    public function getParser()
    {
        //
        return $this->_parser;
    }

    /**
     * Retrieve current writer.
     *
     * @return object Current writer
     */
    public function getWriter()
    {
        //
        return $this->_writer;
    }

    /**
     * Debug mode setter.
     *
     * @param bool $flag Set True to enable debug mode.
     */
    public function setDebug($flag)
    {
        //
        $this->_debug = (boolean) $flag;
    }

    /**
     * Debug mode getter.
     *
     * @return bool Return debug mode status.
     */
    protected function getDebug()
    {
        //
        return $this->_debug;
    }
    
    /**
     * Print-out a memory used benchmark.
     *
     * @return float Time elapsed.
     */
    public function benchmark()
    {
        //
        $delta = microtime()-$this->_ts;

        //
        $style = 'background:#333;'
               . 'color:#fff;'
               . 'padding:2px 6px 3px 6px;'
               . 'border:1px solid #000';

        //
        $infoline = 'Time: '.$delta.' '
                  . 'Mem: '.memory_get_usage(true);

        //
        echo '<pre style="'.$style.'">'.$infoline.'</pre>';

        //
        return $delta;
    }
}