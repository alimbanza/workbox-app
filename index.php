<?php
session_start();

if(isset($_POST) && !empty($_POST) && isset($_POST['email']) && $_GET['action'] == 'download'){
        
        $db_content = json_decode(file_get_contents('db/db.json')); 

        if(!filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)){
            echo json_encode([
                "message"=>"Email incorrect",
                "success"=>false
            ]);
            die();
        }

        $message = "";
        $message_type ='';
        $success = false;
        $toAsh = time().''.rand(100,time());
        $link = sha1($toAsh);
        $action='';
        $found = false;

        foreach($db_content as $key => $value){
         
            if($db_content[$key]->email == htmlspecialchars($_POST['email'])){
                $found = true;
                if($value->statut_download == 0 && $value->attempt < 5){
                     $db_content[$key]->link = $link;
                     $db_content[$key]->attempt= ($db_content[$key]->attempt + 1);
                     $db_content[$key]->date_enregistrement = date("d/m/Y H:i:s");
                     $action = 'send_link';
                     break;
                }else{

                    if($value->attempt == 5){
                        $action = 'attempt_maximal';
                        break;
                    }

                    if($value->statut_download == 1){
                        $action = "max_download";
                        break;
                    }
                }
            } 
        }

        if($found == false){
            array_push($db_content,[
                "email"              =>htmlspecialchars($_POST['email']),
                "link"               =>$link,
                "statut_download"    =>0,
                "status_test"        =>0,
                "attempt"            =>1,
                "date_enregistrement"=>date("d/m/Y H:i:s")
            ]);

            $action = 'send_link';
        }

        file_put_contents('db/db.json',json_encode($db_content));

        if($action == 'send_link'){
            $message = 'Vérifiez votre boite mail, car le lien vous a été envoyé';
            $success = true;
            
            try{
                mail(htmlspecialchars($_POST['email']),'Lien de téléchargement',"workbox.test.com/download.php?link=".$link);
            }catch(Exception $e){
                $response = [
                    "message"=>"Nous n'avons pas pu vous envoyé le lien<br/>Veuillez nous laisser un message dans le formulaire de contact et nous vous l'enverrons.",
                    "success"=>$success
                ];
                echo json_encode($response);
                die();
            }
        }
        
        if($action == 'attempt_maximal'){
            $message = "Nombre maximal d'essaie de téléchargement atteint";
            $success = false;
        }

        if($action == 'max_download'){
            $message = "Votre quota de téléchargement est atteint";
            $success = false;
        }  
        
        $response = [
            "message"=>$message,
            "success"=>$success 
        ];
        
        echo json_encode($response);

        return;

    }elseif(isset($_POST) && !empty($_POST) && $_GET['action'] == 'order'){

        $error = [];

        if(!filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)){
            $error['email'] = "Email incorrect";
        }

        if(!isset($_POST['name']) || empty($_POST['name'])){
            $error['name'] = "Veuillez renseigner le nom";
        }

        if(!isset($_POST['adresse']) || empty($_POST['adresse'])){
            $error['adresse'] = "Veuillez renseigner l'adresse";
        }

        if(!isset($_POST['telephone']) || empty($_POST['telephone'])){
            $error['telephone'] = "Veuillez renseigner le téléphone";
        }

        if(!isset($_POST['type']) || empty($_POST['type'])){
            $error['type'] = "Categorie client requise 1";
        }

        if($_POST['type'] !="entreprise" && $_POST['type'] != 'client'){
            $error['type'] = "Categorie client requise 2";
        }

        if(!isset($_POST['message']) || empty($_POST['message'])){
            $error['message'] = "Message requis";
        }
        

        if(count($error) > 0){
            echo json_encode($error);
            die();
        }else{

            $db_content = json_decode(file_get_contents('db/register.json')); 

            array_push($db_content,[
                "name"               =>htmlspecialchars($_POST['name']),
                "email"              =>htmlspecialchars($_POST['email']),
                "adresse"            =>htmlspecialchars($_POST['adresse']),
                "telephone"          =>htmlspecialchars($_POST['telephone']),
                "adresse"            =>htmlspecialchars($_POST['adresse']),
                "type"               =>htmlspecialchars($_POST['type']),
                "message"            =>htmlspecialchars($_POST['message']),
                "date_enregistrement"=>date("d/m/Y H:i:s")
            ]);

            
            file_put_contents('db/register.json',json_encode($db_content));
            
            try{
                mail("m.alimbanza@gmail.com",'Souscription',$_POST['message'].' '.date("d/m/Y H:i:s"));
                echo json_encode([
                    "message"=>'Merci pour le message, nous vous contacerons d\'ici là',
                    "success"=>true
                ]);
            }catch(Exception $e){
                echo json_encode([
                    "message"=>'Merci pour le message, nous vous contacerons d\'ici là',
                    "success"=>true
                ]);
            }
            die();
        }

        die();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <title>Workbox | accueil</title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand hide-table" href="#"><img src="img/logo_transparent.png" alt="" width="200"></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                <a class="nav-link" aria-current="page" href="#">Accueil</a>
                <a class="nav-link" href="#">Offre</a>
                <a class="nav-link " href="#">Contact</a>
                <a class="nav-link " href="#">A propos</a>
                <!-- <a class="nav-link disabled navbar-right" href="#" tabindex="-1" aria-disabled="true">Disabled</a> -->
            </div>
        </div>
    </div>
