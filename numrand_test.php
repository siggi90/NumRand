<?

/*
	To initialize NumRand run create_db.sql 
	Then comment out call to get_state in random construct function
	and comment-in save_state and run random constructor
	
	then for normal usage comment-out save_state from random constructor and comment-in get_state.
*/

include '_class.php';
include 'mysql.php';
include 'statement.php';
include 'cylinder.php';
include 'random.php';


$sql = new mysql("numrand");

$statement = statement::init($sql, "numrand", "-1");


$random = new NumRand\random($sql, $statement);

//Run this part after intializing.

/*$result = $random->_random(0, 10, 10);

var_dump($result);*/


?>