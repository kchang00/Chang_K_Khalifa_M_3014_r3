<?php

function login($username, $password){
    // sprint = like print, but returns a string
    // return sprintf('You are trying username=>%s, password=>%s', $username, $password);

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
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $founduser['user_fname'];
            // fetch user_login from database
            $user_login = $founduser['user_login'];
            // fetch user_date from database
            $user_created = $founduser['user_date'];
            // fetch user_lock from database
            $user_lock = $founduser['user_lock'];

            $login_time = new DateTime();
            $login_time_pretty = $login_time->format('Y/m/d H:i:s');

            $check_match_query = 'UPDATE `tbl_user` SET user_login = :logintime WHERE user_id = :id';
            $user_match = $pdo->prepare($check_match_query);
            $user_match->execute(
                array(
                    ':logintime'=>$login_time_pretty,
                    ':id'=>$id
                )
            );
       }

       if(isset($id)){
            // grab user_created time
            // formatting necessary to pass into db
            // order of formatting important ->modify changes the variables
            $user_created_time = new DateTime($user_created);
            $user_created_time_pretty = $user_created_time->format('Y/m/d H:i:s');

            $time_limit = $user_created_time->modify('+1 minutes');
            $time_limit_pretty = $time_limit->format('Y/m/d H:i:s');

            // for debugging
            // print_r('User created: '. $user_created_time_pretty . "\n");
            // echo nl2br("\n");
            // print_r('User must log in by: '. $time_limit_pretty . "\n");
            // echo nl2br("\n");
            // print_r('User login: '. $login_time_pretty . "\n");
            // echo nl2br("\n");
            // exit;
            // $calculate_limit = $user_created_time->diff($login_time);
            // echo $calculate_limit->format('%y years, %m months, %d days, %h hours, %i minutes ago');

            // if account is unlocked
            if($user_lock != 1) {
                // if the user still has time
                if($time_limit > $login_time){
                    // if the user has logged in before
                    // meaning, there is a value for user_login and is not NULL
                    // redirect_to('index.php');
                    // else (the user has NOT logged in before) redirect_to('admin_edituser.php');
                    if(isset($user_login)){
                        redirect_to('index.php'); 
                    }else{
                        redirect_to('admin_edituser.php');
                    }
                // if the user does not have time
                }else{
                    // if the user has logged in before
                    if(isset($user_login)){
                        redirect_to('index.php'); 
                    // if it's the user's first time logging in
                    // and the time has expired
                    }else{
                        // lock the account
                        $check_match_query = 'UPDATE `tbl_user` SET user_lock = :lock WHERE user_id = :id';
                        $user_match = $pdo->prepare($check_match_query);
                        $user_match->execute(
                            array(
                                ':lock'=>1,
                                ':id'=>$id
                            )
                        );
                        return 'Account has expired. Please contact the administrator.';
                    }
                }
            }else{
                return 'Account has been locked. Please contact the administrator.';
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