</nav>
<!-- <div class="container" style="padding-left:0 !important;margin-left:0 !important;padding-right:0 !important;margin-right: 0 !important;"> -->
<div class="row section-1" style="margin-top:1px !important;padding-bottom:15px !important;">
    <!-- <div class="col-md-12 " > -->
        <div class="col-md-6 col-sm-12" >
            <!-- gestion -->
            <div class="row section-text" id="section-text">
                <div class="col-md-12">
                    <h1>Pour une gestion efficace <br/>de vos ventes,</h1>
                    <br>
                    <h3>Faites le bon choix, optez <br/> pour notre logiciel
                    au bénéfice d'une bonne <br/> gestion de vos ventes !
                    </h3>
                </div>
            </div>
            <br class="hide-space">
            <br class="hide-space">
            <br class="hide-space">
            <div class="row" style="padding-left:60px !important ;" id="section-btn">
                <div class="col-md-5 space-two-btn">
                    <button class="btn btn-base btn-download open-down">Essayer gratuitement &nbsp; <i class="bi bi-windows"></i></button>
                </div>
                <div class="break-line" style="display:none;">
                    <!-- rgeiji   -->
                </div>
                <div class="col-md-7">
                    <button class="btn btn-base btn-contact licence-btn">Licence payante</button>
                </div>
            </div>
        </div>
        <div class="col-md-6 hide-table">
            <img src="img/undraw_Projections_re_ulc6-PhotoRoom.png" class="hide-table" style="width:600px" draggable="false">
        </div>
    <!-- </div> -->


</div>

<div class="container" style="padding-top:60px !important;" >
        <div class="row">
            <div class="col-md-12 text-center">
                <h4>Pourquoi utiliser workbox ?</h4>
                <hr class="hr-section"/>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 text-center">
                <div class="card-header">
                    <i class="bi bi-pie-chart-fill" style="font: size 60px;color:#e67e22;font-size: 25px;"></i><br>
                    
                </div>
                <div class="card-body">
                    <h5>Monitoring</h5>
                    <p class="classic">
                        Des statistiques en temps en réel <br>
                        pour vous aider à garder un oeil sur l'évolution de votre activité, mais aussi à 
                         monitorer les performances réalisées sur les entrées et sorties de vos produits
                    </p>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="card-header">
                    <i class="bi bi-file-earmark-pdf-fill" style="font: size 60px;color:#e67e22;font-size: 25px;"></i><br>
                </div>
                <div class="card-body">
                    <h5>Reporting</h5>
                    <p class="classic">
                        Avec notre outil, fini le temps de se casser la tête et de se noyer dans de papiers pour produire le rapport 
                        de votre activité. En quelques clics, exportez facilement en pdf vos rapports mensuels, annuels...
                    </p>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="card-header">
                    <i class="bi bi-cash" style="font: size 60px;color:#e67e22;font-size: 25px;"></i><br>
                </div>
                <div class="card-body">
                    <h5>Ventes</h5>
                    <p class="classic">
                        Grâce à une interface intuitive, vous pouvez facilement faire:recherche et choix de produit commandé et enregistrer les commandes et le paiement facilement 
                    </p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 text-center">
                <div class="card-header">
                    <i class="bi bi-truck" style="font: size 60px;color:#e67e22;font-size: 25px;"></i><br>
                </div>
                <div class="card-body">
                    <h5>Gestion stock</h5>
                    <p class="classic">
                        Gérer facilement le stock de vos produits ou articles
                    </p>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="card-header">
                    <i class="bi bi-bell" style="font: size 60px;color:#e67e22;font-size: 25px;"></i><br>
                </div>
                <div class="card-body">
                    <h5>Rupture de stock</h5>
                    <p class="classic">
                        Vous êtes alerté en temps réel de l'état de vos produits dans le stock
                    </p>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="card-header">
                    <i class="bi bi-lock" style="font: size 60px;color:#e67e22;font-size: 25px;"></i><br>
                </div>
                <div class="card-body">
                    <h5>Sécurité et fiabilité</h5>
                    <p class="classic">
                        Sécurité optimale pour vos données et opérations
                    </p>
                </div>
            </div>
        </div>
    </div>

