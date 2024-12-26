<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/thankyou.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" href="public\images\logo2.png" type="image/gif" sizes="16x16">
    <title>KnowDays</title>
</head>
<body>

    <nav>
        <div class="img">
            <a href="index.php"><img src="public\images\logo_nobg.png" alt="" style="height: 50px; "></a>
        </div>

        <form method="POST"class="search-container">
            <input type="text" id="search" name="search" placeholder="Search.." >
            <button type="submit" name="submit_search"><img src="public\images\search.jpg" alt=""></button>
        </form>

        <ul>
            <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i> User</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav> 

    <div class="container">
        <img src="public/images/thankyou_bg.jpg" height="1080" width="1920" class="background-image"   alt="">
        <div class="content">
            <h1>
             THANK <br>YOU!
            </h1>
            <p>
             Your receipt will be sent <br> to your email.
            </p>
            <h2>
             HELP SPREAD THE <br>WORD!
            </h2>
            <p>
             Tell others about your gift and the <br>life-changing Power of Fashion.
            </p>
        </div>
     </div>
</body>
</html>
