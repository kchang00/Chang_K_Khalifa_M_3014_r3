<?php 
    require_once '../load.php';

    // grab the session data instead
    $user_date = '2020-03-9 19:40:00';
    // $user_date = $_SESSION['user_date'];
    // formatting necessary to pass into db
    // order of formatting important ->modify changes the variables
    $user_created_time = new DateTime($user_date);
    $user_created_time_pretty = $user_created_time->format('Y/m/d H:i:s');

    $time_limit = $user_created_time->modify('+5 minutes');
    $time_limit_pretty = $time_limit->format('Y/m/d H:i:s');

    $login_time = new DateTime();
    $login_time_pretty = $login_time->format('Y/m/d H:i:s');

    // for debugging
    print_r('User created: '. $user_created_time_pretty . "\n");
    echo nl2br("\n");
    print_r('User must log in by: '. $time_limit_pretty . "\n");
    echo nl2br("\n");
    print_r('User login: '. $login_time_pretty . "\n");
    echo nl2br("\n");

    // $calculate_limit = $user_created_time->diff($login_time);
    // echo $calculate_limit->format('%y years, %m months, %d days, %h hours, %i minutes ago');

    // if the user still has time
    if($time_limit > $login_time) {
        echo 'user can log in';
    }else{
        echo 'Account expired. Please contact the administrator.';
    }

    if(isset($_POST['submit'])) {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        if(!empty($username) && !empty($password)){
            //Login (login = function)
            $message = login($username, $password, $login_time_pretty);
        }else{
            $message = 'Please fill out the required fields';
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Welcome to the Login Page</title>
</head>
<body>
    <!-- only checked once the message is sent - if empty, display message -->
    <!-- shorthand if else statement -->
    <?php echo !empty($message)?$message:''; ?>
    <form action="admin_login.php" method="post">
        <label>Username</label><br>
        <input type="text" name="username" value=""/><br>
        <label>Password:</label><br>
        <input type="password" name="password" value=""/><br>
        <button name="submit">Submit</button>
    </form>
</body>
</html>