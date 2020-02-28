<?php

/*
 * File: SqlConnection.php
 * Author: Jay Pipes
 * Updated: 2008-01-22 by Jenny Ferenc
 * Link: http://www.jpipes.com/index.php?/archives/99-MySQL-Connection-Management-in-PHP-How-Not-To-Do-Things.html
 *
 * SqlConnection Class for MySQL databases
 * 
 * Encapsulates a simple API for database related activities.
 * 
 * @access      public
 */
class SqlConnection {

    /**
     * @access private
     */
    var $_host = '';
    var $_user = '';
    var $_pass = '';
    var $_name = '';
    var $_Cnn = false;
    var $_Results = array();
    var $_Res = false;
    var $_NumQueries = 0;

    // for pagination
    var $max_results = 15;
    var $page = 1;
    var $total_results = 0;
    var $cur_row = 0;
    
    /**
     * Constructor.
     * 
     * @return  void    
     * @access	private
     */   
    function SqlConnection() {}

    /**
     * Get the single instance of SqlConnection object.
     * 
     * @return  SqlConnection
     *  
     * @param   string  (optional) Host name (Server name)
     * @param   string  (optional) User Name
     * @param   string  (optional) User Password
     * @param   string  (optional) Database Name
     */   
    function &instance() {
        static $instance;
        if (!isset($instance)) {
            $object = __CLASS__;
            $instance = new $object;

            if (func_num_args() == 4) {
                $host = func_get_arg(0);
                $user = func_get_arg(1);
                $pass = func_get_arg(2);
                $name = func_get_arg(3);
                $instance->SetConnOpt($host, $user, $pass, $name);
            }
        }
        return $instance;
    }

    /**
     * Set db connection parameters.
     * 
     * @return  void
     * 
     * @param   string  Host name (Server name)
     * @param   string  User Name
     * @param   string  User Password
     * @param   string  Database Name
     */ 
    function SetConnOpt($host, $user, $pass, $name) {
        $this->_host = $host;
        $this->_user = $user;
        $this->_pass = $pass;
        $this->_name = $name;
    }

    /**
     * @return  resource
     */
    function GetLastResult() {
        return end($this->_Results);
    }

    /**
     * Get number of active (un-freed) results.
     * 
     * @return  int
     */
    function GetNumResults() {
        return count($this->_Results);
    }

    /**
     * Get number queries executed (or attempted) so far.
     * 
     * @return  int
     */
    function GetNumQueries() {
        return $this->_NumQueries;
    }
    
    /**
     * Attempt to connect the resource based on supplied parameters. 
     * 
     * @return  boolean 
     * @access  public
     *  
     * @param   string  (optional) Host name (Server name)
     * @param   string  (optional) User Name
     * @param   string  (optional) User Password
     * @param   string  (optional) Database Name
     */  
    function Connect() {
        if (func_num_args() == 4) {
            
            // A different database has been requested other than the 
            // standard global config settings

            $this->SetConnOpt(func_get_arg(1), func_get_arg(2), func_get_arg(3), func_get_arg(4));
        }

        /**
         * Short circuit out when already
         * connected.  To reconnect, pass
         * args again
         */
        if (is_resource($this->_Cnn) && func_num_args() != 4) {return true;}
            
        if (! $this->_Cnn = @mysql_connect($this->_host, $this->_user, $this->_pass)) {
            trigger_error('Could not connect to database server.', E_USER_ERROR);
            return false;
        } else {
            if (! @mysql_select_db($this->_name, $this->_Cnn)) {
                trigger_error('Could not connect to specified database on server.', E_USER_ERROR);
                return false;
            } else {
                // enable if your site is UTF-8
                //$this->Execute("SET NAMES utf8");
                return true;
            }            
        }
    }
    
    /**
     * Executes the supplied SQL statement and returns
     * the result of the call.
     * 
     * @return  bool   
     * @access  public
     *  
     * @param   string  SQL to execute
     * @param   mixed  (optional) Error message (string) to trigger on failure, null to suppress any error
     */  
    function Execute( $Sql, $err_msg = 'Could not execute query.' ) {
        
        /* Auto-connect to database */
        if (! $this->_Cnn) {
            $this->Connect();
        }

        $this->_NumQueries++;
        
        if (!$this->_Res = mysql_query($Sql, $this->_Cnn)) {
            if (!is_null($err_msg)) {
                trigger_error($err_msg.' '.mysql_error().': '.$Sql, E_USER_WARNING);
            }
            return false;
        } else {
            if (is_resource($this->_Res)) { array_push($this->_Results, $this->_Res); }
            return true;
        }
    }

