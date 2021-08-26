<!DOCTYPE html>
<html>
<title>Mini-Pinterest</title>
<meta charset="utf8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css"><!-- obliger d'avoir internet si non pas de css donc affichage basique et simpliste pareil pour les 2 link ci-dessous, ajout des liens au lieu des dossiers contenant les css pour diminuer la taille lors du dépot -->
<link rel="stylesheet" href="https://www.w3schools.com/lib/w3-theme-black.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src='https://kit.fontawesome.com/a076d05399.js'></script> <!-- responsable pour les icones fafa-user -->

<body id="myPage">

<?php
  session_start();
  $time=time(); 
  $mysqli = new mysqli('localhost:3306', 'p1808891', 'e15c84', 'p1808891'); // mdp et bdd à ajouter
  //$mysqli = new mysqli('localhost', 'root', 'root', 'pinterest', 3307); // mdp et bdd à ajouter

  /* Vérification de la connexion */
  if (mysqli_connect_errno()) {
    printf("Échec de la connexion : %s\n", mysqli_connect_error());
    exit();
  }
  $mysqli->set_charset('utf8');

  function execute_query($bdd,$query)
  {
  if($result = mysqli_query($bdd,$query))
  {
    $data = $result->fetch_all();
    mysqli_free_result($result);
    return $data;
  }
  }


  function Secure($donnee)
  {
    $donnee = trim($donnee); // supprime les espaces du début et fin de ce qui va etre saisi
    $donnee = stripslashes($donnee);
    $donnee = strip_tags($donnee); // supprime les balises html ou php saisi
    //$donnee = mysqli_escape_string($donnee); marche pas sur mon ordi version php peut etre
    return $donnee; 
  }

  if(isset($_POST['connecter'])) {  
    if(isset($_SESSION['wrong_pass'])){unset($_SESSION['wrong_pass']);}
    if(isset($_SESSION['compte_bloquee'])){unset($_SESSION['compte_bloquee']);}
    $id = $_POST['Identifiant'];
    $pass = $_POST['pass'];

    $id = Secure($id);
    $pass = Secure($pass);
    if($result = mysqli_query($mysqli," SELECT * FROM utilisateur WHERE Pseudo = '$id' AND PassWord = '$pass' ")){
      $data = $result->fetch_all();
      mysqli_free_result($result);
      $count = count($data);
      if($count == 1 AND $data[0][5]=='oui'){
        $_SESSION['logged']='logged';
        $_SESSION['id']=$data[0][1];
        $_SESSION['pwd']=$data[0][2];
        $_SESSION['profil']=$data[0][3];
        $_SESSION['time']=time();

        if($result = mysqli_query($mysqli," UPDATE utilisateur SET connecte = '1' WHERE Pseudo = '$id'"))
        {   
          
        }
        // return $_SESSION['logged'];
        // echo $_SESSION['logged'] ."rien " ;
        /*echo " <script> document.getElementById('menu_logged').style.display='block';
          document.getElementById('menu_general').style.display='none';
          </script>  "; */
        //include('menu_logged.html');
         
        /*   echo "<div class=\"w3-top\" id=\"acceuil\">
          <div class=\"w3-bar w3-theme-d2 w3-left-align\"> 
          <a class=\"w3-bar-item w3-button w3-hide-medium w3-hide-large w3-right w3-hover-white w3-theme-d2\" href=\"javascript:void(0);\" onclick=\"openNav()\"><i class=\"fa fa-bars\"></i></a>
          <a href=\"#myPage\" class=\"w3-bar-item w3-button w3-teal\"><i class=\"fa fa-home w3-margin-right\"></i>Acceuil</a>
          <a onclick=\"document.getElementById('menu_logged').style.display='block'\" class=\"w3-bar-item w3-button w3-hide-small w3-hover-white\"> Ajout de photo </a>
          <a href=\"#images\" class=\"w3-bar-item w3-button w3-hide-small w3-hover-white\"> Images </a>
          <a href=\"#contact\" class=\"w3-bar-item w3-button w3-hide-small w3-hover-white\">A Propos</a>
          <a href=\"#\" class=\"w3-bar-item w3-button w3-hide-small w3-right w3-hover-teal\" title=\"Search\"><i class=\"fa fa-search\"></i></a>
          </div>
          </div> ";*/
      }
      else{
        if(($count==1) && ($data[0][5]=='non')){$_SESSION['compte_bloquee']='bloquee';}
        else{$_SESSION['wrong_pass']='wrong';}
        //include('general_menu.php');
      }
    }
  }
  if(isset($_SESSION['logged'])){if(isset($_POST['deconnect'])){$s=$_SESSION['id'];session_unset();if($result = mysqli_query($mysqli," UPDATE utilisateur SET connecte = '0' WHERE Pseudo = '$s'")){}}}
  if(isset($_SESSION['wrong_pass'])){if(isset($_POST['remove_modal'])){unset($_SESSION['wrong_pass']); }}
  
  if(isset($_SESSION['compte_bloquee'])){
    if(isset($_POST['remove_modal'])){
      unset($_SESSION['compte_bloquee']);
    }
  }

