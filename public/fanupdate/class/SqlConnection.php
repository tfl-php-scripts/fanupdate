<?php
/*****************************************************************************
 * SqlConnection Class for MySQL databases
 *
 * Copyright (c) Jay Pipes http://www.jpipes.com/index.php?/archives/99-MySQL-Connection-Management-in-PHP-How-Not-To-Do-Things.html
 * Copyright (c) Jenny Ferenc <jenny@prism-perfect.net>
 * Copyright (c) 2020 by Ekaterina (contributor) http://scripts.robotess.net
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 ******************************************************************************/

class SqlConnection
{
    private $_host = '';
    private $_user = '';
    private $_pass = '';
    private $_name = '';
    private $_Cnn = false;
    private $_Results = array();
    private $_Res = false;
    private $_NumQueries = 0;

    // for pagination
    public $max_results = 15;
    public $page = 1;
    public $total_results = 0;
    public $cur_row = 0;

    /**
     * @var self
     */
    private static $instance;

    private function __construct()
    {
    }

    /**
     * Get the single instance of SqlConnection object.
     *
     * @param string  (optional) Host name (Server name)
     * @param string  (optional) User Name
     * @param string  (optional) User Password
     * @param string  (optional) Database Name
     * @return  SqlConnection
     *
     */
    public static function instance()
    {
        if (!isset(self::$instance)) {
            $object = __CLASS__;
            self::$instance = new $object;

            if (func_num_args() === 4) {
                $host = func_get_arg(0);
                $user = func_get_arg(1);
                $pass = func_get_arg(2);
                $name = func_get_arg(3);
                self::$instance->SetConnOpt($host, $user, $pass, $name);
            }
        }
        return self::$instance;
    }

    /**
     * Set db connection parameters.
     *
     * @param string  Host name (Server name)
     * @param string  User Name
     * @param string  User Password
     * @param string  Database Name
     * @return  void
     *
     */
    private function SetConnOpt($host, $user, $pass, $name)
    {
        $this->_host = $host;
        $this->_user = $user;
        $this->_pass = $pass;
        $this->_name = $name;
    }

    /**
     * @return  resource
     */
    public function GetLastResult()
    {
        return end($this->_Results);
    }

    /**
     * Get number of active (un-freed) results.
     *
     * @return  int
     */
    public function GetNumResults()
    {
        return count($this->_Results);
    }

    /**
     * Get number queries executed (or attempted) so far.
     *
     * @return  int
     */
    public function GetNumQueries()
    {
        return $this->_NumQueries;
    }

    /**
     * Attempt to connect the resource based on supplied parameters.
     *
     * @param string  (optional) Host name (Server name)
     * @param string  (optional) User Name
     * @param string  (optional) User Password
     * @param string  (optional) Database Name
     * @return  boolean
     * @access  public
     *
     */
    public function Connect()
    {
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
        if (is_resource($this->_Cnn) && func_num_args() != 4) {
            return true;
        }

        if (!$this->_Cnn = @mysql_connect($this->_host, $this->_user, $this->_pass)) {
            trigger_error('Could not connect to database server.', E_USER_ERROR);
            return false;
        }

        if (!@mysql_select_db($this->_name, $this->_Cnn)) {
            trigger_error('Could not connect to specified database on server.', E_USER_ERROR);
            return false;
        }

// enable if your site is UTF-8
        //$this->Execute("SET NAMES utf8");
        return true;
    }

    /**
     * Executes the supplied SQL statement and returns
     * the result of the call.
     *
     * @param string  SQL to execute
     * @param mixed  (optional) Error message (string) to trigger on failure, null to suppress any error
     * @return  bool
     * @access  public
     *
     */
    public function Execute($Sql, $err_msg = 'Could not execute query.')
    {
        /* Auto-connect to database */
        if (!$this->_Cnn) {
            $this->Connect();
        }

        $this->_NumQueries++;

        if (!$this->_Res = mysql_query($Sql, $this->_Cnn)) {
            if ($err_msg !== null) {
                trigger_error($err_msg . ' ' . mysql_error() . ': ' . $Sql, E_USER_WARNING);
            }
            return false;
        }

        if (is_resource($this->_Res)) {
            $this->_Results[] = $this->_Res;
        }
        return true;
    }

    public function ExecutePaginate($Sql, $max_results = 0)
    {

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
        $count_sql = 'SELECT COUNT(*) ' . $sql_from;

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
        $this->Execute($Sql . ' LIMIT ' . (($this->page - 1) * $this->max_results) . ', ' . $this->max_results);
    }

