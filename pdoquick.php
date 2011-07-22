<?php
/*
 *     
 *     PDO-Quick, a lightweight PHP class that securely handles PDO connections and includes simple querying methods.
 *
 *
 *     Copyright (c) 2011, Andrew Griffiths, http://andrewgriffithsonline.com
 * 
 *     Permission is hereby granted, free of charge, to any person obtaining a copy
 *     of this software and associated documentation files (the "Software"), to deal
 *     in the Software without restriction, including without limitation the rights
 *     to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *     copies of the Software, and to permit persons to whom the Software is
 *     furnished to do so, subject to the following conditions:
 *
 *     The above copyright notice and this permission notice shall be included in
 *     all copies or substantial portions of the Software.
 *
 *     THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *     IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *     FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *     AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *     LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *     OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *     THE SOFTWARE.
 *     
 *
 *     @copyright       Copyright (C) 2011 Andrew Griffiths
 *     @author          Andrew Griffiths <hello@andrewgriffithsonline.com>
 *     @license         http://www.opensource.org/licenses/MIT
 *     @package         PDO-Quick
 *     @tutorial        http://andrewgriffithsonline.com/software/pdo-quick/
 *     @download        git@github.com:techjacker/PDO-Quick.git
 *     @version         0.1
 */

// include mysqlBase class
include ("config.php"); // set username and password and db_name in here (and place outside of web root)

class pdoQuick extends mysqlBase { 

   private static $_dbManagerInstance;
   private        $_dbConnectionInstance;
   public         $_func                = "nameParams";
   private        $_statement_number    = 0;


   /************* edit to suit your needs  *********************/

   protected      $_production_mode     = false;
   private        $_error_message       = "We are currently experiencing technical difficulties. We have a bunch of monkeys working really hard to fix the problem.";
   private        $_errors_email        = 'poor@sod.com';

   private        $_dbtype              = "mysql";
   protected      $_host                = 'localhost';
   protected      $_port                = 3306; // default port for MySQL

   /***** username and password and db_name should be set in mysqlBase class in the pdo_config.php file ********/


   /* PDO constants options: http://php.net/manual/en/pdo.constants.php */
   protected      $_db_params           = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", PDO::ATTR_PERSISTENT => true);             // increase performance by creating a persistent connection

   /************* END edit to suit your needs  *********************/




   /*********************************** Connection Methods ***********************************/

	public static function getManager(){
		if (null === self::$_dbManagerInstance) {
			self::$_dbManagerInstance = new pdoQuick();
		}
		return self::$_dbManagerInstance;
	}


	private function __construct() {

		if(!$this->_dbConnectionInstance) {

			try	{
			
				$this->_dbConnectionInstance =  new PDO($this->_dbtype.':host='.$this->_host.';port='.$this->_port.';dbname='.$this->_pdo_db_name, $this->_pdo_db_user, $this->_pdo_db_password, $this->_db_params);
				
				if ($this->_production_mode === true) {
      			$this->_dbConnectionInstance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
				} else {
				   $this->_dbConnectionInstance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			   }
			
				$this->_dbConnectionInstance->beginTransaction();


			 } catch (PDOException $e) {
			
				$this->_dbConnectionInstance->rollback();
				$this->_dbConnectionInstance = null;
            
            
            /* error message output */           
				if ($this->_production_mode === true) {
               file_put_contents('pdo_errors.log', $e->getMessage(), FILE_APPEND); // log the errors     
				   //$this->email_errors($e->getMessage()); // email errors to me
				   die($this->_error_message);
				} else {			
				   die($e->getMessage());
               //return $e->getMessage();
            }
            
			} // end try
			
		} // end if(!$this->_dbConnectionInstance)
		
		return $this->_dbConnectionInstance;
		
	}


	public function __destruct(){
	   $this->_dbConnectionInstance = null;
	}
   /*********************************** // END Connection Methods ***********************************/




   /*********************************** Query Methods ***********************************/


   // use for delete and update statements
	public function generic($sql, $values=null, $rows_affected=false) {
    
		$statement = $this->resetCursor($sql, $values);
		if ($rows_affected) { print "\nrows affected = ".$statement->rowCount()."\n\n"; }
	}	


