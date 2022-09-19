<?php
   
    if(isset($_GET['email']) && !empty($_GET['email'])){
        
        $current_server_month = date('m');
        $current_server_day   = date('d');
        $current_server_year  = date('Y');

        $db_content = json_decode(file_get_contents('db/db.json'));
        $found =false;

        foreach($db_content as $key => $value){
            if($db_content[$key]->email ==  htmlspecialchars($_GET['email'])){
                $found = true;
            }

        }

        if($found){

            $token = password_hash(time(), PASSWORD_DEFAULT);

            echo json_encode(['success'=>true,'data'=>[
                'token' =>$token,
                'day'   =>$current_server_day,
                'month' =>$current_server_month,
                'year'  =>$current_server_year
            ]]);    
            die();
        }else{
            echo json_encode(['success'=>false,'data'=>[],'message'=>'Email incorrect']);
            die();
        }
    }else{
        echo json_encode(['success'=>false,'data'=>[],'message'=>'Erreur survenue']); 
        die();
    }   
   
?>