## **Notice: No Longer Actively Maintained
Please let me know if you would like to adopt this project as I no longer have time to maintain it.


#### Description
A PDO connection class using the singleton factory design pattern. Inspired by [Jon Raphaelson’s answer](http://stackoverflow.com/questions/130878/global-or-singleton-for-database-connection/219599#219599) to [this stackoverflow question](http://stackoverflow.com/questions/130878/global-or-singleton-for-database-connection).

#### Licence
Released under an MIT licence.


#### Advantages of using this Class

* Security – all connection variables are protected and private methods
* Flexibility – you can supply your own prepared statements
* Simplicity – include podquick.php and config.php in your file and you’re good to go!
* Speed – lightweight class that uses persistent connections to speed up querying
* Ease – helper functions included to avoid needing to write long prepared statements


## Installation

1. Include pdoquick.php in your PHP file
2. Enter your database name and logins into config.php
3. Include pdoquick.php in your PHP file

## Class Methods

### Prepared Statements

Use the pdoQuick::getManager()->generic($sql, $params) method.

	/* generic create your own prepared statement example */
	$table       = 'City';
	$sql         = "UPDATE $table SET District = ? WHERE Population > ? AND CountryCode = ?" ;
	$params       = array ('Hertfordshire', 421010, 'NLD');

	pdoQuick::getManager()->generic($sql, $params);

### Query Methods

	pdoQuick::getManager()->select()
	pdoQuick::getManager()->insert()

	/* select example */
	$table   = 'City';
	$sql     = "SELECT * FROM $table WHERE CountryCode = ? AND District = ? LIMIT 100" ;
	$params  = array('NLD', 'Utrecht');
	$result  = pdoQuick::getManager()->select($sql, $params, true);

	/* insert example */
	$table   = 'City';
	$row    = array (
			// 'ID' => 65456 // optional
		   'Name'         => 'Farringdon',
		   'CountryCode'  => 'GB',
		   'District'     => 'London',
		   'Population'   => 404561
	);
	$inserted_row_id = pdoQuick::getManager()->insert($row, $table);

#### Convenience DB Query Methods

These assume that all comparisons will be = (ie you cannot use these if you need to add > or < comparisons to your queries).

	pdoQuick::getManager()->deleteQuick()
	pdoQuick::getManager()->selectQuick()
	pdoQuick::getManager()->updateQuick()

	/* select quick example */
	  $table   = 'City';
	$where_equals    = array (
		 'CountryCode'  => 'NLD',
		 'District'     => 'Zuid-Holland'
	  );
	$result = pdoQuick::getManager()->selectQuick($where_equals, $table, true);


	/* delete quick example */
	$table   = 'City';
	$where_equals     = array (
	   'CountryCode'  => 'NLD',
	   'District'     => 'Limburg'
	);
	pdoQuick::getManager()->deleteQuick($where_equals, $table, true);

	/* update quick example */
	$table   = 'City';
	$new_values       = array ('Population' => 421018, 'District' => 'Dorset' );
	$where_condition  = array ('CountryCode' => 'NLD', 'Name' => 'Amsterdam');

	$rows_affected = pdoQuick::getManager()->updateQuick($new_values, $where_condition, $table, true);