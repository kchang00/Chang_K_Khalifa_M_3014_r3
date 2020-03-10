<?php

function login($username, $password, $login_time_pretty){
    // sprint = like print, but returns a string
    // return sprintf('You are trying username=>%s, password=>%s', $username, $password);
    $login_time = new DateTime();
    $login_time_pretty = $login_time->format('Y/m/d H:i:s');

    $pdo = Database::getInstance()->getConnection();

    //Check existence
    // with the username = $username => direct user input
    // :username = placeholder
    $check_exist_query = 'SELECT COUNT(*) FROM `tbl_user` WHERE user_name =:username';
    $user_set = $pdo->prepare($check_exist_query);
    $user_set->execute(
        array(
            ':username'=>$username
        )
    );

    if($user_set->fetchColumn()>0){
        //Check if user and password match
        //problems: no-encryption, people could have same user and pass, SQL injection
        $check_match_query = 'SELECT * FROM `tbl_user` WHERE user_name =:username AND user_pass =:password';
        $user_match = $pdo->prepare($check_match_query);
        $user_match->execute(
            array(
                ':username'=>$username,
                ':password'=>$password
            )
        );
        // if($user_match->fetchColumn()>0){ => if fetched result is larger than 0 (use if using COUNT(*))
       while($founduser = $user_match->fetch(PDO::FETCH_ASSOC)){
           $id = $founduser['user_id'];
           $user_created = $founduser['user_date'];

           $_SESSION['user_id'] = $id;
           $_SESSION['user_name'] = $founduser['user_fname'];
           $_SESSION['user_date'] = $founduser['user_date'];

            $check_match_query = 'UPDATE `tbl_user` SET user_first_login = :logintime WHERE user_id = :id';
            $user_match = $pdo->prepare($check_match_query);
            $user_match->execute(
                array(
                    ':logintime'=>$login_time_pretty,
                    ':id'=>$id
                )
            );

            // fetch user_first_login from database
            $user_first_login = $founduser['user_first_login'];
       }

       if(isset($id)){
           // if user_first_login == not NULL
           // redirect_to('index.php');
           // else redirect_to('admin_edituser.php');
           if(isset($user_first_login)){
                redirect_to('index.php');
           }else{
                redirect_to('admin_edituser.php');
           }
           
        }else{
            return 'Incorrect password';
        }
     }else{
        return 'User does not exist';
    }

}

function confirm_logged_in(){
    if(!isset($_SESSION['user_id'])){
        redirect_to('admin_login.php');
    }
}

function logout(){
    session_destroy();
    redirect_to('admin_login.php');
}

function check_time_limit(){
    // grab the session data instead
    // $user_date = '2020-03-9 19:40:00';
    $user_date = $_SESSION['user_date'];
    // formatting necessary to pass into db
    // order of formatting important ->modify changes the variables
    $user_created_time = new DateTime($user_date);
    $user_created_time_pretty = $user_created_time->format('Y/m/d H:i:s');

    $time_limit = $user_created_time->modify('+5 minutes');
    $time_limit_pretty = $time_limit->format('Y/m/d H:i:s');

    $login_time = new DateTime();
    $login_time_pretty = $login_time->format('Y/m/d H:i:s');

    // for debugging
    // print_r('User created: '. $user_created_time_pretty . "\n");
    // echo nl2br("\n");
    // print_r('User must log in by: '. $time_limit_pretty . "\n");
    // echo nl2br("\n");
    // print_r('User login: '. $login_time_pretty . "\n");
    // echo nl2br("\n");

    // $calculate_limit = $user_created_time->diff($login_time);
    // echo $calculate_limit->format('%y years, %m months, %d days, %h hours, %i minutes ago');

    // if the user still has time
    if($time_limit > $login_time) {
        $_SESSION['time_message'] = 'user can log in';
        
    }else{
        // logout();
        $_SESSION['time_message'] = 'Account expired. Please contact the administrator.';
    }
}