function display_time($time_secs)
  {
    $secs=$time_secs%60;
    $minutes=($time_secs/60)%60;
    $heures=($time_secs/3600)%60;
    $jours=($time_secs/86400)%60;
    echo "<form action=\"\"><button type=submit class=\"w3-bar-item w3-button w3-hide-small w3-right w3-hover-teal\"> Temps de connexion : ".$jours."d ".$heures."h ".$minutes."m ".$secs."s </button></form>";
  }

function select_categorie($categorie,$bdd){  
  $data=execute_query($bdd, "SELECT photo.nomFich , photo.description , categorie.nomCat , utilisateur.Pseudo FROM photo JOIN categorie ON photo.catId=categorie.catId JOIN utilisateur ON photo.UserId=utilisateur.UserId  WHERE categorie.nomCat='$categorie' AND photo.etat='montre' ");
  return $data;
}



function add_categorie($categorie,$bdd,$data)
{
  $data2=select_categorie($categorie,$bdd);
  $c1=count($data);
  $c2=count($data2);
  $count=0;
  while($count<$c2)
  {
    $data[$count+$c1][0]=$data2[$count][0];
    $data[$count+$c1][1]=$data2[$count][1];
    $data[$count+$c1][2]=$data2[$count][2];
    $data[$count+$c1][3]=$data2[$count][3];
    $count=$count+1;
  }
  return $data;
}

$data=select_categorie('Rien',$mysqli);

function verif_pseudo($pseudo,$bdd)
{
  $ls_pseudo=execute_query($bdd,"SELECT Pseudo FROM utilisateur");
  $count=0;
  while($count<count($ls_pseudo))
  {
    if($ls_pseudo[$count][0]==$pseudo){return false;}
    $count=$count+1;
  }
  return true;
}  

function inscription($pseudo,$mdp,$confirm_mdp,$bdd)
{
  if(($mdp==$confirm_mdp)&&(verif_pseudo($pseudo,$bdd))&&($mdp!="")&&($pseudo!=""))
  {
    $query_insert="INSERT INTO utilisateur (Pseudo,PassWord,Profil,active,connecte) VALUES ('$pseudo','$mdp','Utilisateur','oui',0)";
    if($result=mysqli_query($bdd,$query_insert)){
      echo 'Votre inscription a été faîte avec succées';
      echo"<script>document.getElementById('id03').style.display='block';</script>";
    } 
  }
  else
  {
    if(($mdp=="")||($pseudo==""))
    {echo "Des données n'ont pas été saisies";}
    else{if(!verif_pseudo($pseudo,$bdd))
    {echo 'Le pseudo que vous avez saisi existe déjà.Veuillez en saisir un autre';}
    else{if(($mdp!=$confirm_mdp)&&($pseudo!=""))
    {echo 'Veuillez correctement confirmer votre mot de passe';}}}
    echo"<script>document.getElementById('id02').style.display='block';</script>";
  }
}
  
  function photo_display($data)
  {
      $count=0;
      while($count<count($data))
      {if($count%4==0){echo "<div class=\"w3-row\"><br>";}
      $d0=$data[$count][0];
      $d1=$data[$count][1];
      $d2=$data[$count][2];
      $d3=$data[$count][3];
      echo "<div class=\"w3-quarter\"><button class=\"w3-button\" onclick=\"document.getElementById('$count').style.display='block'\";>
        <img src=assets/image/$d0 alt=chèvre style=width:100% class=w3-hover-opacity>
        </button>
        <div class=\"w3-modal\" id=$count>
          <div class=\"w3-modal-content w3-card-4 w3-animate-top\" style=width:45%;>
          <header class=\"w3-container w3-teal w3-display-container\"> 
            <span onclick=document.getElementById('$count').style.display='none' class=\"w3-button w3-teal w3-display-topright\"><i class=\"fa fa-remove\"></i></span>
            <h4> Les détails sur cette photos </h4>
          </header>
          <div class=\"w3-card-4\" style=\"overflow-x:auto;\">
            <div class=\"w3-container\">
              <hr>
              <img src=assets/image/$d0 alt=$count+1 class=\"w3-left w3-margin-right\" style=width:50%;>
                <table border=1 style=border-collapse: collapse;>
                  <tr>
                    <th> Description </th>
                    <td>$d1</td>
                  </tr>
                
                  <tr>
                    <th> nom fichier </th>
                    <td> $d0 </td>
                  </tr>
                  <tr> 
                    <th> Catégorie </th>
                    <td> <a href=?$d2=$d2#team onclick=document.getElementById('$count').style.display='none';>$d2</a> </td>
                  </tr>
                    <th> Propriétaire </th>
                    <td> $d3 </td>
                </table>
            </div>
          </div>
        </div></div></div>";
        if((($count+1)%4==0)||($count==count($data)-1)){echo "</div>";}
        $count=$count+1;
      }
      
  }

  $cat=execute_query($mysqli,"SELECT nomCat FROM categorie");  
  //$query_test="UPDATE photo SET nomFich='nouveau' WHERE photoId='5'";
  //if($result=mysqli_query($mysqli,$query_test))
