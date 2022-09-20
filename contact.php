<?php
    session_start();

    if(isset($_POST) && !empty($_POST)){
        $error = [];

        if(!filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)){
            $error['email'] = "Email incorrect";
        }

        if(!isset($_POST['name']) || empty($_POST['name'])){
            $error['name'] = "Veuillez renseigner le nom";
        }

        if(!isset($_POST['message']) || empty($_POST['message'])){
            $error['message'] = "Message requis";
        }
        
        if(count($error) > 0){
            $_SESSION['flash'] = [
                'success'=>true,
                'message'=>'email,nom ou message incorects'
            ]; 

            header("location:index.php");
            die();
        }else{
            $db_content = json_decode(file_get_contents('db/contact.json')); 

            array_push($db_content,[
                "name"               =>htmlspecialchars($_POST['name']),
                "email"              =>htmlspecialchars($_POST['email']),
                "message"            =>htmlspecialchars($_POST['message']),
                "date_enregistrement"=>date("d/m/Y H:i:s")
            ]);

            
            file_put_contents('db/contact.json',json_encode($db_content));

            $_SESSION['flash'] = [
                'success'=>true,
                'message'=>'Message envoyé'
            ];

            header("location:index.php");
            die();
        }
    }
?>