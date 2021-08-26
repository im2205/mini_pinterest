<?php
  session_start(); 
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
?>

<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src='https://kit.fontawesome.com/a076d05399.js'></script> <!-- pour les w3css icons de type: fa fa ou far fa -->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script> <!-- pour les pie chart/camembert -->

<script type="text/javascript">
  google.charts.load('current', {'packages':['corechart']});
  google.charts.setOnLoadCallback(drawChart);

  function drawChart() {

    var data = google.visualization.arrayToDataTable([
      ['Profil', 'COUNT(Pseudo)'],
          
      <?php
        $query = "SELECT Profil, COUNT(Pseudo) from utilisateur GROUP BY Profil";
        //$res = execute_query($mysqli, $query);
        $res = mysqli_query($mysqli,$query);
        while($row= mysqli_fetch_assoc($res))
        {
          echo "['".$row['Profil']."',".$row['COUNT(Pseudo)']."],";
        }
      ?>
      ]);
      var options = {
        title: 'Nombre de personnes par profil',
      };

        var chart = new google.visualization.PieChart(document.getElementById('piechart_profil'));

        chart.draw(data, options);
      }
    </script>

</head>
<body id="acceuil">

<a class="w3-btn w3-blue w3-margin-left w3-margin-top" href="page_principale.php"> Retour vers la page d'acceuil </a>
<!--    AJOUTER DES STATISTIQUES ICI    -->

    <h5 class="w3-center"> Nombre total d'utilisateurs </h5>
  <div class="w3-row w3-border">
    <div class="w3-container w3-half">
    <?php 
      $requete = "SELECT Pseudo, Profil, connecte FROM utilisateur";
      $users = execute_query($mysqli, $requete);
      if (count($users)>0){
        $nombre = 0;
        echo "<br/> <br/><br/><br/>
        <table class=\"w3-margin-top w3-margin-left\" border=1 style=border-collapse: collapse;>
          <tr> 
            <th> Pseudo </th>
            <th> Profil </th>
            <th> Etat </th>
          </tr>";
        while($nombre<count($users)){ // mieux en tableau si user + grand
          echo "<tr>
          <td>". $users[$nombre][0]." </td>";
    ?>
    <td>
    <?php 
      if($users[$nombre][1]=='Administrateur'){
        echo "<p class=\"w3-blue\">" .$users[$nombre][1]. "</p>";
      }
      if($users[$nombre][1]=='Utilisateur'){ 
        echo "<p class=\"w3-red\">" .$users[$nombre][1]. " </p>";}
    ?>
    </td>
    <td>
    <?php
      if($users[$nombre][2]==0){
        echo "<p> Hors ligne </p>";
      }
      if ($users[$nombre][2]==1) {
        echo "<p> En ligne </p>";
      }
      $nombre +=1; 
    ?>
    </td>
    </tr>
    <?php
    }
      echo" 
        </table>";
      }
    ?>
    </div>
    
    <div class="w3-container w3-half">
      <div id="piechart_profil" style="width: 500px; height:400px;"> pie chart des profils </div>
    </div>
  </div>  
    <h5 class="w3-center"> Statistiques d'images </h5>
    <div class="w3-row w3-border">
      <div class="w3-container w3-half">
    <?php  // récupère le nombre de photos de chaque catégorie, tableau 2d: |categorie|nombre_de_photo|
      $query = " SELECT categorie.nomCat, COUNT(photo.nomFich) FROM categorie JOIN photo ON categorie.catId=photo.catId GROUP BY categorie.nomCat ";
      $res = execute_query($mysqli,$query);
      if(count($res)>0){
        $number = 0;
        echo " <br/> <br/> <br/> <br/>
        <div class=\"w3-center\">   
        <table class=\"w3-margin-top w3-margin-left\" border=1 style=border-collapse: collapse;>";
      echo"<tr>
      <th> Catégorie </th>
      <th> Nombre de Photos </th>
      </tr>";
      while($number<count($res))
      {
        $d1=$res[$number][0];
        $d2=$res[$number][1];
        echo"<tr>
            <td>" .$d1. "</td>
            <td>" .$d2. "</td>
            </tr>";
        $number=$number+1;    
      }
      echo "</table>   </div><br/>";
      }
    ?>
  </div>
  <div class="w3-container w3-half">