?>



<script>
</script>
<!-- Sidebar on click -->


<div id="menu_general">
<div class="w3-top" id="acceuil">
 <div class="w3-bar w3-theme-d2 w3-left-align">
  <a class="w3-bar-item w3-button w3-hide-medium w3-hide-large w3-right w3-hover-white w3-theme-d2" href="javascript:void(0);" onclick="openNav()"><i class="fa fa-bars"></i></a>
  <a href="#myPage" class="w3-bar-item w3-button w3-teal"><i class="fa fa-home w3-margin-right"></i>Acceuil</a>
  <a href="#tmp_images" class="w3-bar-item w3-button w3-hide-small w3-hover-white"> Images </a>
  <a href="#contact" class="w3-bar-item w3-button w3-hide-small w3-hover-white">A Propos</a>
  <?php if(isset($_SESSION['logged'])){echo "<a onclick=\"document.getElementById('form_add_photo').style.display='block'\" class=\"w3-bar-item w3-button w3-hide-small w3-hover-white\"> Ajout de photo </a> 
  <a onclick=\"document.getElementById('profil').style.display='block'\" class=\"w3-bar-item w3-button w3-hide-small w3-right w3-hover-teal\" title=\"fa-user\"><i class='far fa-user' style='font-size:20px;color:blue'></i> ".$_SESSION['id']." </a>
  <form action=\"\" method=\"post\"><button name=\"deconnect\" type=\"submit\" class=\"w3-bar-item w3-button w3-hide-small w3-right w3-hover-white\">Déconnexion</button></form>";
  display_time($time-$_SESSION['time']);}
  else{echo"<a onclick=\"document.getElementById('id01').style.display='block'\" class=\"w3-bar-item w3-button w3-hide-small w3-right w3-hover-white\"> Se connecter </a>";}?>
  
 </div>
</div>
</div>


<?php // test sur le formualire: récupération de la catégorie et teste d'image

