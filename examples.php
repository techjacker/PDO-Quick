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
 *     @download        git://github.com/techjacker/PDO-Quick.git
 *     @version         0.1
 */
 
 
// Include the connection class
include('pdoquick.php');



   /******************************* Just Use as Connection Class****************************/

	function generic() {

      $table       = 'City';
      $sql         = "UPDATE $table SET District = ? WHERE Population > ? AND CountryCode = ?" ;
      $params       = array ('Hertfordshire', 421010, 'NLD');

		$statement   = pdoQuick::getManager()->generic($sql, $params);
	}




   /******************************* Use the Query Methods ****************************/

	function selectExample() {

	   $table   = 'City';
		$sql     = "SELECT * FROM $table WHERE CountryCode = ? AND District = ? LIMIT 100" ;
		$params  = array('NLD', 'Utrecht');

		$result  = pdoQuick::getManager()->select($sql, $params, true);

		if (isset($result)) {var_dump($result);}
	}


	function updateExample() {

      $table   = 'City';
      $sql     = "UPDATE $table SET District = ? WHERE Population > ? AND CountryCode = ?" ;
      $params  = array ('Devon', 421010, 'NLD');

      pdoQuick::getManager()->generic($sql, $params, true);     
	}


	function deleteExample() {

      $table   = 'City';
      $sql     = "DELETE FROM $table WHERE CountryCode = ? AND District = ?" ;
		$params  = array('NLD', 'Overijssel');

      pdoQuick::getManager()->generic($sql, $params, true);     

	}
	
		
	function insertExample() {

      $table   = 'City';
      $row    = array (
         // 'ID' => 65456 // optional
         'Name'         => 'Farringdon', 
         'CountryCode'  => 'GB',
         'District'     => 'London',
         'Population'   => 404561
      );
      
      $inserted_row_id = pdoQuick::getManager()->insert($row, $table);
      print "\nrow successfully inserted with id $inserted_row_id\n";
	}


	

   /******************************* Quick Query Methods Examples ****************************/
   // selectQuick(), updateQuick(), deleteQuick()
   // they always use the = operator for the where condition
   // pass table name and array of key values in format array = (<column_name> => <column_value>)
	
	function selectExampleQuick() {

	   $table   = 'City';
      $where_equals    = array (
         'CountryCode'  => 'NLD',
         'District'     => 'Zuid-Holland'
      );

      $result = pdoQuick::getManager()->selectQuick($where_equals, $table, true);  
		if (isset($result)) {var_dump($result);}
	}

	function updateExampleQuick() {

	   $table   = 'City';     
      $new_values       = array ('Population' => 421018, 'District' => 'Dorset' );
      $where_condition  = array ('CountryCode' => 'NLD', 'Name' => 'Amsterdam');

      $result = pdoQuick::getManager()->updateQuick($new_values, $where_condition, $table, true);
		if (isset($result)) {return($result);}

	}

	function deleteExampleQuick() {

      $table   = 'City';
      $where_equals    = array (
         'CountryCode'  => 'NLD',
         'District'     => 'Limburg'
      );

      pdoQuick::getManager()->deleteQuick($where_equals, $table, true);     
	}

   /******************************* // END Quick Methods ****************************/






   /******************************* Call Functions ***************************
   
   // Just Use as a Connection Class & Prepare Your Own Statements
   generic();

   // query methods examples
   insertExample();
   deleteExample();
   selectExample();
   updateExample();

	// quick query methods functions (where conditions must use the = operator ie cannot do < or > comparisons)
   deleteExampleQuick();
   selectExampleQuick();   
   updateExampleQuick();   

   */

