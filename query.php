<?php 
	require_once ('config.php');

	global $db_username, $db_password, $db_database, $db_host, $db_version;
	$db_database = "healthcare2";
	$db_host = "localhost";
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

	$db_username = 'root';
	$db_password = '';
	OpenDatabase ( $db_host, $db_database, $db_username, $db_password );

    $access = "";
    switch ( $_SESSION['type'] )
    {
        case 0:
                $access .= "myprofile_".$_SESSION['user']." => SELECT,UPDATE(all columns except id)<br>
                background_".$_SESSION['user']." => SELECT<br>
                doctor => SELECT<br>
                trusteddoc_".$_SESSION['user']." => SELECT,INSERT,DELETE<br>";
                $rows = Select ("patient_id",'patient_doctor_trust','patient_id=?',array($_SESSION['user']));
                if ( $rows->fetch()) 
                {
                    $res = $GLOBALS ['DBH']->query ( "REVOKE INSERT ON healthcare2.trusteddoc_".$_SESSION['user']." FROM '".$_SESSION['user']."'@'localhost'" );
                }
                else
                {
                    $res = $GLOBALS ['DBH']->query ( "GRANT INSERT ON healthcare2.trusteddoc_".$_SESSION['user']." TO '".$_SESSION['user']."'@'localhost' identified by '".$_SESSION['pass']."'" );
                }
            break;
        case 1:
                $access .= "patient_".$_SESSION['user']." => SELECT<br>
                patient_background_".$_SESSION['user']." => SELECT,UPDATE<br>
                background => INSERT<br>
                doctor => SELECT<br>
                trusteddoc_".$_SESSION['user']." => SELECT,INSERT,DELETE<br>";
            break;
        case 2:
                $access .= "patient => SELECT<br>
                pharmacy_background => SELECT<br>
                doctor => SELECT<br>";
            break;
        case 3:
                $access .= "getData() => EXECUTE<br>
                research_background => SELECT<br>";
            break;
    }
    
    CloseDatabase();
	$db_username = $_SESSION['user'];
	$db_password = $_SESSION['pass'];
	OpenDatabase ( $db_host, $db_database, $db_username, $db_password );
	require('header.php');
?>
		<div class="container theme-showcase" role="main">
            <div class='row'>
                <div class='col-md-8'>
                    <form method=post>
                    <div class='row'>
                        <div class='col-md-12'>
                            <div class='row'><div class='col-md-12'><div class='alert alert-warning alert-dismissible' role='alert'>
                            You are a<strong>
                            <?php 
                                switch ( $_SESSION['type'] )
                                {
                                    case 0: // patient
                                        echo "patient";
                                        break;
                                    case 1: // doctor
                                        echo "doctor";
                                        break;
                                    case 2: // doctor_pharmacy
                                        echo "doctor_pharmacy";
                                        break;
                                    case 3: // researcher
                                        echo "researcher";
                                        break;
                                    default:
                                        echo "unknown (probably admin)";
                                        break;
                                }
                            ?>
                            </strong></div></div></div>
                        </div>
                    </div>
                    <div class='row'>
                        <div class='col-md-12'>
                            <input type="text" name='query' class='form-control' placeholder="Query" />
                        </div>
                    </div>
                    <div class='row'>
                        <div class='col-md-6 col-md-offset-3'>
                            <input type="submit" class='form-control btn-success' value="Execute" />
                            <a href='logout.php'><input type="button" class='form-control btn-danger' value="خروج" /></a>
                        </div>
                    </div>
                    </form>
                </div>
                <div class='col-md-4'>
                    <div class='row'><div class='col-md-12'><div class='alert alert-warning alert-dismissible' role='alert'>
                    You have access to:<br>
                    <?php 
                    echo $access;
                    ?>
                    </div></div></div>
                </div>
            </div>
            <?php 
                if ( isset ($_POST['query']) )
                {
                    try {
                        $res = $GLOBALS ['DBH']->query ( $_POST['query'] );
                        
                        echo "<div class='row'><div class='col-md-12'><div class='alert alert-warning alert-dismissible' role='alert'>
                                <strong>Query:</strong> ".$_POST['query']."</div></div></div>";
                        echo "<div class='row'><div class='col-md-12'><div class='alert alert-warning alert-dismissible' role='alert'>
                                <strong>".$res->rowCount()." row(s)</strong> were affected.</div></div></div>";
                        if ( substr(strtolower(trim($_POST['query'])),0,6) === 'select' )
                        {
                            echo "<table class='table table-striped'>";
                            $headers = false ;
                            while ( $row = $res->fetch() )
                            {
                                if ( $headers == false )
                                {
                                    echo "<tr>";
                                    foreach ( $row as $key => $value)
                                    {
                                        if ( is_numeric ($key) )
                                            continue;
                                        echo "<th>".$key."</th>";
                                    }
                                    echo "</tr>";
                                    $headers = true;
                                }
                                echo "<tr>";
                                foreach ( $row as $key => $value)
                                {
                                    if ( is_numeric ($key) )
                                        continue;
                                    echo "<td>".$value."</td>";
                                }
                                echo "</tr>";
                            }
                            echo "</table>";
                        }
                    }
                    catch ( Exception $e ) {
                        
                        echo $e->getMessage ();
                    }
                }
            ?>
        </div>
	<?php require('footer.php'); ?>
</html>
<?php
	CloseDatabase();
?>