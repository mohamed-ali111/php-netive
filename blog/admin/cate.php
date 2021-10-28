
<?php
// this functiom to direct to any page without problem
ob_start();

session_start();
include("init.php");
include("includes/templets/navbar.php");





if(isset($_GET['page'])){
  $page = $_GET['page'];
}else{
  $page='All';
}

$statment = $connection-> prepare("SELECT * FROM catogeries");
$statment->execute();
$cateCount= $statment->rowCount();
$catogeres = $statment->fetchAll();
?>



<?php if($page == "All")
{ ?>
<div class="card ">
  

  
  <a href="?page=addcate" type="button" class="btn btn-secondary soso mt-5">Add new catogeries</a> 


  <div class="card-body">
    

  <div class="card-header ">
  Catogere Mangement <span class="badge badge-primary"><?php echo $cateCount;?></span>
  </div>


  <table class="table table-light table-hover table-striped table-bordered text-center">
  <thead>
    <tr>
      <th scope="col">ID</th>
      <th scope="col">Title</th>
      <th scope="col">Description</th>
      <th scope="col">Status</th>
      
      <th scope="col">operation</th>


    </tr>
  </thead>
  <tbody>
      <?php
      if($cateCount>0){
          foreach($catogeres as $cate){

          

      
      ?>
    <tr>
      <th scope="row"><?php echo $cate['id']?></th>
      <td><?php echo $cate['title']?></td>
      <td><?php echo $cate['description']?></td>
      <td><?php
               if($cate['status']== 0){
                 echo '<span class="badge bg-info">Pending</span>';
               }else{
                echo '<span class="badge bg-success">Approved</span>';

               }
              ?></td>
     
      <td>
          <a href="?page=showcate&userid=<?php echo $cate['id']?>" class="btn btn-primary">
          <i class="fas fa-eye"></i>
          </a>

          <a href='?page=delete&userid=<?php echo $cate['id']?>'class="btn btn-danger" >
          <i class="fas fa-trash"></i>
          </a>
      </td>

    </tr>
  <?php
      }
    }
  ?>
  </tbody>
</table>
  </div>
</div>






<?php }elseif($page=="savecate"){

  if($_SERVER['REQUEST_METHOD']=='POST'){
    if(isset($_POST['save-cate'])){
      $titleErr = $descriptionErr =  '';
  
      $title = $_POST['title'];
      $description =$_POST['description'];
     
      $status = $_POST['status'];
   
  
  
      if(!empty($title)){
        $title = filter_var($title , FILTER_SANITIZE_STRING);
      }else{
        $titleErr ="title is required";
      }
      if(!empty($description)){
        $description = filter_var($description , FILTER_SANITIZE_STRING);
      }else{
        $descriptionErr ="description is required";
      }
   
      // if(!empty($status)){
      //   $status  = filter_var($status , FILTER_SANITIZE_STRING);
      // }else{
      //   $statusErr ="status is required";
      // }
  
  
      if(empty($titleErr) && empty($descriptionErr)){
        $stmt = $connection->
        prepare('INSERT INTO catogeries(`title` , `description`, `status` , `created_at`)
        VALUES (:ztitle , :zdescription, :zstatus , now())
        ');
        $stmt->execute(array(
          'ztitle' => $title ,
          'zdescription' => $description ,
         
          'zstatus' =>  $status
        
        ));
  
        if($stmt -> rowCount() >0){
          echo "<div class='alert alert-success m-auto w-50'>cateogres has been Created successfully</div>";
        header("refresh:3;url=cate.php");
        exit();
        }
  
    }else {
      echo 'There are errors';
    }
  
  }
  
  }
}
     elseif($page=="update"){

  if($_SERVER['REQUEST_METHOD']=='POST'){

    $title = $_POST['title'];
    $description = $_POST['description'];
    $cateid = $_POST['cateid'];
    $status = $_POST['status'];
    

    $updatestatment = $connection->
     prepare("UPDATE catogeries SET `title`= ?,`description`= ?,`status`= ?,`updated_at`= now() WHERE id= ?;");
  
     $updatestatment->execute(array($title,$description,$status,$cateid));
    $updateRow =$updatestatment->rowCount();

    if($updateRow > 0){
      echo "<div class='alert alert-success m-auto w-50'>catogeries has been Updated successfully</div>";
        header("refresh:3;url=cate.php");
        exit();
    }

  }
}

elseif($page=="showcate"){

$userid='';
if(isset($_GET['userid'])&& is_numeric($_GET['userid'])) {
  $cateid= intval($_GET['userid']);
}else{
  $userid = '';
}

   $statment = $connection -> prepare("SELECT * FROM catogeries WHERE id = ?");
  $statment -> execute(array($cateid));
  $catecount = $statment -> rowCount();
  if($catecount > 0){
    $catesInfo = $statment ->fetch();
  }
?>


<!-- start html code  -->

  <div class="container-fluid card">

<h1 class="text-center  mt-2"> Edit catogires</h1>
    <div class="row">
      <div class="col-md-6 inall">


      <form  class="mt-3 mb-5 f_add" method="post" action="?page=update">
    <label>title</label>
    <input type="text" name="title" class="form-control" value="<?php echo  $catesInfo ['title'] ; ?>">
    <br>
    <label>description</label>
    <input type="text" name="description" class="form-control" value="<?php echo  $catesInfo ['description'] ; ?>" />
    <br>

      <label class="mr-2">Status</label>
        <input 
        <?php
            if($catesInfo['status']==='0'){
            echo 'checked';
          }else{
            echo '';
          }
          ?>
        type="radio" name="status" value="0"/>pending
        <input
        <?php
            if($catesInfo['status']==='1'){
            echo 'checked';
          }else{
            echo '';
          }
          ?>
        type="radio" name="status" value="1"/>Approved
        <br>
   
      <br>
      <input type="hidden" name="cateid" value="<?php echo $catesInfo['id'] ; ?>"/>

    <input type="submit" class="btn btn-danger" name="save-cate" value="Save" />
  </form>
      </div>
    </div>
  </div>


<!-- end html code  -->


<!-- <=================> -->
<!-- <=================> -->
<!-- <=================> -->

<?php
}

elseif($page=="addcate"){ ?>




<div class="card">
  <div class="inall">
  <h1>Add new cate</h1>



<form style="width:450px,margin-right:100px;" method="post" action="?page=savecate">
<div class="form-group">
  <label for="exampleInputdescription1">title</label>
  <input type="text" class="form-control" id="exampleInputdescription1" aria-describedby="descriptionHelp" name="title">
</div>


<div class="form-group">
  <label for="exampleInputdescription1">description</label>
  <input type="description" class="form-control" id="exampleInputdescription1" aria-describedby="descriptionHelp" name="description">
</div>



<br>
<br>

      <label class="mr-2">Status</label>
        <input type="radio" name="status" value="0">Pending
        <input type="radio" name="status" value="1">Approved
        <br>
<input type="submit" class="btn btn-primary" name="save-cate" value="Save" >
</form> 

</div>

</div>


 <!-- // <=============================> -->
 <!-- // <=============================> --> 
<!-- // <=============================> --> 
 <!-- // <=============================> --> 
<?php
}
elseif($page=="delete")
{


$userid='';
if(isset($_GET['userid'])&& is_numeric($_GET['userid'])) {
  $userid= intval($_GET['userid']);
}else{
  echo"no data";
}

 
$check = $connection->prepare("SELECT* FROM  catogeries where id=?");
$check->execute(array($userid));
$rows=$check->rowCount();

if($rows > 0 ){
$delstatment = $connection->prepare('DELETE FROM catogeries where id=?');
$delstatment->execute(array($userid));
$delrows=$delstatment->rowCount();

if($delrows > 0) {
  echo "<div class='alert alert-danger m-auto w-50'>catogeries has been deleted successfully</div>";

  header('refresh:3;url=cate.php');
  exit();
}

}

}


?>





<?php
     include("includes/templets/footer.php");
?>