if (isset($_POST["upload"])) {
    $categorie = $_POST['categorie'];
    echo $categorie;
    $description = $_POST['description'];
    echo $description;
    $nomFich = $_FILES['file-input']['name'];
    echo $nomFich;
    $extension_acceptee = array(
        "png",
        "PNG",
        "gif",
        "jpeg"
    );
    
    // récuperer l'extension du fichier
    $file_extension = pathinfo($_FILES["file-input"]["name"], PATHINFO_EXTENSION);
    
    // Vérifier si le champ n'est pas vide,un + du required en html
    if (! file_exists($_FILES["file-input"]["tmp_name"])) {
        $response = array(
            "type" => "error",
            "message" => "Veuillez sélectionné une image."
        );
    }    // Tester si le format est accépté ou pas
    else if (! in_array($file_extension, $extension_acceptee)) {
        $response = array(
            "type" => "error",
            "message" => "Format invalide, Veuillez choisir les formats : jpeg, png, gif."
        );
        
    }    // teste de la taille en octet(100000) = 100 KO
    else if (($_FILES["file-input"]["size"] > 100000)) {
        $response = array(
            "type" => "error",
            "message" => "L'image dépasse la taille autorisé: 100 KO "
        );
    }    // Validate image file dimension
    
    else {
        $target = "assets/image/" . basename($_FILES["file-input"]["name"]);
        if (move_uploaded_file($_FILES["file-input"]["tmp_name"], $target)) {
          // vu que c'est en auto increment j'ai retiré celà
          $photoId_tab = execute_query($mysqli, "SELECT MAX(photoId) FROM photo");
          $Id_suiv_photo = $photoId_tab[0][0] + 1;
          $categorie = $_POST['categorie'];
          echo $categorie;
          $desc = $_POST['description'];
          $description = addslashes($desc);
          echo $description;
          $nomFich = $_FILES['file-input']['name'];
          echo $nomFich;
          $catId_tab = execute_query($mysqli,"SELECT catId FROM categorie WHERE nomCat='$categorie'");
          $catId=$catId_tab[0][0];
          $compte = $_SESSION['id']; 
          //echo $compte;
          $userId_tab = execute_query($mysqli,"SELECT UserId FROM utilisateur WHERE Pseudo='$compte'"); 
          $userId=$userId_tab[0][0];
          //echo $userId;
          $query_insert_image = "INSERT INTO photo (photoId, nomFich, description, catId, UserId, etat) VALUES 
            ($Id_suiv_photo, '$nomFich', '$description', $catId, $userId, 'montre')";
          if($result = mysqli_query($mysqli,$query_insert_image)){
            $response = array(
              "type" => "success",
              "message" => "L'image a bien été téléchargé."
            );
          }
        }
        else { // retourne l'erreur en cas de problème de target, dossier inexistant par exemple
            $response = array(
                "type" => "error",
                "message" => "Problème durant le téléchargement de l'image."
            );
        }
    }
}
?>
<div id="form_add_photo" class="w3-modal">
  <div class="w3-modal-content w3-card-4 w3-animate-top" style="width:45%;">
    <header class="w3-container w3-blue w3-display-container"> 
      <form action=""><button onclick="document.getElementById('form_add_photo').style.display='none';" class="w3-button w3-blue w3-display-topright"><i class="fa fa-remove"></i></button></form>
      <h4> Formulaire d'ajout de photo <i class="fa fa-smile-o"></i> </h4>
    </header>
    
<form id="frm-image-upload" action="page_principale.php" name='img' method="post" enctype="multipart/form-data"> 
<div class="w3-row w3-section w3-margin-left">
  <div class="w3-col" style="width:50px"> <i class="w3-xxlarge fa fa-file" style="font-size:36px;color:lightblue;
  text-shadow:2px 2px 4px #000000"></i>
  </div>
  <a target="_blank" title="Veuillez ajouter une photo n'excédant pas la taille de 100 Ko, en format: JPEG, GIF ou PNG.">
    <i class='far fa-question-circle' style='font-size:24px'></i></a>
    <div class="w3-rest">
      <input type="file" class="file-input" accept="image/*" name="file-input" required>
    </div>
</div>

<div class="w3-row w3-section w3-margin-left">
  <div class="w3-col" style="width:50px"><i class='w3-xxlarge fas fa-comment-alt' style='font-size:24px;color:lightblue;text-shadow:2px 2px 4px #000000'></i></div>
    <div class="w3-rest">
      <textarea class="w3-input w3-border" name="description" type="text" placeholder="Description" required></textarea>
    </div>
</div>
<label for="catégorie" class="w3-text-teal w3-margin-left">Catégorie </label>
  <select name="categorie" required>
    <option value="choisir une catégorie" selected disabled> Choisir une catégorie</option>
    <option value="humour"> Animaux</option>
    <option value="informatique"> Informatique</option>
    <option value="jeux"> Jeux</option>
    <option value="paysage"> Paysage</option>
    <option value="paysage"> Personnage</option>
    <option value="autre"> Autre </option>
  </select>
    <br/>

<p class="w3-margin-left">
<input type="submit" id="btn-submit" name="upload" value="Upload" class="w3-button w3-blue">
</p>