<script type="text/javascript">
  google.charts.load('current', {'packages':['corechart']});
  google.charts.setOnLoadCallback(drawChart);

  function drawChart() {

    var data = google.visualization.arrayToDataTable([
      ['Profil', 'COUNT(Pseudo)'],
          
      <?php
        $query = " SELECT categorie.nomCat, COUNT(photo.nomFich) FROM categorie JOIN photo ON categorie.catId=photo.catId GROUP BY categorie.nomCat ";
        $res = mysqli_query($mysqli,$query);
        while($row= mysqli_fetch_assoc($res))
        {
          echo "['".$row['nomCat']."',".$row['COUNT(photo.nomFich)']."],";
        }
      ?>
      ]);
      var options = {
        title: 'Nombre de photos par catégorie',
        is3D:true,
      };

        var chart = new google.visualization.PieChart(document.getElementById('piechart_photo'));

        chart.draw(data, options);
      }
    </script>
      <div id="piechart_photo" style="width: 700px; height:400px;"> pie chart des profils </div>

  </div>
</div>

<h5 class="w3-margin-left w3-margin-top"> Supprimer le compte d'un utilisateur </h5>

<?php // supprimer le compte du user
  if(isset($_GET['deleteId'])){
  $deleteId = $_GET['deleteId'];
  $query_delete_account = "SELECT Pseudo from utilisateur where utilisateur.UserId=$deleteId";
  $result_Pseudo = mysqli_query($mysqli,$query_delete_account);
  //echo $deleteId;
  $supprime_photos = " DELETE FROM photo WHERE photo.UserId=$deleteId ";
  if(mysqli_query($mysqli,$supprime_photos)){ echo " Photos et " ;}
  $supprime_compte = " DELETE FROM utilisateur WHERE utilisateur.UserId=$deleteId ";
  //AND utilisateur.Pseudo='$users[$nombre][2]'
  if(mysqli_query($mysqli,$supprime_compte)) { echo "compte de l'utilisateur supprimée avec succès";}
  }
?>
<?php // bloquer le compte du user
  if(isset($_GET['blockId'])){
    $blockId = $_GET['blockId'];
    $query_block = " UPDATE utilisateur SET active='non' WHERE utilisateur.UserId=$blockId ";
    if(mysqli_query($mysqli,$query_block)) { echo " le compte de l'utilisateur est bloqué ";}
  }
?>
<?php // débloquer le compte du user
  if(isset($_GET['deblockId'])){
    $deblockId = $_GET['deblockId'];
    $query_deblock = " UPDATE utilisateur SET active='oui' WHERE utilisateur.UserId=$deblockId ";
    if(mysqli_query($mysqli,$query_deblock)) { echo " le compte de l'utilisateur est activé ";}
  }
?>
<?php // donnée à afficher dans la tableau 
  $query = "SELECT UserId, Pseudo, Profil, active from utilisateur where Profil='Utilisateur' ";
  $users = mysqli_query($mysqli, $query);
  $count = mysqli_num_rows($users);
  if ($count>0){ 
?>
  <table class="w3-margin-top w3-margin-left w3-margin-bottom" border=1 style=border-collapse: collapse;>
    <tr>
      <th> nombre </th>
      <th> User ID </th>
      <th> Pseudo </th>
      <th> Profil </th>
      <th> Status </th>
      <th> Action </th>
    </tr>
<?php
  $i = 0;
  while($row = mysqli_fetch_array($users)){
    $i++;
?>
<tr align="center">
    <td> <?php echo $i; ?> </td>
    <td> <?php echo $row['UserId']; ?> </td> 
    <td> <?php echo $row['Pseudo']; ?> </td>
    <td> <?php echo $row['Profil']; ?> </td>
    <td> <?php if($row['active']=='oui') echo " Activé "; else{ echo " Désactivé "; } ?> </td>
    <td>
      <?php 
        if($row['active']=='non'){
          echo "<a class=\"w3-margin-right w3-btn w3-purple\" href=\"stat.php?deblockId=" .$row['UserId']."\" onclick=\"return confirm('Voulez vous activer le compte de l\'utilisateur ? il pourra à nouveau se connecter à son compte');\"> Activer</a>";
        }
        elseif($row['active']=='oui'){
          echo "<a class=\"w3-margin-right w3-btn w3-orange\" href=\"stat.php?blockId=" .$row['UserId']."\" onclick=\"return confirm('Voulez vous bloquer le compte de l\'utilisateur ? il pourra plus se connecter à son compte');\">
            Bloquer </a>";
        } 
      ?> 
     <!--  <a class="w3-margin-right w3-btn w3-orange" href="stat.php?blockId=<?php echo $row['UserId']; ?>" 
        onclick="
        <?php 
          // if($row['activé']=='non'){
          //   echo "<script> return confirm('Voulez vous activer le compte de l\'utilisateur ? il pourra à nouveau se connecter à son compte'); </script>";
          // }
          // else{
          //   echo "<script> return confirm('Voulez vous bloque le compte de l\'utilisateur ?'); </script>";
          // }
        ?>"> -->
       <a class="w3-red w3-btn" href="stat.php?deleteId=<?php echo $row['UserId']; ?>" onclick="return confirm('Voulez vous supprimer définitivement ce compte ?');" > Supprimer </a>
    </td>
  </tr>

<?php 
  }
  echo "</table>";
  }
  if ($count=0){
    echo "<h3> Plus aucun utilisateur sur le site </h3>";
  }