<div class="container offre-section">
    <div class="row">
        <div class="col-md-12 text-center">
            <h4>Offres</h4>
            <hr class="hr-section"/>
        </div>
    </div>
    <div class="row" >
        <div class="col-md-3"></div>
        <div class="col-md-3 pricing-card" style="margin-bottom:20px !important;">
            <div class="pricing-type text-center">
                <h5>
                Gratuit
                <br>
                    <span style="color:#e67e22;">0$</span>
                </h5>
            </div>
            <div class="pricing-body text-center">
                <ul class="fonctionality">
                    <li class="fonctionnaly-list">
                        Statitiques 
                        <hr style="width:50%;margin-top:4px !important;" >
                    </li>
                   
                    <li class="fonctionnaly-list">
                        Gestion utilisateur(2)
                        <hr style="width:50%;margin-top:4px !important;" >
                    </li>
                    <li class="fonctionnaly-list">
                         Catégorie article
                        <hr style="width:50%;margin-top:4px !important;" >
                    </li>
                    <li class="fonctionnaly-list">
                        Alert rupture stock.
                       <hr style="width:50%;margin-top:4px !important;" >
                   </li>
                   <li class="fonctionnaly-list">
                     Valorisation stock
                   <hr style="width:50%;margin-top:4px !important;" >
                   </li>
                   <li class="fonctionnaly-list">
                    Rapport vente(pdf).
                   <hr style="width:50%;margin-top:4px !important;" >
                    </li>
                    <li class="fonctionnaly-list">
                        Compte client
                       <hr style="width:50%;margin-top:4px !important;" >
                   </li>
                   <li class="fonctionnaly-list">
                    Personnalisation facture
                    <hr style="width:50%;margin-top:4px !important;" >
                    </li>
                    <li class="fonctionnaly-list">
                        Choix devise.
                       <hr style="width:50%;margin-top:4px !important;" >
                   </li>
                   <li class="fonctionnaly-list">
                    Limite tot. vente(60 max)
                   <hr style="width:50%;margin-top:4px !important;" >
                    </li>
                </ul>
            </div>
            <div class="pricing-footer text-center">
                <a href="#" class="btn offre-btn-one open-down">Télécharger</a>
            </div>
        </div>
        <div class="col-md-3 pricing-card">
            <div class="pricing-type text-center">
                <h5>
                    Premium
                    <br>
                    <span style="color:#e67e22;">250$</span> 
                </h5>
            </div>
            <div class="pricing-body">
                <ul class="fonctionality">
                    <li class="fonctionnaly-list">
                        Statitiques 
                        <hr style="width:50%;margin-top:4px !important;" >
                    </li>
                   
                    <li class="fonctionnaly-list">
                        Gestion utilisateur(2)
                        <hr style="width:50%;margin-top:4px !important;" >
                    </li>
                    <li class="fonctionnaly-list">
                         Catégorie article
                        <hr style="width:50%;margin-top:4px !important;" >
                    </li>
                    <li class="fonctionnaly-list">
                        Alert rupture stock.
                       <hr style="width:50%;margin-top:4px !important;" >
                   </li>
                   <li class="fonctionnaly-list">
                     Valorisation stock
                   <hr style="width:50%;margin-top:4px !important;" >
                   </li>
                   <li class="fonctionnaly-list">
                    Rapport vente(pdf).
                   <hr style="width:50%;margin-top:4px !important;" >
                    </li>
                    <li class="fonctionnaly-list">
                        Compte client
                       <hr style="width:50%;margin-top:4px !important;" >
                   </li>
                   <li class="fonctionnaly-list">
                    Personnalisation facture
                    <hr style="width:50%;margin-top:4px !important;" >
                    </li>
                    <li class="fonctionnaly-list">
                        Choix devise.
                       <hr style="width:50%;margin-top:4px !important;" >
                   </li>
                   <li class="fonctionnaly-list">
                    Limite tot. vente illimité
                   <hr style="width:50%;margin-top:4px !important;" >
                    </li>
                </ul>
            </div>
            <div class="pricing-footer text-center">
                <a href="#" class="btn offre-btn-two licence-btn">Souscrire</a>
            </div>
        </div>
        <div class="col-md-3"></div>
    </div>