    function ExecutePaginate($Sql, $max_results = 0) {

        if ($max_results > 0) {
            $this->max_results = $max_results;
        }

        if (!isset($_GET['p'])) {
            $this->page = 1;
        } else {
            $this->page = (int)$_GET['p'];
        }
        
        $sql_from = strstr($Sql, 'FROM ');
        list($sql_from) = explode('ORDER BY ', $sql_from);
        $count_sql = 'SELECT COUNT(*) '.$sql_from;

        // Figure out the total number of results in DB:
        $this->Execute($count_sql);
        $numrows = $this->NumRows();
        $this->total_results = $this->GetFirstCell();
        
        if ($numrows > 1) {
            $this->total_results = $numrows;
        }

        if (isset($_GET['nopage'])) {
            $this->max_results = $this->total_results;
        }

        $this->cur_row = 0;
        $this->Execute($Sql.' LIMIT '.(($this->page - 1) * $this->max_results).', '.$this->max_results);
    }

    function ReadRecordPaginate() {
        if ($this->cur_row >= $this->max_results) return false;
        $this->cur_row++;
        return $this->ReadRecord();
    }

    function PrintPaginate($doNums = true, $nextName = 'Next', $prevName = 'Previous') {

        if ($this->total_results > $this->max_results) {

            // Figure out the total number of pages. Always round up using ceil()
            $total_pages = ceil($this->total_results / $this->max_results);

            $clean_uri = htmlspecialchars(clean_input($_SERVER['REQUEST_URI']));

            if ($this->page == 1 && empty($_GET['p'])) {
                if (!empty($_GET)) {
                    $clean_uri .= '&amp;p=1';
                } else {
                    $clean_uri .= '?p=1';
                }
            }

            $clean_uri = str_replace('&amp;nopage=1', '', $clean_uri);

            // Build Previous Link
            if ($this->page > 1) {
                $prev = ($this->page - 1);
                $url = preg_replace('/(\?|&amp;)p=[0-9]+/', '$1p='.$prev, $clean_uri);
                echo '<a href="'.$url.'" class="prev">'.$prevName.'</a> ';
            }

            if ($doNums) {

                $sep_str = '';
                
                if ($this->page > 1) { echo '&middot; '; }

                for ($i=1; $i<=$total_pages; $i++) {
                    if (($i < 1 + 3) || ($i > ($total_pages - 3)) || ($i < ($this->page + 3) && $i > ($this->page - 3))) {
                        echo $sep_str;
                        $sep_str = '';
                        if ($this->page == $i) {
                            echo '<strong class="here">'.$i.'</strong> ';
                        } else {
                            $url = preg_replace('/(\?|&amp;)p=[0-9]+/', '$1p='.$i, $clean_uri);
                            echo '<a href="'.$url.'">' . $i . '</a> ';
                        }
                    } else {
                        $sep_str = ' ... ';
                    }
                }

                $url = preg_replace('/(\?|&amp;)p=[0-9]+/', '$1nopage=1', $clean_uri);
                echo '&middot; <a href="'.$url.'">All</a> ';

                if ($this->page < $total_pages) { echo '&middot; '; }
            }

            // Build Next Link
            if ($this->page < $total_pages) {
                $next = ($this->page + 1);
                $url = preg_replace('/(\?|&amp;)p=[0-9]+/', '$1p='.$next, $clean_uri);
                echo '<a href="'.$url.'" class="next">'.$nextName.'</a>';
            }
        }
    }
    
    /**
     * Reads into an array the current
     * record in the result.
     * 
     * @return  mixed   
     * @access  public
     */  
    function ReadRecord() {

        if (! $this->GetLastResult()) {return false;}
        return mysql_fetch_assoc($this->GetLastResult());
    }

