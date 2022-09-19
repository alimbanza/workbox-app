<?php

    if(isset($_GET['link']) && !empty($_GET['link'])){

        $db_content = json_decode(file_get_contents('db/db.json')); 

        $found   = false;
        $action  = '';

        foreach($db_content as $key => $value){
         
            if($db_content[$key]->link == $_GET['link']){
                $found = true;
                if($value->statut_download == 0){
                     $db_content[$key]->statut_download = 1;
                     $action = 'download';
                     break;
                }else{
                    if($value->statut_download == 1){
                        $action = "max_download";
                        break;
                    }
                }
            }else{
                $found=false;
            } 
        }

        file_put_contents('db/db.json',json_encode($db_content));

        if($found){
            if($action == 'download'){

                header("Content-Type: application/zip");

                header('Content-Disposition: attachement; filename="workbox.zip"');

                readfile("storage/workbox.zip");
                die();
            }else{
                header("location:index.php");
                die();
            }
        }else{
            header("location:index.php");
            die();
        }
    }
    die();
?>