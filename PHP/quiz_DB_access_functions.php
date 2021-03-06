<?php
require_once "config.php";

// Gets a quiz with the corresponding quiz_id
// Returns an associative array. I.E. index of "username" --> some string
// Returns null if not found.
function getQuizInfo($link, $quiz_id){
	$sql = "SELECT * FROM quizzes WHERE id = ".$quiz_id;
	$results = mysqli_query($link, $sql);
	if($row = mysqli_fetch_assoc($results)){
		return $row;
	}
	return null;
}

// Gets and returns all questions associated with the given quiz
function getQuizQuestions($link, $quiz_id){
	$sql = "SELECT * FROM questions WHERE quiz_id = ".$quiz_id;
	$results = mysqli_query($link, $sql);
	$total = array();
	while($row = mysqli_fetch_assoc($results)){
		$total[] = $row;
	}
	return $total;
}

// Gets and returns all possible results for a quiz given the quiz id
function getQuizResults($link, $quiz_id){
	$sql = "SELECT * FROM quiz_results WHERE quiz_id = ".$quiz_id;
	$results = mysqli_query($link, $sql);
	$total = array();
	while($row = mysqli_fetch_assoc($results)){
		$total[] = $row;
	}
	return $total;
}

// Gets and return the result from specified quiz with specified traits
function getQuizResult($link, $result_id){
	$sql = "SELECT * FROM quiz_results WHERE id = ".$result_id;
	$results = mysqli_query($link, $sql);
	return mysqli_fetch_assoc($results);
}

// Returns all answers associated with given question
function getQuestionAnswers($link, $question_id){
	$sql = "SELECT * FROM answers WHERE question_id = ".$question_id;
	$results = mysqli_query($link, $sql);
	$total = array();
	while($row = mysqli_fetch_assoc($results)){
		$total[] = $row;
	}
	return $total;
}

// Gets a complete list of all quizzes that the logged in user did not create
// Returns a double array. 1st array is standard numerical index starting at 0.
// second array is an associative array. I.E. index of "username" --> some string
function getOtherQuizzes($link){
	$sql = "SELECT * FROM quizzes WHERE owner_id != ".$_SESSION["id"];
	$results = mysqli_query($link, $sql);
	$total = array();
	while($row = mysqli_fetch_assoc($results)){
		if(empty($row["description"])) $row["description"] = "No Description";
		$total[] = $row;
	}
	return $total;
}

function getSortedOtherQuizzes($link, $order, $phrase){
	$sql = "SELECT * FROM quizzes WHERE NOT owner_id = ".$_SESSION["id"]." AND name LIKE '%".$phrase."%' ORDER BY ".$order;
	$results = mysqli_query($link, $sql);
	$total = array();
	while($row = mysqli_fetch_assoc($results)){
		if(empty($row["description"])) $row["description"] = "No Description";
		$total[] = $row;
	}
	return $total;
}

// Gets a complete list of all quizzes that the logged in user created
// Returns a double array. 1st array is standard numerical index starting at 0.
// second array is an associative array. I.E. index of "username" --> some string
function getMyQuizzes($link){
	$sql = "SELECT * FROM quizzes WHERE owner_id = ".$_SESSION["id"];
	$results = mysqli_query($link, $sql);
	$total = array();
	while($row = mysqli_fetch_assoc($results)){
		$total[] = $row;
	}
	return $total;
}

// Gets a complete list of all quizzes in the DB
// Returns a double array. 1st array is standard numerical index starting at 0.
// second array is an associative array. I.E. index of "username" --> some string
function getAllQuizzes($link){
	$sql = "SELECT * FROM quizzes";
	$results = mysqli_query($link, $sql);
	$total = array();
	while($row = mysqli_fetch_assoc($results)){
		$total[] = $row;
	}
	return $total;
}

function searchRandom($link){
	$sql = "SELECT id FROM quizzes WHERE NOT owner_id =".$_SESSION["id"]." ORDER BY RAND() LIMIT 1";
	$results = mysqli_query($link, $sql);
	return mysqli_fetch_assoc($results)["id"];
}
?>