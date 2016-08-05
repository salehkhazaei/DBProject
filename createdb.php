<?php
    require_once('config.php');
    
	global $db_username, $db_password, $db_database, $db_host, $db_version;
	$db_database = "healthcare2";
	$db_host = "localhost";
	$db_username = "system";
	$db_password = '';
	$db_version = '1.0';    
	
    global $patient,$doctor_pharmacy,$doctor,$researcher,$background;
	global $patient_doctor_trust;
	global $DBH;

	$patient = "patient" ;
	$background = "background" ;
	$doctor = "doctor" ;
	$researcher = "researcher" ;
	$doctor_pharmacy = "doctor_pharmacy" ;
	$patient_doctor_trust = "patient_doctor_trust" ;
    
	OpenDatabase ( $db_host, $db_database, $db_username, $db_password );

    CreateBackgroundTable ();
    CreateDoctorTable ();
    CreatePatientTable ();
    CreateDoctorPharmacyTable ();
    CreateResearcherTable ();
    CreatePatientDoctorTrustTable ();
    redirect('index.php');
    CloseDatabase();
    
	function CreatePatientTable() {
		try {
			$GLOBALS ['DBH']->query ( 
					"CREATE TABLE IF NOT EXISTS " . $GLOBALS ['patient'] . " ( id char(255),
																				name CHAR(255),
																				family CHAR(255),
																				sex CHAR(1),
																				research_access int default 0,
                                                                                date_of_birth DATE,
                                                                                education_level CHAR(20),
                                                                                simple_insurance_mode CHAR(50),
                                                                                complete_insurace_mode CHAR(50),
                                                                                job CHAR(100),
                                                                                location CHAR(200),
                                                                                geographical_position_x double,
                                                                                geographical_position_y double, 
																				PRIMARY KEY(id))
																				CHARSET=utf8 DEFAULT COLLATE utf8_persian_ci;" );
		}
		catch ( PDOException $e ) {
			echo $e->getMessage ();
			file_put_contents ( 'PDOErrors.txt', $e->getMessage (), FILE_APPEND );
		}
	}

	function CreateBackgroundTable() {
		try {
			$GLOBALS ['DBH']->query ( 
					"CREATE TABLE IF NOT EXISTS " . $GLOBALS ['background'] . "(patient_id char(255),
																				doctor_id char(255),
                                                                                disease_type char(100),
                                                                                details text,
                                                                                description text,
                                                                                doctor_diagnosis text,
                                                                                security_level int,
                                                                                prescription text,
                                                                                pdate DATE,
																				PRIMARY KEY(patient_id,doctor_id,pdate))
																				CHARSET=utf8 DEFAULT COLLATE utf8_persian_ci;" );
		}
		catch ( PDOException $e ) {
			echo $e->getMessage ();
			file_put_contents ( 'PDOErrors.txt', $e->getMessage (), FILE_APPEND );
		}
	}

	function CreateDoctorTable() {
		try {
			$GLOBALS ['DBH']->query ( 
					"CREATE TABLE IF NOT EXISTS " . $GLOBALS ['doctor'] . " ( id char(255),
                                                                            name CHAR(50),
                                                                            specialization CHAR(50),
                                                                            PRIMARY KEY(id))
                                                                            CHARSET=utf8 DEFAULT COLLATE utf8_persian_ci;" );
		}
		catch ( PDOException $e ) {
			echo $e->getMessage ();
			file_put_contents ( 'PDOErrors.txt', $e->getMessage (), FILE_APPEND );
		}
	}

	function CreateDoctorPharmacyTable() {
		try {
			$GLOBALS ['DBH']->query ( 
					"CREATE TABLE IF NOT EXISTS " . $GLOBALS ['doctor_pharmacy'] . " ( id char(255),
                                                                            PRIMARY KEY(id))
                                                                            CHARSET=utf8 DEFAULT COLLATE utf8_persian_ci;" );
		}
		catch ( PDOException $e ) {
			echo $e->getMessage ();
			file_put_contents ( 'PDOErrors.txt', $e->getMessage (), FILE_APPEND );
		}
	}

	function CreateResearcherTable() {
		try {
			$GLOBALS ['DBH']->query ( 
					"CREATE TABLE IF NOT EXISTS " . $GLOBALS ['researcher'] . " ( id char(255),
                                                                            PRIMARY KEY(id))
                                                                            CHARSET=utf8 DEFAULT COLLATE utf8_persian_ci;" );
		}
		catch ( PDOException $e ) {
			echo $e->getMessage ();
			file_put_contents ( 'PDOErrors.txt', $e->getMessage (), FILE_APPEND );
		}
	}
	function CreatePatientDoctorTrustTable() {
		try {
			$GLOBALS ['DBH']->query ( 
					"CREATE TABLE IF NOT EXISTS " . $GLOBALS ['patient_doctor_trust'] . " ( patient_id char(255),
																				doctor_id char(255),
																				access_type int,
																				PRIMARY KEY(patient_id,doctor_id),
                                                                                FOREIGN KEY(patient_id) REFERENCES patient(id),
                                                                                FOREIGN KEY(doctor_id) REFERENCES doctor(id)
                                                                                )
																				CHARSET=utf8 DEFAULT COLLATE utf8_persian_ci;" );
		}
		catch ( PDOException $e ) {
			echo $e->getMessage ();
			file_put_contents ( 'PDOErrors.txt', $e->getMessage (), FILE_APPEND );
		}
	}
?>