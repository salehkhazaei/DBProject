<?php
    session_start();
    
	function redirect($link) {
		echo "<script>window.location='$link';</script>";
	}
	function OpenDatabase($host, $database, $username, $password) {
		try {
			$GLOBALS ['DBH'] = new PDO ( "mysql:host=$host;dbname=$database;charset=utf8", $username, $password );
			$GLOBALS ['DBH']->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			
			return true;
		}
		catch ( PDOException $e ) {
			return false;
		}
		return false;

	}
	function CloseDatabase() {
		$GLOBALS ['DBH'] = null;
	}

	function Create($table, $headers, $values, $data) {
		try {
			$STH = $GLOBALS ['DBH']->prepare ( "INSERT INTO " . $table . " " . $headers . " values " . $values );
			$STH->execute ( $data );
		}
		catch ( PDOException $e ) {
			echo $e->getMessage ();
			file_put_contents ( 'PDOErrors.txt', $e->getMessage (), FILE_APPEND );
			return;
		}
		return true;
	}

	function Update($table, $set, $where, $data) {
		try {
			$STH = $GLOBALS ['DBH']->prepare ( "UPDATE " . $table . " SET " . $set . " WHERE " . $where );
			$STH->execute ( $data );
		}
		catch ( PDOException $e ) {
			echo $e->getMessage ();
			file_put_contents ( 'PDOErrors.txt', $e->getMessage (), FILE_APPEND );
		}

	}

	function Delete($table, $where, $data) {
		try {
			$STH = $GLOBALS ['DBH']->prepare ( "DELETE FROM " . $table . " WHERE " . $where );
			$STH->execute ( $data );
		}
		catch ( PDOException $e ) {
			echo $e->getMessage ();
			file_put_contents ( 'PDOErrors.txt', $e->getMessage (), FILE_APPEND );
		}

	}

	function Select($what, $table, $where, $data, $dump=false) {
		if ($where == null) {
			return $GLOBALS ['DBH']->query ( "select " . $what . " from " . $table );
		}
		else {
			$statement = $GLOBALS ['DBH']->prepare ( "select " . $what . " from " . $table . " where " . $where ); // name = :name");
			if ( $dump == true )
			{
				var_dump ( $statement);
				var_dump ( $data);
			}
			$statement->execute ( $data );
			return $statement;
		}
	}
?>