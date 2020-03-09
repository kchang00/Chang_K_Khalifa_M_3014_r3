<?php

function login($username, $password, $login_time_pretty){
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
       }

       if(isset($id)){
           // if user_first_login == NULL
           // redirect_to('admin_edituser.php');
           // else
           redirect_to('index.php');
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