<?php // test de récuperation des saisi du user
  if(isset($_POST['upload']))
  {
    echo "<script>document.getElementById('form_add_photo').style.display='block';</script>";
    echo $response['message'];
  }
  /*$filename = $_FILES['file-input']['name'];
  echo " le nom du fichier est : " .$filename. " <br/> " ;
  $description = $_POST['description'];
  echo "la description est : " .$description. " <br/> ";
  $categorie = $_POST['categorie'];
  echo "vous avez choisi la catégorie : " .$categorie . " <br/> " ;*/
?>

</form>

</div>
</div> <!-- affiche si l'ajout s'est bien effectué à l'exterieur de formulaire pas dans le modal  -->





<div id="profil" class="w3-modal">
  <div class="w3-modal-content w3-card-4 w3-animate-top" style="width:70%;">
    <header class="w3-container w3-teal w3-display-container"> 
      <span onclick="document.getElementById('profil').style.display='none'" class="w3-button w3-teal w3-display-topright"><i class="fa fa-remove"></i></span>
      <h4> Profil utilisateur <i class="fa fa-smile-o"></i> </h4>
    </header>
    <p>   Identifiant : <?php echo $_SESSION['id'];?></p>
    <p>   Profil : <?php echo $_SESSION['profil']; ?></p>
    <?php if($_SESSION['profil']=='Administrateur'){echo "<div class=\"w3-center\"><a class=\"w3-btn w3-blue\" href=\"stat.php\"> Option Administrateur </a></div>";} ?>
    <br>
    <p>   Liste des photos :</p>
    <?php
      $sid = $_SESSION['id'];
      $query="SELECT photo.photoId, photo.nomFich , photo.description , categorie.nomCat , photo.etat FROM photo JOIN categorie ON photo.catId=categorie.catId WHERE UserId IN (SELECT UserId FROM utilisateur WHERE Pseudo='$sid')";
      $data=execute_query($mysqli,$query);
      $count=0;
      while($count<count($data))
      {
        $d0=$data[$count][0];
        $d1=$data[$count][1];
        $d4=$data[$count][4];
        $display="<script>document.getElementById('modif_$count').style.display='block';</script>";
        $name_fich= 'fich_'.$count;
        $name_desc= 'desc_'.$count;
        $name_cat= 'cat_'.$count;
        $name_form= 'form_'.$count;      
        $name_close= 'close_'.$count;
        $name_etat= 'etat_'.$count;
        $fichier = 'assets/image/'.$d1;
        if(isset($_POST[$count])){
          $query_suppr="DELETE FROM photo WHERE nomFich='$d1' ";
          if($result=mysqli_query($mysqli,$query_suppr))
          {
            if( file_exists ($fichier))
            { unlink($fichier) ;}
          }
          echo "<script>document.getElementById('profil').style.display='block';</script>";
        }

        if(isset($_POST[$name_etat])){
          if($d4=='montre'){$etat='cache';}
          else{$etat='montre';}
          $query_update="UPDATE photo SET etat='$etat' where photoId='$d0'";
          if($result=mysqli_query($mysqli,$query_update)){
            echo "<script>document.getElementById('profil').style.display='block';</script>";
          }
        }

        if(isset($_POST[$name_fich]))
        {
          if($_POST[$name_fich]==""){$wrong1='wrong';echo $display;}
          else{
            $photo=explode(".",$_POST[$name_fich]);
            if(count($photo)>1){$wrong2='wrong';echo $display;}
            else{
              $ok1='ok';
            }
          }
        }

        if(isset($_POST[$name_desc]))
        {
          if($_POST[$name_desc]==""){$wrong1='wrong';echo $display;}
          else{
            $ok2='ok';
          }
        }

        if(isset($_POST[$name_cat]))
        {
          if($_POST[$name_cat]=="Choisir Catégorie"){$wrong1='wrong';echo $display;}
          else{
            $ok3='ok';
          }
        }

        if(((isset($wrong1))||(isset($wrong2)))&&(!isset($count_modif)))
        {
         $count_modif=$count;
        }
        
        if((isset($ok1))&&(isset($ok2))&&(isset($ok3)))
        {
          $f=explode(".",$d1);
          $ext=$f[1];
          $fich=addslashes($_POST[$name_fich]);
          $array_fich=array($fich,$ext);
          $str_fich=implode(".",$array_fich);
          $query_update="UPDATE photo SET nomFich='$str_fich' where photoId='$d0'";
          if($result=mysqli_query($mysqli,$query_update)){
            $rename_fichier='assets/image/'.$str_fich;
            rename($fichier,$rename_fichier);}
          
          $desc=$_POST[$name_desc];
          $desc=addslashes($_POST[$name_desc]);
          $query_update="UPDATE photo SET description='$desc' where photoId='$d0'";
          if($result=mysqli_query($mysqli,$query_update)){}
          
          $cat_choice=$_POST[$name_cat];
          $query_id="SELECT catId FROM categorie WHERE nomCat='$cat_choice'";
          $data_id=execute_query($mysqli,$query_id);
          $cat_id=$data_id[0][0];
          $query_update="UPDATE photo SET catId='$cat_id' where photoId='$d0'";
          if($result=mysqli_query($mysqli,$query_update)){}
          unset($ok1);unset($ok2);unset($ok3);
        }
        
        if((isset($_POST[$name_form])) || (isset($_POST[$name_close])))
        {
          echo "<script>document.getElementById('profil').style.display='block';</script>";
        }

        $count=$count+1;
      }
      $data=execute_query($mysqli,$query);
      if(count($data)>0)
      {$count=0;
      echo "<div class=\"w3-center\">   <table border=1 style=border-collapse: collapse;>";
      echo"<tr>
      <th> Nom du fichier </th>
      <th> Description  </th>
      <th> Categorie </th>
      <th> Etat </th>
      <th> Modifier </th>
      <th> Supprimer </th>
      </tr>";
      while($count<count($data))
      {
        $d1=$data[$count][1];
        $d2=$data[$count][2];
        $d3=$data[$count][3];
        $d4=$data[$count][4];
        $name_fich= 'fich_'.$count;
        $name_desc= 'desc_'.$count;
        $name_cat= 'cat_'.$count;
        $name_form= 'form_'.$count;
        $name_close= 'close_'.$count;
        $name_etat= 'etat_'.$count;
        echo"<tr>
              <td> <a onclick=\"document.getElementById('$d1').style.display='block';\" class=\"w3-button\">$d1</a> </td>
              <td> $d2 </td>
              <td> $d3 </td>
              <td> <form action=\"\" method=POST><button name=$name_etat type=submit class=\"w3-button\"> $d4 </button></form> </td>
              <td><a onclick=\"document.getElementById('modif_$count').style.display='block';\" class=\"w3-button\"> Modifier </a></td>
              <td> <form action=\"\" method=POST><button name=$count type=submit class=\"w3-button\">Supprimer </button></form></td>
            </tr>
            <div class=\"w3-modal\" id=$d1>
            <div class=\"w3-modal-content w3-card-4 w3-animate-top\" style=width:25%;>
            <header class=\"w3-container w3-teal w3-display-container\"> 
              <span onclick=document.getElementById('$d1').style.display='none' class=\"w3-button w3-teal w3-display-topright\"><i class=\"fa fa-remove\"></i></span>
              <h4> $d1 </h4>
            </header>
            <div class=\"w3-card-4\" style=\"overflow-x:auto;\">
              <div class=\"w3-container\">
                <hr>
                <img src=assets/image/$d1 alt=$count+1 class=\"w3-left w3-margin-right\" style=width:100%;>
              </div>
            </div>
            </div></div>
            
            <div class=\"w3-modal\" id=modif_$count>
            <div class=\"w3-modal-content w3-card-4 w3-animate-top\" style=width:50%;>
            <header class=\"w3-container w3-teal w3-display-container\"> 
              <form action=\"\" method=POST><button type=submit  name=$name_close class=\"w3-button w3-teal w3-display-topright\"><i class=\"fa fa-remove\"></i></button></form>
              <h4>Modification de $d1 </h4>
            </header>
            <div class=\"w3-card-4\" style=\"overflow-x:auto;\">
              <div class=\"w3-container\">
                <hr>
                <img src=assets/image/$d1 alt=$d1 class=\"w3-left w3-margin-right\" style=width:50%;>
                <form action=\"\" method=POST>
                <input type=\"text\" name=$name_fich placeholder=\"Nom de Fichier\" />";
                $fichier = explode(".",$d1);
                $extension = '.'.$fichier[1];
                echo $extension;
                if(isset($count_modif)){echo "<script>document.getElementById('modif_$count_modif').style.display='block';</script>";}
                echo "<br><br>
                <textarea maxlength=250 cols=40 rows=5 name=$name_desc placeholder=\"description\"></textarea>
                <br><br>
                <label for=$name_cat class=\"w3-text-teal\">Catégorie : </label>
                <select name=$name_cat id=$name_cat>
                  <option value=\"Choisir Catégorie\" selected> Choisir catégorie </option>";
                  $count_cat=0;
                  while($count_cat<count($cat))
                  {
                    $c=$cat[$count_cat][0];
                    echo "<option value=$c>$c</option>";
                    $count_cat=$count_cat+1;
                  }
                echo "</select>
                <br>";
                if(isset($wrong1)){echo "Des données n'ont pas été saisies";}
                elseif(isset($wrong2)){echo "Le nom de votre fichier ne doit pas contenir de '.'";}
                echo "<br>
                <button type=submit name=$name_form class=\"w3-btn w3-teal w3-center\"> Modifier </button>
                </form>
              </div>
              <br>
            </div>
            </div></div>";
        $count=$count+1;    
      }
      echo "</table>   </div><br>";}
      else{echo "Vous n'avez posté aucune photo sur le site";}
    ?>
