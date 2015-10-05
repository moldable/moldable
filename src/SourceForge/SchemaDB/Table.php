<?php

/*\
 * 
 * 
 * 
 * 
\*/
namespace SourceForge\SchemaDB;

/**
 * static part of sdbClass
 *
 *
 */
class Table 
{
    /**
	 * schemadb mysql constants for rapid fields creation
	 */
    const PRIMARY_KEY	= '<<#primary_key>>';
    const VARCHAR		= '<<{"Type":"varchar(255)"}>>';
    const VARCHAR_80	= '<<{"Type":"varchar(80)"}>>';
    const VARCHAR_255	= '<<{"Type":"varchar(255)"}>>';
    const TEXT			= '<<{"Type":"text"}>>';
    const INT			= '<<{"Type":"int(10)"}>>';
    const INT_10		= '<<{"Type":"int(10)"}>>';
    const INT_14		= '<<{"Type":"int(14)"}>>';
    const FLOAT			= '<<{"Type":"float(14,4)"}>>';
    const FLOAT_14_4	= '<<{"Type":"float(14,4)"}>>';
    const TIME			= '00:00:00';
    const DATE			= '0000-00-00';
    const DATETIME		= '0000-00-00 00:00:00';
   	
    /**
	 * Retrieve table name
	 * 
	 * @return string
	 */
    public static function getTable()
    {        
        ## retrieve value from class setting definition
		if (static::hasClassSetting('table')) {
			return static::getClassSetting('table');
		}
		
		## 
		else if (isset(static::$table)) {
            $name = static::$table;
        } 
		
		##
		elseif (isset(static::$class)) {
            $name = static::$class;
        } 
		
		##
		else {
            $name = get_called_class();
        }

		## get prefix
        $table = static::getDatabase()->getPrefix() . $name;

		## store as setting for future request
		static::setClassSetting('table', $table);
								
        ## return complete table name
        return $table;
    }
    
	/**
	 * Retrieve primary key field name
	 *  
	 * @return boolean
	 */
    public static function getPrimaryKey()
    {
		##
		$setting = 'PrimaryKey';
		
		## retrieve value from class setting definition
		if (static::hasClassSetting($setting)) {
			return static::getClassSetting($setting);
		}
		
		##
		$key = false;
		
		##
		$schema = static::getSchema();

        ##
        foreach ($schema as $field => &$attributes) {

            ##
            if ($attributes['Key'] == 'PRI') {
	
				##
				$key = $field;
                
				##
				break;
            }
        }
			
		## store as setting for future request
		static::setClassSetting($setting, $key);
								
        ## return primary key field name
        return $key;
    }
	
	/**
	 * Retrieve primary key field name
	 *  
	 * @return boolean
	 */
    public static function getMainField()
    {
		##
		$setting = 'MainField';
		
		## retrieve value from class setting definition
		if (static::hasClassSetting($setting)) {
			return static::getClassSetting($setting);
		}
			
		##
		$fields = static::getSchemaFields();
		
		##
		$field = $fields ? $fields[0] : false;
        		
		## store as setting for future request
		static::setClassSetting($setting, $field);
								
        ## return primary key field name
        return $field;
    }
	
	/**
	 * Retrieve primary key field name
	 *  
	 * @return boolean
	 */
    public static function getDefaultFields()
    {
		##
		$setting = 'DefaultFields';
		
		## retrieve value from class setting definition
		if (static::hasClassSetting($setting)) {
			return static::getClassSetting($setting);
		}
		
		##
		$fields = array();
        
		##
		$schema = static::getSchema();
		
		##
		foreach($schema as $field => $attributes) {
			if (isset($attributes['Class'])) {
				$class = $attributes['Class'];
				$fields[$field] = call_user_func($class.'::join', $field);
			} else {
				$fields[] = $field;
			}			
		}
						
		## store as setting for future request
		static::setClassSetting($setting, $fields);
								
        ## return primary key field name
        return $fields;
    }
	
    /**
	 * 
	 * @return type
	 */ 
    public static function updateTable()
    {        
		## if model is not connectect to any db return
		if (!static::getDatabase()) {
			return;			
		}	

		## avoid re-update by check the cache
        if (static::hasClassSetting('update')) {	
			return;
		}

        ## get table name
        $table = static::getTable();

		## 
		$schema = static::getSchema();
						
        ## have a valid schema update db table
        if ($schema) {
            static::getDatabase()->updateTable($table, $schema, false);
        }

        ## cache last update avoid multiple call
        static::setClassSetting('update', time());
    }
	
	/**
	 * Retriece linked database or default
	 * 
	 * @return type
	 */
    public static function getDatabase()
    {		
        ##
        return static::hasClassSetting('database') ? static::getClassSetting('database') : Database::getDefault();
    }

	/**
	 * Link specific database to this table
	 * 
	 * @return type
	 */
    public static function setDatabase($database)
    {        
		##
		static::setClassSetting('database',$database);
    }

    ## usefull mysql func
    public static function now()
    {
        ##
        return date('Y-m-d H:i:s');
    }

    /**
     *
     *
     * @param type $array
     */
    protected static function fetch($sql, $array=false, $value=false, $cast=true)
    {	
		##
		if ($array) {
			$result = static::getDatabase()->getResults($sql);			
		}
		
        ##
        else if (!$value) {
		
			##
			$row = static::getDatabase()->getRow($sql);
			
			##
			return $cast ? static::make($row) : (object) $row; 
        }

        ##
        else {
            $result = static::getDatabase()->getValue($sql);
        }
			
		return $result;
    }
	
	/**
	 * Drop table
	 * 
	 * @param type $confirm
	 * @return type
	 */ 
    public static function drop($confirm=null)
    {
        ##
        if ($confirm !== 'confirm') {
            return;
        }

        ## prepare sql query
        $t = static::getTable();

        ##
        $q = "DROP TABLE IF EXISTS {$t}";

		##
		static::delModelSetting('update');
		
        ## execute query
        static::getDatabase()->query($q);
    }
	
	/**
     * Import records from a source
     *
     * @param type $source
     */
    public static function import($source)
    {
        ## source is array loop throut records
        foreach ($source as $record) {

            ## insert single record
            static::insert($record);
        }
    }
}