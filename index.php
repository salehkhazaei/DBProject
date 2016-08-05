<?php 
	require_once ('config.php');
	require('header.php');
    if ( isset ($_SESSION['user']) )
    {
        redirect('query.php');
        CloseDatabase();
        die();
    }
    if ( isset ( $_POST['stunum'] ) )
    {
        global $db_username, $db_password, $db_database, $db_host, $db_version;
        $db_database = "healthcare2";
        $db_host = "localhost";
        $db_username = $_POST['stunum'];
        $db_password = $_POST['stupwd'];
        $db_version = '1.0';    
        
        if ( OpenDatabase ( $db_host, $db_database, $db_username, $db_password ) )
        {
            CloseDatabase();
            $db_username = "root";
            $db_password = '';
            OpenDatabase ( $db_host, $db_database, $db_username, $db_password );

            $_SESSION['user'] = $_POST['stunum'];
            $_SESSION['pass'] = $_POST['stupwd'];
            $_SESSION['type'] = -1;
            
            $rows = Select("id",'patient',"id=?",array($_SESSION['user']));
            if ( $rows->fetch() )
            {
                $_SESSION['type'] = 0;
            }
            $rows = Select("id",'doctor',"id=?",array($_SESSION['user']));
            if ( $rows->fetch() )
            {
                $_SESSION['type'] = 1;
            }
            $rows = Select("id",'doctor_pharmacy',"id=?",array($_SESSION['user']));
            if ( $rows->fetch() )
            {
                $_SESSION['type'] = 2;
            }
            $rows = Select("id",'researcher',"id=?",array($_SESSION['user']));
            if ( $rows->fetch() )
            {
                $_SESSION['type'] = 3;
            }
            redirect('query.php');
            CloseDatabase();
            die();
        }
    }
?>
		<div class="container theme-showcase" role="main">
			<div class='row'>
				<div class='col-md-4 col-md-offset-4'>
					<form method=post>
                        <h3>ورود</h3>
						<input type="hidden" name='login' class='form-control' value='0' />
						<input type="text" name='stunum' class='form-control' placeholder="نام کاربری" />
						<br>
						<input type="password" name='stupwd' class='form-control' placeholder="رمز عبور" />
						<br>
						<input type="submit" class='form-control btn-success' value="ورود" />
					</form>
                    <a href='createuser.php'><input type="button" class='form-control btn-danger' value="ساخت کاربر" /></a>
                    <a href='createdb.php'><input type="button" class='form-control btn-danger' value="ساخت دیتابیس" /></a>
				</div>
			</div>
        </div>
	<?php require('footer.php'); ?>
</html>
<?php
	CloseDatabase();
?>