    public function ReadRecordPaginate()
    {
        if ($this->cur_row >= $this->max_results) {
            return false;
        }
        $this->cur_row++;
        return $this->ReadRecord();
    }

    public function PrintPaginate($doNums = true, $nextName = 'Next', $prevName = 'Previous')
    {

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
                $url = preg_replace('/(\?|&amp;)p=[0-9]+/', '$1p=' . $prev, $clean_uri);
                echo '<a href="' . $url . '" class="prev">' . $prevName . '</a> ';
            }

            if ($doNums) {

                $sep_str = '';

                if ($this->page > 1) {
                    echo '&middot; ';
                }

                for ($i = 1; $i <= $total_pages; $i++) {
                    if (($i < 1 + 3) || ($i > ($total_pages - 3)) || ($i < ($this->page + 3) && $i > ($this->page - 3))) {
                        echo $sep_str;
                        $sep_str = '';
                        if ($this->page == $i) {
                            echo '<strong class="here">' . $i . '</strong> ';
                        } else {
                            $url = preg_replace('/(\?|&amp;)p=[0-9]+/', '$1p=' . $i, $clean_uri);
                            echo '<a href="' . $url . '">' . $i . '</a> ';
                        }
                    } else {
                        $sep_str = ' ... ';
                    }
                }

                $url = preg_replace('/(\?|&amp;)p=[0-9]+/', '$1nopage=1', $clean_uri);
                echo '&middot; <a href="' . $url . '">All</a> ';

                if ($this->page < $total_pages) {
                    echo '&middot; ';
                }
            }

            // Build Next Link
            if ($this->page < $total_pages) {
                $next = ($this->page + 1);
                $url = preg_replace('/(\?|&amp;)p=[0-9]+/', '$1p=' . $next, $clean_uri);
                echo '<a href="' . $url . '" class="next">' . $nextName . '</a>';
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
    public function ReadRecord()
    {

        if (!$this->GetLastResult()) {
            return false;
        }
        return mysql_fetch_assoc($this->GetLastResult());
    }

    // IMPORTANT:
    // All other Get*() methods for result sets
    // free the result resorce.

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
     * @param string  (optional) SQL to execute
     * @return  mixed
     * @access  public
     *
     */
    public function GetRecord()
    {

        // Look for a SQL string supplied
        if (func_num_args() == 1) {
            $this->Execute(func_get_arg(0));
        }

        if (!$this->GetLastResult()) {
            return array();
        }

        $return = mysql_fetch_assoc($this->GetLastResult());
        $this->FreeResult();
        return $return;
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
     * @param string  (optional) SQL to execute
     * @return  mixed
     * @access  public
     *
     */
    public function GetFirstCell()
    {
        // Look for a SQL string supplied
        if (func_num_args() == 1) {
            $this->Execute(func_get_arg(0));
        }

        if (!$this->GetLastResult()) {
            return false;
        }

        $row = mysql_fetch_row($this->GetLastResult());
        $this->FreeResult();
        return $row[0];
    }

    /**
     * Returns last inserted auto-id
     *
     * @return  mixed
     * @access  public
     */
    public function GetLastSequence()
    {
        return mysql_insert_id($this->_Cnn);
    }

    /**
     * Returns number of rows in resultset
     *
     * @return  int
     * @access  public
     */
    public function NumRows()
    {
        if (is_resource($this->GetLastResult())) {
            return mysql_num_rows($this->GetLastResult());
        }

        return 0;
    }

    /**
     * Returns number of rows affected by DML statement
     *
     * @return  int
     * @access  public
     */
    public function AffectedRows()
    {
        return mysql_affected_rows($this->_Cnn);
    }

    /**
     * Free result set
     *
     * @return  bool
     * @access  public
     */
    public function FreeResult()
    {
        if (is_resource($this->GetLastResult())) {
            return mysql_free_result(array_pop($this->_Results));
        }

        return false;
    }

    /**
     * Real escape or else adds slashes for insert into DB
     *
     * @param string  String to escape
     * @return  mixed
     * @access  public
     *
     */
    public function Escape($value)
    {
        /* Auto-connect to database */
        if (!$this->_Cnn) {
            $this->Connect();
        }

        if (get_magic_quotes_gpc()) {
            $value = stripslashes($value);
        }

        //check if this function exists
        if (function_exists('mysql_real_escape_string')) {
            $value = mysql_real_escape_string($value, $this->_Cnn);
        } //for PHP version < 4.3.0 use addslashes
        else {
            $value = addslashes($value);
        }

        $value = trim($value);

        return $value;
    }

}