</div>
</div>



<!-- Image Header -->
<div class="w3-display-container w3-animate-opacity">
  <img src="sailboat.jpg" alt="bateau" style="width:100%;min-height:350px;max-height:600px;">
  <div class="w3-container w3-display-bottomleft w3-margin-bottom">
    <p class="w3-panel w3-sand w3-round w3-xxlarge w3-opacity"> Mini Pinterest </p>
  </div>
</div>



<div id="id01" class="w3-modal">
  <div class="w3-modal-content w3-card-4 w3-animate-top" style="width:45%;">
    <header class="w3-container w3-teal w3-display-container"> 
    <form action="" method=POST><button  type=submit name="remove_modal" onclick="document.getElementById('id01').style.display='none'" class="w3-button w3-teal w3-display-topright"><i class="fa fa-remove"></i></button>
      <h4>Connexion pour modifier le catalogue <i class="fa fa-smile-o"></i> </h4>
    </header>
    <form  method="POST" class="w3-container" id="contact" action="page_principale.php">
      <p>
        <label for="Identifiant" class="w3-text-teal" >Identifiant</label>
        <input type="text" name="Identifiant" id="Identifiant" class="w3-input" placeholder="Votre Identifiant">
      </p>
    
      <p>
        <label for="pass" class="w3-text-teal">Mot de passe</label>
        <input type="password" name="pass" id="pass" class="w3-input" placeholder="Votre mot de passe"/> 
      </p>
      <p class=" w3-center">
      <button type="submit" class="w3-btn w3-teal" name="connecter" id="connect">Se connecter</button>
    </p>
    </form>
    <?php 
      if(isset($_SESSION['wrong_pass'])){echo " <div class=\"w3-center\">Identifiant ou mot de passe erroné</div>";if(!isset($_POST['remove_modal'])){echo " <script>  document.getElementById('id01').style.display='block'; </script>";}}
      if(isset($_SESSION['compte_bloquee'])){echo " <div class=\"w3-center\">Compte temporairement bloqué</div>";if(!isset($_POST['remove_modal'])){echo " <script>  document.getElementById('id01').style.display='block'; </script>";}}
    ?>
    <footer class="w3-container w3-teal w3-center">
      <button class="w3-button" onclick="document.getElementById('id02').style.display='block'; document.getElementById('id01').style.display='none'"> Créer un compte</button>
    </footer>
  </div>