    // IMPORTANT:
    // All other Get*() methods for result sets
    // free the result resorce.
    
    /**
     * Returns an array of records from the 
     * current result resource.
     * Returns empty array if no retrieval
     * 
     * Frees result resource.
     *
     * This method consumes more memory resources
     * than ReadRecord() but is useful to 
     * get quick record sets for processing
     *
     * Optionally, you can supply a SQL
     * string to short-cut a call to
     * SqlConnection::Execute
     * 
     * @return  mixed   
     * @access  public
     * 
     * @param   string  (optional) SQL to execute
     */  
    function &GetRecords() {

        // Look for a SQL string supplied
        if (func_num_args() == 1) {
            $this->Execute(func_get_arg(0));
        }

        $return = array();
        if (! is_resource($this->GetLastResult())) {
            trigger_error(get_class($this) . 
                                "::GetRecords() : " . 
                                mysql_error(), E_USER_ERROR);
            return $return;
        } else {
            while ($row = mysql_fetch_assoc($this->GetLastResult())) {
                $return[] = $row;
            }
            $this->FreeResult();
            return $return;
        }
    }
    
    /**
     * Returns an single record array from the
     * current result resource.
     * Returns empty array if no retrieval
     * 
     * Frees result resource.
     *
     * Optionally, you can supply a SQL
     * string to short-cut a call to
     * SqlConnection::Execute
     * 
     * @return  mixed   
     * @access  public
     *  
     * @param   string  (optional) SQL to execute
    */  
    function &GetRecord() {
        
        // Look for a SQL string supplied
        if (func_num_args() == 1) {
            $this->Execute(func_get_arg(0));
        }
        
        if (! $this->GetLastResult()) {
            $return = array();
            return $return;
        } else {
            $return = mysql_fetch_assoc($this->GetLastResult());
            $this->FreeResult();
            return $return;
        }
    }
    
    /**
     * Returns first data point from 
     * current result resource
     * or false if no retrieval
     * 
     * Frees result resource.
     *
     * Optionally, you can supply a SQL
     * string to short-cut a call to
     * SqlConnection::Execute
     * 
     * @return  mixed   
     * @access  public
     *  
     * @param   string  (optional) SQL to execute
    */  
    function GetFirstCell() {
        
        // Look for a SQL string supplied
        if (func_num_args() == 1) {
            $this->Execute(func_get_arg(0));
        }
        
        if (! $this->GetLastResult()) {
            return false;
        } else {
            $row = mysql_fetch_row($this->GetLastResult());
            $this->FreeResult();
            return $row[0];
        }
    }
    
    /**
     * Returns last inserted auto-id
     * 
     * @return  mixed   
     * @access  public
    */  
    function GetLastSequence() {
        return mysql_insert_id($this->_Cnn);
    }
    
    /**
     * Returns number of rows in resultset
     * 
     * @return  int   
     * @access  public
    */  
    function NumRows() {
        if (is_resource($this->GetLastResult())) {
            return mysql_num_rows($this->GetLastResult());
        } else {
            return 0;
        }
    }

    /**
     * Returns number of rows affected by DML statement
     * 
     * @return  int   
     * @access  public
    */  
    function AffectedRows() {
        return mysql_affected_rows($this->_Cnn);
    }

    /**
     * Free result set
     * 
     * @return  bool  
     * @access  public
    */  
    function FreeResult() {
        if (is_resource($this->GetLastResult())) {
            return mysql_free_result(array_pop($this->_Results));
        } else {
            return false;
        }
    }

    /**
     * Real escape or else adds slashes for insert into DB
     * 
     * @return  mixed   
     * @access  public
     * 
     * @param   string  String to escape
    */  
    function Escape($value) {

        /* Auto-connect to database */
        if (! $this->_Cnn) {
            $this->Connect();
        }

        if (get_magic_quotes_gpc()) {
            $value = stripslashes($value);
        }

        //check if this function exists
        if (function_exists("mysql_real_escape_string")) {
            $value = mysql_real_escape_string($value, $this->_Cnn);
        }
        //for PHP version < 4.3.0 use addslashes
        else {
            $value = addslashes($value);
        }

        $value = trim($value);

        return $value;
    }

} // end class SqlConnection

?>