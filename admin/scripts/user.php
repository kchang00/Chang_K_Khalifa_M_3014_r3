<?php

function createUser($fname, $username, $password, $email){
    $pdo = Database::getInstance()->getConnection();

    //TODO: build the proper SQL query with the info above
    // execute it to create a user in tbl user;

    $check_query = 'INSERT INTO tbl_user(user_fname, user_name, user_pass, user_email) VALUES (:fname, :username, :password, :email)';
    $create_user_query = $pdo->prepare($check_query);
    $create_user_result = $create_user_query->execute(
        array(
            ':fname'=>$fname,
            ':username'=>$username,
            ':password'=>$password,
            ':email'=>$email
        )
    );

    //TODO: based on execution result, if everything goes through
    // redirect to the index.php
    // otherwise, return an error message

    if($create_user_result){
        redirect_to('index.php');
    }else{
        return 'Something went wrong';
    }

}

function editUser($id, $fname, $username, $password, $email){
    $pdo = Database::getInstance()->getConnection();

    //TODO: build the proper SQL query with the info above
    // execute it to create a user in tbl user;

    $check_query = 'UPDATE tbl_user SET user_fname = :fname, user_name = :username, user_pass = :password, user_email = :email WHERE user_id = :id';
    $edit_user_query = $pdo->prepare($check_query);
    $edit_user_result = $edit_user_query->execute(
        array(
            ':id'=>$id,
            ':fname'=>$fname,
            ':username'=>$username,
            ':password'=>$password,
            ':email'=>$email
        )
    );

    //TODO: based on execution result, if everything goes through
    // redirect to the index.php
    // otherwise, return an error message

    if($edit_user_result){
        redirect_to('index.php');
    }else{
        return 'Update failed';
    }

}

function getSingleUser($id){
    //TODO: set up database connection
    $pdo = Database::getInstance()->getConnection();

    //TODO: run the proper SQL fetch the user based on $id
    $get_user_query = 'SELECT * FROM `tbl_user` WHERE user_id = :id';
    $get_user_set = $pdo->prepare($get_user_query);
    $get_user_result = $get_user_set->execute(
        array(
            ':id'=>$id
        )
    );
    
    // echo $get_user_set->debugDumpParams();
    //TODO: return the user data if the above query went through
    // otherwise, return some error message

    // Is execution successful? && Am I getting something?
    // then show me the results
    if($get_user_result && $get_user_set->rowCount()){
        return $get_user_set;
    }else{
        return false;
    }
}