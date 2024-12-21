<?php
    session_start();
    require_once "dbcontroller.php";
    $db = new dbcontroller;

    require_once 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$clientID = $_ENV['CLIENT_ID'];
$clientSecret = $_ENV['CLIENT_SECRET'];
$redirectUri = $_ENV['REDIRECT_URI'];

$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");


if (isset($_GET['code'])) {
  $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
  $client->setAccessToken($token['access_token']);

    $google_oauth = new Google_Service_Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();
    $email =  $google_account_info->email;
    $name =  $google_account_info->name;
    $token=$token['access_token'];
    $sql_check = "SELECT * FROM t_user WHERE email='$email'";
    $count = $db->rowCOUNT($sql_check);

    if ($count == 0) {
        
        $sql_insert = "INSERT INTO t_user (f_nama, email, f_password) VALUES ('$name', '$email', '$token')";
        $db->runSQL($sql_insert);
        
        $sql_get_id = "SELECT f_id FROM t_user WHERE email = '$email'";
        $user = $db->getITEM($sql_get_id);
        $userId = $user['f_id'];
    } else {
        
        $sql_get_id = "SELECT f_id FROM t_user WHERE email = '$email'";
        $user = $db->getITEM($sql_get_id);
        $userId = $user['f_id'];
    }
    
    $_SESSION['email'] = $email;
    $_SESSION['name'] = $name;
    $_SESSION['iduser'] = $userId;
    $_SESSION['loggedin'] = true; 
    header("Location: index.php");
    exit();

} 
?>

<?php
if (isset($_POST['log'])) {
    $email = $_POST['email']; 
    $password = md5($_POST['password']);
    $notification = '';
    $captchaAnswer = $_POST['captcha_answer'];

    if ($captchaAnswer != $_SESSION['captcha_text']) {
        $notification ='Incorrect CAPTCHA. Please try again.';
    } else 
    {
        $sql = "SELECT * FROM t_user WHERE email='$email' AND f_password='$password'";
        $count = $db->rowCOUNT($sql);

        if ($count == 0) {
            $notification ='Email atau password salah';
        } else {;

            $sql = "SELECT * FROM t_user WHERE email='$email' AND f_password='$password'";
            $row = $db->getITEM($sql);

            $_SESSION['email'] = $row['email'];
            $_SESSION['name'] = $row['f_nama'];
            $_SESSION['iduser'] = $row['f_id'];
            $_SESSION['role'] = $row['f_peran'];
            $_SESSION['loggedin'] = true; 
            header("Location: index.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="public\images\logo.png" type="image/gif" sizes="16x16">

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

            <h2>Login</h2>

            <form action="login.php" method="POST">

                <div class="input-group login">
                    <label for="email"><i class="fas fa-envelope"></i></label>
                    <input type="email" id="email" name="email" placeholder="Email" required>
                </div>
               
                <div class="input-group login">
                    <label for="password"><i class="fas fa-lock"></i></label>
                    <input type="password" id="password" name="password" placeholder="Password" required>
                </div>

                <div class="input-group-captcha">
                    <img src="captcha.php" alt="CAPTCHA Image">
                    <input type="text" id="captcha_answer" name="captcha_answer" placeholder="Answer the question above" required>
                </div>
                <button type="submit" class="login-button" name="log">Login</button> 
           
            </form>

            <div class="separator">

                <hr>
                <span>or login with</span>
                <hr>
                
            </div>

            <div class="google-login">
                
            <a href="<?php echo $client->createAuthUrl(); ?>">
                <button type="button" class="google-button">
                    <i class="fab fa-google"></i>Google
                </button>
            </a>
            </div>

            
            <p class="register-login">Don't have an account? <a href="register.php">Register</a></p>

            <?php if (!empty($notification)): ?>
                <p class="notification"><?php echo $notification; ?></p> 
            <?php endif; ?>

    </div>
    
</body>
</html>