</div>

<div id="id03" class="w3-modal">
<div class="w3-modal-content w3-card-4 w3-animate-top" style="width:45%;">
    <header class="w3-container w3-teal w3-display-container"> 
      <h4>Création de votre compte <i class="fa fa-smile-o"></i> </h4>
    </header>
    <p class=" w3-center">La création de votre compte est terminée</p>
    <form  method="POST" class="w3-container" id="contact" action="">
    <p class=" w3-center">
    <button type="submit" name='connect' class="w3-btn w3-teal" > Se connecter </button></p>
    <p class=" w3-center">
    <button type="submit" class="w3-btn w3-teal"> Retour à l'accueil </button>
    </p>
    </form>
    <?php if(isset($_POST['connect'])){echo "<script>document.getElementById('id01').style.display='block';</script>"; }
    ?>
</div>
</div>

<div id="id02" class="w3-modal">
<div class="w3-modal-content w3-card-4 w3-animate-top" style="width:45%;">
    <header class="w3-container w3-teal w3-display-container"> 
      <form action=""><button type=submit onclick="document.getElementById('id02').style.display='none'" class="w3-button w3-teal w3-display-topright"><i class="fa fa-remove"></i></button></form>
      <h4>Création de votre compte <i class="fa fa-smile-o"></i> </h4>
    </header>
    <form  method="POST" class="w3-container" id="contact" action="">
      <p>
        <label for="Identifiant" class="w3-text-teal" >Identifiant</label>
        <input type="text" name="Identifiant" id="Identifiant" class="w3-input" placeholder="Votre Identifiant">
      </p>
    
      <p>
        <label for="pass" class="w3-text-teal">Mot de passe</label>
        <input type="password" name="pass" id="pass" class="w3-input" placeholder="Votre mot de passe"/> 
      </p>
      <p>
        <label for="confirm_pass" class="w3-text-teal">Confirmer mot de passe</label>
        <input type="password" name="confirm_pass" id="confirm_pass" class="w3-input" placeholder="Votre mot de passe"/> 
      </p>
      <p class=" w3-center">
      <?php
        if((isset($_POST['Identifiant']))&&(isset($_POST['pass']))&&(isset($_POST['confirm_pass'])))
        {inscription($_POST['Identifiant'],$_POST['pass'],$_POST['confirm_pass'],$mysqli);}
        else{echo "Veuillez saisir toutes les données nécessaires pour votre inscription";}
      ?></p>
      <p class=" w3-center">
      <button type="submit" class="w3-btn w3-teal">S'inscrire</button>
    </p>
    </form>
    <footer class="w3-container w3-teal w3-center">
      <button class="w3-button" onclick="document.getElementById('id01').style.display='block'; document.getElementById('id02').style.display='none'">Déjà inscrit</button>
    </footer>
  </div>