</div>


<div class="row" style="height:auto !important; color:#fff !important;margin-top: 5%; background: #000;padding:40px 20px !important;">
    <div class="col-md-4">
        <div class="header-text-footer">
            <h6 class="classic">A propos</h6>
        </div>
        <div class="header-body-footer">
            <br/>
            <p>
                Workbox est une solution(logiciel) de gestion de stock et des ventes, pensée et développée pour aider à une gestion efficace des opérations dès l'entrée des articles en stock jusqu'à leur sortie et cela grâce à une interface fluide 
                
            </p>
        </div>
    </div>
    <div class="col-md-4 hide-mention">
        <div class="header-text-footer text-center">
            <h6 class="classic" style="font-family: 'Roboto light' !important;">Mentions légales</h6>
        </div>
        <div class="header-body-footer text-center">
            <br>
            <p style="text-align:center  !important;">
                Tous droits réservés 
            </p>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="download-modal">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header text-center">
              <h5 class="modal-title" style="color: black !important;">Téléchargement</h5>
              <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
            </div>
            <div class="modal-body">
              <div class="alert" id="alert-download" style="display:none !important;">
              
              </div>
              <input type="email" class="form-control" id="download-email" placeholder="email" style="font-family: 'Roboto light';">
            </div>
            <div class="modal-footer">
              <button type="button" class="btn envoyer-lien" style="background: #e67e22;color: #fff;">Envoyer le lien</button>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade" tabindex="-1" id="licence-modal">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header text-center">
              <h5 class="modal-title" style="color: black !important;">Souscription</h5>
              <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
            </div>
            <div class="modal-body">
            <div class="alert" id="alert-register" style="display:none !important;">
              
              </div>
              <div class="row">
                  <div class="col-md-6 form-group">
                      <input type="text" class="form-control customer-name" placeholder="nom" style="font-family: 'Roboto light';">

                  </div>
                  <div class="col-md-6 form-group">
                    <input type="text" class="form-control customer-mail" placeholder="email" style="font-family: 'Roboto light';">
                  </div>
              </div>
              <br>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <input type="tel" class="form-control customer-adresse" placeholder="adresse" style="font-family: 'Roboto light';">
                    </div>
                    <div class="col-md-6 form-group">
                        <input type="tel" class="form-control customer-phone" placeholder="telephone" style="font-family: 'Roboto light';">
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-12">
                        <select name="" class="form-control customer-type" style="font-familly:'Roboto light' !important;">
                            <option>Vous êtes ?</option>
                            <option value="entreprise">Entreprise</option>
                            <option value="client">Client</option>
                        </select>
                    </div>
                </div>
              <br>
              <div class="row">
                  <div class="col-md-12">
                      <textarea name="" id="" cols="30" rows="3" class="form-control customer-message">Message</textarea>
                  </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-customer" style="background: #e67e22;color: #fff;">Commander</button>
            </div>
          </div>
        </div>
      </div>

    <div class="col-md-4 text-right">
        <div class="header-text-footer">
            <h6 class="classic" style="text-align:left !important ;">Contact</h6>
        </div>
        <div class="header-body-footer" style="text-align:left !important ;">
            <br>
            <form action="contact.php" method="post">
                <input type="text" name="name" class="form-control" placeholder="name">
                <br/>
                <input type="email" name="email" class="form-control" placeholder="email"/>
                <br/> 
                <textarea name="message" class="form-control classic" cols="30" rows="5">Message</textarea>
                <br/>
                <button type="submit" class="btn classic" style="background:#e67e22 !important;color:#fff !important;">Envoyer</button>
            </form>
       
        </div>
    </div>
</div>
<!-- </div> -->
<script src="js/jquery-3.6.0.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/app.js"></script>
<script>
    <?php if(isset($_SESSION['flash']) && !empty($_SESSION['flash'])):?>
        alert("<?php echo $_SESSION['flash']['message'];?>");
    <?php endif;?>
</script>
</body>
</html>

<?php
    unset($_SESSION['flash']);
?>