?>

<hr>
<hr>
<h5 class="w3-margin-left w3-margin-top"> Modifications des photos </h5>
  <div class="w3-container w3-threequarter">

<?php // cacher une image
  if(isset($_GET['invisibleId'])){
    $invisible_Id = $_GET['invisibleId'];
    $query_block = "UPDATE photo set etat='cache' where photo.photoId=$invisible_Id ";
    if(mysqli_query($mysqli,$query_block)) { echo " la photo de l'utilisateur n'est plus visible ";}
  }
  // rendre visible une image
  if(isset($_GET['visibleId'])){
    $visible_Id = $_GET['visibleId'];
    $query_block = "UPDATE photo set etat='montre' where photo.photoId=$visible_Id ";
    if(mysqli_query($mysqli,$query_block)) { echo " la photo est maintenant visible par les utilisateurs ";}
  }
?>  


<?php 

  $query="SELECT photo.photoId, categorie.nomCat ,utilisateur.Pseudo, photo.nomFich, photo.description, photo.etat FROM photo JOIN categorie on photo.catId=categorie.catId join utilisateur ON photo.UserId=utilisateur.UserId WHERE utilisateur.Profil='Utilisateur'  ";
  $data=execute_query($mysqli,$query);
  $count=0;
  while($count<count($data)){
    $d0=$data[$count][0]; // id
    $d4=$data[$count][4]; // etat
    $d2 = $data[$count][3]; // nom fichier
    $d1 = $data[$count][2]; // pseudo
    $name_etat= 'etat_'.$count;
    $name_desc= 'desc_'.$count;
    $name_cat= 'cat_'.$count;
    $name_form= 'form_'.$count;
    $fichier = 'assets/image/'.$d1;
    if(isset($_POST[$count])){
      $query_suppr="DELETE FROM photo WHERE nomFich='$d2' ";
      if($result=mysqli_query($mysqli,$query_suppr))
      {
        if( file_exists ($fichier)){unlink($fichier);}
      }
    }
    if(isset($_POST[$name_form])){
    $desc=$_POST[$name_desc];
    echo $desc;
    $query_update="UPDATE photo SET description='$desc' where photoId='$d0'";
    if($result=mysqli_query($mysqli,$query_update)){}      
    $cat_choice=$_POST[$name_cat];
  echo $cat_choice;
    $query_id="SELECT catId FROM categorie WHERE nomCat='$cat_choice'";
    $data_id=execute_query($mysqli,$query_id);
    $cat_id=$data_id[0][0];
    $query_update="UPDATE photo SET catId='$cat_id' where photoId='$d0'";
    if($result=mysqli_query($mysqli,$query_update)){}
    }
    $count = $count +1;
  }
   $data=execute_query($mysqli,$query);
      if(count($data)>0)
      {$count=0;
      echo "<div class=\"w3-center\">   <table border=1 style=border-collapse: collapse;>";
      echo"<tr>
      <th> Pseudo </th>
      <th> Nom du fichier </th>
      <th> Description  </th>
      <th> Categorie </th>
      <th> Etat </th>
      <th> Modifier </th>
      <th> Supprimer </th>
      </tr>";
      while($count<count($data))
      {
        $d0=$data[$count][0]; // id
        $d1 = $data[$count][2]; // pseudo
        $d2 = $data[$count][3]; // nom fichier
        $d3 = $data[$count][4]; // description
        $d4 = $data[$count][1]; // nom categorie
        $d5 = $data[$count][5]; // etat
        $name_fich= 'fich_'.$count;
        $name_desc= 'desc_'.$count;
        $name_cat= 'cat_'.$count;
        $name_form= 'form_'.$count;
        $name_close= 'close_'.$count;
        $name_etat= 'etat_'.$count;
        echo"<tr>
              <td> $d1 </td>
              <td> <a onclick=\"document.getElementById('$d2').style.display='block';\" class=\"w3-button\">$d2</a> </td>
              <td> $d3 </td>
              <td> $d4 </td>
              <td>"; 
                if ($d5=='montre'){
                  echo " Visible <br/>
                  <a class=\"w3-margin-right w3-btn w3-orange\" href=\"stat.php?invisibleId=" .$d0."\"> Cacher</a>";
                } 
                elseif($d5=='cache'){
                  echo " Caché <br/>
                  <a class=\"w3-margin-right w3-btn w3-purple\" href=\"stat.php?visibleId=" .$d0."\">Afficher </a>
                  ";
                } 
              echo "</td>
              <td><a onclick=\"document.getElementById('modif_$count').style.display='block';\" class=\"w3-button w3-grey\"> Modifier </a></td>
              <td> <form action=\"\" method=POST><button onclick=\"return confirm('Voulez vous supprimer définitivement la photos ?');\" name=$count type=submit class=\"w3-button w3-red w3-margin-top\">Supprimer </button></form></td>
            </tr>
            <div class=\"w3-modal\" id=$d2>
            <div class=\"w3-modal-content w3-card-4 w3-animate-top\" style=width:35%;>
            <header class=\"w3-container w3-blue w3-display-container\"> 
              <span onclick=document.getElementById('$d2').style.display='none' class=\"w3-button w3-blue w3-display-topright\"><i class=\"fa fa-remove\"></i></span>
              <h4> $d2 </h4>
            </header>
            <div class=\"w3-card-4\" style=\"overflow-x:auto;\">
              <div class=\"w3-container\">
                <hr>
                <img src=assets/image/$d2 alt=$count+1 class=\"w3-left w3-margin-right\" style=width:100%;>
              </div>
            </div>
            </div></div>
            
            <div class=\"w3-modal\" id=modif_$count>
            <div class=\"w3-modal-content w3-card-4 w3-animate-top\" style=width:50%;>
            <header class=\"w3-container w3-blue w3-display-container\"> 
              <form action=\"\" method=POST><span onclick=\"document.getElementById('modif_$count').style.display='none'\" class=\"w3-button w3-blue w3-display-topright\"><i class=\"fa fa-remove\"></i> </span></form>
              <h4>Modification de $d2 </h4>
            </header>
            <div class=\"w3-card-4\" style=\"overflow-x:auto;\">
              <div class=\"w3-container\">
                <hr>
                <img src=assets/image/$d2 alt=$d2 class=\"w3-left w3-margin-right\" style=width:50%;>
                <form action=\"\" method=POST>";
                if(isset($count_modif)){echo "<script>document.getElementById('modif_$count_modif').style.display='block';</script>";}
                echo "<br><br>
                <textarea maxlength=250 cols=40 rows=5 name=$name_desc placeholder=\"description\" required></textarea>
                <br><br>
                <label for=$name_cat class=\"w3-text-blue\">Catégorie : </label>
                <select name=$name_cat id=$name_cat required>
                  <option value=\"Choisir Catégorie\" selected disabled> Choisir catégorie </option>";
                  $count_cat=0;
                  $cat=execute_query($mysqli,"SELECT nomCat FROM categorie");
                  while($count_cat<count($cat))
                  {
                    $c=$cat[$count_cat][0];
                    echo "<option value=$c>$c</option>";
                    $count_cat=$count_cat+1;
                  }
                echo "</select>
                <br> <br>
                <button type=submit name=$name_form class=\"w3-btn w3-blue w3-center\"> Modifier </button>
                </form>
              </div>
              <br>
            </div>
            </div></div>";
        $count=$count+1;    
      }
      echo "</table>   </div><br>";}
  ?>

</div> 



<div class="w3-container w3-quarter">
<footer class="w3-center">
  <br/> <br/> <br/><br> <br> <br>
  <a title="Retour à l'acceuil" class="w3-btn w3-blue " href="#acceuil"><i class='far fa-arrow-alt-circle-up' style='font-size:24px;color:blue'></i></a>
</footer>
</div>

</body>
</html>