	public function select($sql, $values=null, $rows_affected=false) {

		$statement = $this->resetCursor($sql, $values);

		while ( $row = $statement->fetch(PDO::FETCH_ASSOC) ) {
			$result[] = $row; // print $row;
		}
		
		if ($rows_affected) { print "\nrows affected = ".$statement->rowCount()."\n\n"; }
		if (isset($result)) {return $result;}
	}


	public function insert($data_array_unchecked, $table) {

		$data_array = array();
		foreach ($data_array_unchecked as $k => $v) {
			if ($v != null) { $data_array[$k] = $v; }
		}

		$cols = $this->namedColumns($data_array);
		$vals = $this->namedValues($data_array);
		
		$sql = " INSERT INTO $table ( $cols ) values ( $vals ) ";

		$this->resetCursor($sql, $data_array);
		return $this->_dbConnectionInstance->lastInsertId();
	}
	
   /******************************* Quick Methods ****************************/
   // deleteQuick(), selectQuick(), updateQuick
   // they always use the = operator for the where condition
   // pass table name and array of key values in format array = (<column_name> => <column_value>)

	public function selectQuick($where_equals, $table, $rows_affected=false, $limit=1000) {

      $sql_where = $this->whereEquals("SELECT * FROM $table", $where_equals);

		$statement = $this->resetCursor($sql_where[0]." LIMIT $limit", $sql_where[1]);
		
		while ( $row = $statement->fetch(PDO::FETCH_ASSOC) ) {
			$result[] = $row; // print $row;
		}
		
		if ($rows_affected) { print "\nrows affected = ".$statement->rowCount()."\n\n"; }
		if (isset($result)) {return $result;}
	}

	public function deleteQuick($where_equals, $table, $rows_affected=false) {

      $sql_where = $this->whereEquals("DELETE FROM $table", $where_equals);
		$statement = $this->resetCursor($sql_where[0], $sql_where[1]);
		if ($rows_affected) { print "\nrows affected = ".$statement->rowCount()."\n\n"; }

	}

	public function updateQuick($new_values, $where_equals, $table, $rows_affected=false) {

		$sql = "UPDATE $table SET";
		$where = array();
		foreach ($new_values as $f => $v) {
			$sql .= " $f=?,";
			$where[] = $v;
		}
		$sql = rtrim($sql, ",");
		
      $sql_where = $this->whereEquals($sql, $where_equals);
      foreach ($sql_where[1] as $value) {
         $where[] = $value;
      }
      
		$statement = $this->resetCursor($sql_where[0], $where);
      
      if (isset($statement)) {
		   if ($rows_affected) { print "\nrows affected = ".$statement->rowCount()."\n\n"; }
      }
	}


   /******************************* // END Quick Methods ****************************/
	
	


   /************* helper functions used in query methods ****************/

   public function whereEquals($sql, $where_condition) {

		$p = 0;
		$count = count($where_condition);
		$values = array();
		
		foreach ($where_condition as $k => $v) {
			$p++;
			$sql .= ($p == 1) ? " WHERE " : "";
			$sql .= "$k = ?";
			$sql .= ($p >= 1) && ($p < $count) ? " AND " : "";
			$values[] = $v; // append where id value to values array
		}
		
		$sql_where = array($sql, $values);
		return $sql_where;
   
   }

   public function resetCursor ($prepare, $execute) {
   
		   $statement_name = 'statement'.$this->_statement_number;
         $this->_statement_number++;
		   
		   ${$statement_name} = $this->_dbConnectionInstance->prepare($prepare);
         if ($this->_statement_number != 1) { ${$statement_name}->closeCursor(); }
		   
		   ${$statement_name}->execute($execute);
		   return ${$statement_name};
		   ${$statement_name} = null;
   }

	public function namedColumns($data_array) {
		return $insert_columns = implode(", ", array_keys($data_array));
	}

	function nameParams($n)	{ return ":".$n; }

	public function namedValues($data_array) {
		return $insert_values = implode( ", ", array_map( array($this, $this->_func), array_keys($data_array)) );
	}

	public function email_errors($errors) {
		$to            = $this->_errors_email;
		$subject       = "You screwed up again!";
		$message_body  = $errors;
		$headers       = "From: failwhale@crapcoding.com";
		//"CC: somebodyelse@example.com";

		mail($to,$subject,$message_body,$headers);
	}
   /************* // END helper functions used in query methods ****************/

   /*********************************** // END Query Methods ***********************************/



} // END class pdoQuick
?>
