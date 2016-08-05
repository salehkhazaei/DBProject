<?php 
	require_once ('config.php');
	require('header.php');
    
    if ( isset ( $_POST['type'] ) )
    {
        echo "hello";
        global $db_username, $db_password, $db_database, $db_host, $db_version;
        $db_database = "healthcare2";
        $db_host = "localhost";
        $db_username = "root";
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
        
        $do_create = false ;
        do {
        try {
            if ( $do_create == true )
            {
                $GLOBALS ['DBH']->query ( "drop user '".$_POST['username']."'@'localhost'" );
                $GLOBALS ['DBH']->query ( "FLUSH PRIVILEGES" );
                $do_create = false;
            }
            $GLOBALS ['DBH']->query ( "create user '".$_POST['username']."'@'localhost' identified by '".$_POST['pass']."'" );
            $GLOBALS ['DBH']->query ( "grant select ON healthcare2.doctor TO '".$_POST['username']."'@'localhost' identified by '".$_POST['pass']."'" );
            switch($_POST['type'])
            {
                case 0: // patient
                    Create ($GLOBALS['patient'], "(id)", "(?)", array($_POST['username']));
                    try {
                        // patient table
                        $res = $GLOBALS ['DBH']->query ( "CREATE OR REPLACE VIEW healthcare2.myprofile_".$_POST['username']." AS SELECT * FROM patient WHERE id='".$_POST['username']."' WITH CASCADED CHECK OPTION" );
                        $res = $GLOBALS ['DBH']->query ( "GRANT SELECT,UPDATE(name,family,sex,date_of_birth,education_level,simple_insurance_mode,complete_insurace_mode,job,location,geographical_position_x,geographical_position_y)
                        ON healthcare2.myprofile_".$_POST['username']." TO '".$_POST['username']."'@'localhost' identified by '".$_POST['pass']."'" );

                        // background table
                        $res = $GLOBALS ['DBH']->query ( "CREATE OR REPLACE VIEW healthcare2.background_".$_POST['username']." AS SELECT * FROM background WHERE patient_id='".$_POST['username']."'" );
                        $res = $GLOBALS ['DBH']->query ( "GRANT SELECT ON healthcare2.background_".$_POST['username']." TO '".$_POST['username']."'@'localhost' identified by '".$_POST['pass']."'" );

                        // doctor table
                        $res = $GLOBALS ['DBH']->query ( "GRANT SELECT ON healthcare2.doctor TO '".$_POST['username']."'@'localhost' identified by '".$_POST['pass']."'" );

                        // patient_doctor_trust table
                        $res = $GLOBALS ['DBH']->query ( "CREATE OR REPLACE VIEW healthcare2.trusteddoc_".$_POST['username']." AS SELECT patient_id,doctor_id,access_type FROM patient_doctor_trust WHERE patient_id='".$_POST['username']."' WITH CASCADED CHECK OPTION" );
                        $res = $GLOBALS ['DBH']->query ( "GRANT SELECT,INSERT,DELETE ON healthcare2.trusteddoc_".$_POST['username']." TO '".$_POST['username']."'@'localhost' identified by '".$_POST['pass']."'" );
                        // doctor pharmacy
                        // researcher 
                    }
                    catch ( Exception $e ) {
                        echo $e->getMessage ();
                    }
                    break;
                case 1: // doctor
                    Create ($GLOBALS['doctor'], "(id)", "(?)", array($_POST['username']));
                    try {
                        // patient table
                        $res = $GLOBALS ['DBH']->query ( "CREATE OR REPLACE VIEW healthcare2.patient_".$_POST['username']." 
                        AS Select * from patient where id in (SELECT p.id FROM `patient` p inner join `patient_doctor_trust` pd on p.id=pd.patient_id where pd.doctor_id='".$_POST['username']."')" );
                        $res = $GLOBALS ['DBH']->query ( "GRANT SELECT ON healthcare2.patient_".$_POST['username']." TO '".$_POST['username']."'@'localhost' identified by '".$_POST['pass']."'" );

                        // background table
                        $res = $GLOBALS ['DBH']->query ( "CREATE OR REPLACE VIEW healthcare2.patient_background_".$_POST['username']." 
                        AS Select * from background b 
                        where exists (select null from patient_doctor_trust pd where pd.patient_id=b.patient_id and pd.doctor_id=b.doctor_id and access_type=0)
                        OR exists (select null from patient_doctor_trust pd where pd.patient_id=b.patient_id and pd.doctor_id=b.doctor_id and access_type=1 and b.disease_type=(Select disease_type from background order by pdate DESC limit 1))
                        " );
                        $res = $GLOBALS ['DBH']->query ( "GRANT SELECT,UPDATE ON healthcare2.patient_background_".$_POST['username']." TO '".$_POST['username']."'@'localhost' identified by '".$_POST['pass']."'" );
                        $res = $GLOBALS ['DBH']->query ( "GRANT INSERT ON healthcare2.background TO '".$_POST['username']."'@'localhost' identified by '".$_POST['pass']."'" );

                        // doctor table
                        $res = $GLOBALS ['DBH']->query ( "GRANT SELECT ON healthcare2.doctor TO '".$_POST['username']."'@'localhost' identified by '".$_POST['pass']."'" );

                        $res = $GLOBALS ['DBH']->query ( "DROP TRIGGER IF EXISTS `healthcare2`.`add_trusted_doc`" );
                        $res = $GLOBALS ['DBH']->query ( "CREATE TRIGGER `healthcare2`.`add_trusted_doc` AFTER INSERT ON healthcare2.background
  FOR EACH ROW BEGIN
    INSERT INTO patient_doctor_trust (patient_id,doctor_id,access_type) values (NEW.patient_id,NEW.doctor_id,0);
  END;" );
                        // patient_doctor_trust table
                        // doctor pharmacy
                        // researcher 
                    }
                    catch ( Exception $e ) {
                        var_dump($e);
                    }
                    break;
                case 2: // pharmacy doctor
                    Create ($GLOBALS['doctor_pharmacy'], "(id)", "(?)", array($_POST['username']));
                    try {
                        // patient table
                        $res = $GLOBALS ['DBH']->query ( "GRANT SELECT ON healthcare2.patient TO '".$_POST['username']."'@'localhost' identified by '".$_POST['pass']."'" );

                        // background table
                        $res = $GLOBALS ['DBH']->query ( "CREATE OR REPLACE VIEW healthcare2.pharmacy_background AS SELECT patient_id,prescription FROM background GROUP BY patient_id ORDER BY pdate DESC" );
                        $res = $GLOBALS ['DBH']->query ( "GRANT SELECT ON healthcare2.pharmacy_background TO '".$_POST['username']."'@'localhost' identified by '".$_POST['pass']."'" );

                        // doctor table
                        $res = $GLOBALS ['DBH']->query ( "GRANT SELECT ON healthcare2.doctor TO '".$_POST['username']."'@'localhost' identified by '".$_POST['pass']."'" );

                        // patient_doctor_trust table
                        // doctor pharmacy
                        // researcher 
                    }
                    catch ( Exception $e ) {
                        echo $e->getMessage ();
                    }
                    break;
                case 3: // researcher
                    Create ($GLOBALS['researcher'], "(id)", "(?)", array($_POST['username']));
                    try {
                        // patient table
                        // background table
                        // doctor table
                        // patient_doctor_trust table
                        // doctor pharmacy
                        // researcher 
                        $res = $GLOBALS ['DBH']->query ( "DROP PROCEDURE IF EXISTS `healthcare2`.`getData`" );
                        $res = $GLOBALS ['DBH']->query ( "CREATE PROCEDURE `healthcare2`.`getData`() BEGIN
SELECT doctor_id,disease_type,details,description,doctor_diagnosis,security_level,prescription,pdate 
FROM background b inner join patient p on b.patient_id=p.id 
WHERE (CASE b.security_level
        WHEN 0 THEN 1=1
        WHEN 1 THEN p.research_access > 0
        WHEN 2 THEN p.research_access > 1
        ELSE 1=0
        END);
END;" );
                        $res = $GLOBALS ['DBH']->query ( "CREATE OR REPLACE VIEW healthcare2.research_background AS SELECT disease_type,sex,job,location,geographical_position_x,geographical_position_y FROM background b inner join patient p on b.patient_id=p.id" );
                        $res = $GLOBALS ['DBH']->query ( "GRANT SELECT ON healthcare2.research_background TO '".$_POST['username']."'@'localhost' identified by '".$_POST['pass']."'" );
                        $res = $GLOBALS ['DBH']->query ( "GRANT EXECUTE ON healthcare2.* TO '".$_POST['username']."'@'localhost' identified by '".$_POST['pass']."'" );
                    }
                    catch ( Exception $e ) {
                        echo $e->getMessage ();
                    }
                    break;
            }
        }
        catch ( Exception $e ) {
            $do_create = true ;
            echo $e->getMessage ();
        }
        }
        while ( $do_create == true );
    }
?>
		<div class="container theme-showcase" role="main">
			<div class='row'>
				<div class='col-md-4 col-md-offset-4'>
					<form method=post>
                        <h3>ساخت کاربر</h3>
						<select name='type' class='form-control'>
                            <option value='0'>بیمار</option>
                            <option value='1'>دکتر</option>
                            <option value='2'>دکتر داروخانه</option>
                            <option value='3'>محقق</option>
                        </select>
						<br>
						<input type="text" name='username' class='form-control' placeholder="نام کاربری" />
						<br>
						<input type="password" name='pass' class='form-control' placeholder="رمز عبور" />
						<br>
						<input type="submit" class='form-control btn-success' value="ثبت" />
                        <a href='index.php'><input type="button" class='form-control btn-danger' value="بازگشت" /></a>
					</form>
				</div>
			</div>
        </div>
	<?php require('footer.php'); ?>
</html>
<?php
	CloseDatabase();
?>