</div>


<!-- Team Container -->
<div class="w3-container w3-padding-64 w3-center" id="team">
<h2> Toutes les photos </h2>

</br></br>
<p id=tmp_images> Catégorie de photos souhaité à afficher </p>
<form method="get" action="#team">
<span class="w3-text-teal w3-show-block">Categorie</span>
<?php
  $count=0;
  while($count<count($cat))
  {
    if($count%3==0){echo "<span class=\"w3-half\">";}
    $c=$cat[$count][0];
    echo "<span class=\"w3-show-block\">
    <input type=\"checkbox\" name=\"$c\" value=\"$c\" id=\"$c\" class=\"w3-check\"";
    if ( isset( $_GET[$c])) echo 'checked';
    echo "/><label for=\"$c\"> $c</label></span>";
    if(($count+1)%3==0){echo "</span>";}
    $count=$count+1;
  }
?>
<input type="submit" class="w3-button w3-circle w3-blue" value="Appliquer"/>
</form>

<div id="catalogue">
<?php 
  $data=select_categorie('Rien',$mysqli);
  $count=0;
  while($count<count($cat))
  {
    $c=$cat[$count][0];
    if(isset($_GET[$c]))
    {$data=add_categorie($c,$mysqli,$data);}
    $count=$count+1;
  }
  if(isset($_GET['Rien']))
  {

    $c=count($data);
    $data=select_categorie('Rien',$mysqli);
    echo "<br><br><br><br>";
    if($c==0)
    {echo "Bienvenue dans la base de données du vide intersidéral. Si vous être à la recherche de rien vous êtes au bon endroit.";}
    else
    {echo "Ne cherches pas à jouer le malin avec moi. Je sais pertinemment que je suis une machine mais sache que je ne suis pas un imbécile";}
  }
  photo_display($data);
?>
</div>

<footer>
</footer>

<script language="JavaScript">
// Script for side navigation
function w3_open() {
  var x = document.getElementById("mySidebar");
  x.style.width = "300px";
  x.style.paddingTop = "10%";
  x.style.display = "block";
}

// Close side navigation
function w3_close() {
  document.getElementById("mySidebar").style.display = "none";
}


</script>

</body>
</html>
