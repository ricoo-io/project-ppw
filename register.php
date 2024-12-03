<?php 
    session_start();
    require_once "dbcontroller.php";
    $db = new dbcontroller;
?>

<?php
$notification = '';
$registration_success=false;
if (isset($_POST['reg'])) {
    $name = $_POST['username'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $konfirmasi = md5($_POST['konfirmasi']);

    $checkEmail = "SELECT email FROM t_user WHERE email = '$email'";
    $db->runSQL($checkEmail);

    if ($db->getAffectedRows() > 0) {
        $notification = 'Email is already registered. Please use a different email.';
    } 
    else {
    if ($password === $konfirmasi) {
            $sql = "INSERT INTO t_user (f_nama, email, f_password) VALUES ('$name', '$email', '$password')";
            $db->runSQL($sql);
        if ($db->getAffectedRows() > 0) {
            $registration_success=True;
            $notification = 'Registration successful. You can <a href="login.php">login now</a>.';
            } 
            else {
                $notification = 'Registration failed. Please try again.';
            }
        } else 
        {
            $notification ='Maaf password anda tidak sesuai dengan yang dimasukan';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Register</title>
        <link rel="stylesheet" href="css/styles.css">
        <link rel="icon" href="public\images\logo.png" type="image/gif" sizes="16x16">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    </head>

    <body>

        <div class="header">
            <a href="login.php"><button class="btn-popup">Login</button></a>
            <a href="register.php"><button class="btn-popup">Register</button></a>
        </div>

        <div class="container">

            <div class="image-placeholder">
                <h1>Welcome to</h1>
                <img src="public\images\logo_nobg2.png" alt="Logo">
            </div>

            <div class="form-container">

                <h2>Register</h2>

                <form action="register.php" method="POST">

                    <div class="input-group">
                        <label for="username"><i class="fas fa-user"></i></label>
                        <input type="text" id="username" name="username" placeholder="Username" required>
                    </div>

                    <div class="input-group">
                        <label for="email"><i class="fas fa-envelope"></i></label>
                        <input type="email" id="email" name="email" placeholder="Email" required>
                    </div>

                    <div class="input-group">
                        <label for="password"><i class="fas fa-lock"></i></label>
                        <input type="password" id="password" name="password" placeholder="Password" required>
                    </div>

                    <div class="input-group">
                        <label for="password"><i class="fas fa-lock"></i></label>
                        <input type="password" id="konfirmasi" name="konfirmasi" placeholder="Reconfirm Password" required>
                    </div>

                    <button type="submit" name="reg">Register</button>

                </form>

                <div class="login">
                    <p>Already have an account? <a href="login.php">Login</a></p>
                    <?php if (!empty($notification)): ?>
                        <p class="notification"><?php echo $notification; ?></p> 
                    <?php endif; ?>
                    <?php if ($registration_success): ?>
                        <meta http-equiv="refresh" content ="2; url=login.php"/>
                    <?php endif; ?>
                </div>

            </div>

        </div>
        
    </body>

</html>

