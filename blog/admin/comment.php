
<?php
ob_start();

session_start();
include("init.php");
include("includes/templets/navbar.php");




if(isset($_GET['page'])){
  $page = $_GET['page'];
}else{
  $page='All';
}


$statment = $connection-> prepare("SELECT c. * FROM (( comments c
INNER JOIN users u ON c.user_id=u.id)
INNER JOIN posts p ON c.post_id=p.id);");
$statment->execute();
$commentCount= $statment->rowCount();
$comments = $statment->fetchAll();
?>



<?php if($page == "All")
{ ?>

<div class="card ">
 
  <a href="?page=addcomment" type="button" class="btn btn-secondary soso mt-5">Add new comment</a> 

  <div class="card-body"> 
    <div class="card-header ">
  Comment Mangement <span class="badge badge-primary"><?php echo $commentCount;?></span>
  </div>
  <table class="table table-light table-hover table-striped table-bordered text-center">
  <thead>
    <tr>
      <th scope="col">ID</th>
      <th scope="col">Comment</th>
     
      <th scope="col">Status</th>
       <th scope="col">User_id</th>
      <th scope="col">Post_id</th>
     

      <th scope="col">operation</th>


    </tr>
  </thead>
  <tbody>
      <?php
      if($commentCount>0){
          foreach($comments as $comment){

          

      
      ?>
    <tr>
      <th scope="row"><?php echo $comment['id']?></th>
      
      <td><?php echo $comment['comment']?></td>
      <td>
        <?php 
         if($comment['status'] == 0)
         {
           echo '<span class="badge bg-danger">Hidden</span>';
         }else{
          echo '<span class="badge bg-info">Visible</span>';
         }
        ?>
      </td>
      <td><?php echo $comment['user_id']?></td>
      <td><?php echo $comment['post_id']?></td>
      

     
      <td>
          <a href="?page=showcomment&commentid=<?php echo $comment['id']?>" class="btn btn-primary">
          <i class="fas fa-eye"></i>
          </a>

          <a href='comment.php?page=delete&commentid=<?php echo $comment['id']?>' class="btn btn-danger">
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


<?php
}elseif($page=='addcomment'){
  ?>

<div class="card">
  <div class="inall">
    <h1 class="text-center">Add New comment</h1>
    <div class="row">
      
      <form action="?page=savecomment" class="mt-3 mb-5 f_add " method="post" enctype="multipart/form-data">
      <label>Title</label>
      <input type="text" name="title" class="form-control" placeholder="Enter the title..  "/>
      <br>
      <label>comment</label>
      <textarea name="comment"  cols="5" rows="2" class="form-control" placeholder="Enter your comment..  "></textarea>
      <br>
      <!-- <label>Post Image </label>
        <input type="file" name="postImage" class="form-control"  />
        <br> -->
        <label class="mr-2">Status</label>
        <input type="radio" name="status" value="0" class="radio">pending
        <input type="radio" name="status" value="1">Approved
        <br>

        <!-- the part of catogery   -->
        <label>post</label>
        <select name="post_id" class="form-control">
         <option readonly>--Choose post--</option>
         <?php
         $postcomment=$connection->prepare('SELECT * FROM posts');
         $postcomment->execute();
         $allpostcomment=$postcomment->fetchAll();
         foreach($allpostcomment as $post){
         echo '<option value="'.$post['id'].'">'.$post['title'].'</option>';
         }
         
         ?>
         
        </select><br>
        <!-- the part of  user  -->
        <label>Publisher</label>
        <select name="user_id" class="form-control">
         <option readonly>--Choose user--</option>
         <?php
         $postUser=$connection->prepare('SELECT * FROM users');
         $postUser->execute();
         $allUsers=$postUser->fetchAll();
         foreach($allUsers as $user){
         echo '<option value="'.$user['id'].'">'.$user['username'].'</option>';
         }
         
         ?>
         
        </select><br>
      <input type="submit" class="btn btn-danger" name="save-comment" value="Save" />
    </form>
        
      </div>
    </div>
    
    </div>
<?php
}elseif($page == 'savecomment'){
  if($_SERVER['REQUEST_METHOD']=='POST'){
    if(isset($_POST['save-comment'])){
     $commentErr = $useridErr = $postidErr = '';
      $commeent = $_POST['comment'];
      $status = $_POST['status'];
      $user_id =$_POST['user_id'];
      $post_id =$_POST['post_id'];

      
      if(!empty($commeent)){
        $commeent = filter_var($commeent , FILTER_SANITIZE_STRING);
      }else{
        $commentErr ="Comment is required";
      }
      if(!empty($user_id)){
        $user_id = $user_id ;
      }else{
        $useridErr ="UserId is required";
      }
      if(!empty($post_id)){
        $post_id = $post_id ;
      }else{
        $postidErr ="PostId is required";
      }

      if( empty($commentErr) && empty($useridErr) && empty($postidErr)){
        $stmt = $connection->
        prepare('INSERT INTO comments( `comment` , `status` ,`user_id`,`post_id`, `created_at`)
        VALUES (:zcomment ,:zstatus , :zuserid ,:zpostid , now())
        ');
        $stmt->execute(array(
          'zcomment' => $commeent ,
          'zstatus' => $status,
          'zuserid' => $user_id ,
          'zpostid' => $post_id
        ));

        if($stmt -> rowCount() >0){
          echo "<div class='alert alert-success m-auto w-50'>Comment has been Created successfully</div>";
        header("refresh:3;url=comment.php");
        exit();
        }
      }else {
        echo 'There are errors';
      }


      
    }
  }
}
// <=============================>
// <=============================>
// <=============================>
// <=============================>
elseif($page=='update'){
  if($_SERVER['REQUEST_METHOD']=='POST'){
    $comment = $_POST['comment'];
    $commentid = $_POST['commentid'];
    $status = $_POST['status'];
    $user_id = $_POST['user_id'];
    $post_id = $_POST['post_id'];
    $updateStmt = $connection->
    prepare('UPDATE comments SET `comment`= ?,`status`= ?,`user_id`= ?,`post_id`= ? ,`updated_at`= now() WHERE id= ?');
    $updateStmt ->execute(array($comment ,$status ,$user_id,$post_id , $commentid ));
    $updateRow =$updateStmt->rowCount();
    if($updateRow > 0){
      echo "<div class='alert alert-success m-auto w-50'>Comment has been Updated successfully</div>";
        header("refresh:3;url=comment.php");
        exit();
    }
  }
}
elseif($page =='showcomment'){


  $commentid='';
  if(isset($_GET['commentid'])&& is_numeric($_GET['commentid'])) {
    $commentid= intval($_GET['commentid']);
  }else{
    $commentid = '';
  }


$statments = $connection -> prepare("SELECT * FROM comments WHERE id = ? ");
$statments -> execute(array($commentid));
$commentcount = $statments -> rowCount();
if($commentcount > 0){
  $commentinfo = $statments ->fetch();
}
?>

<!-- start html code  -->

<div class="container-fluid card">

<h1 class="text-center  mt-2"> Edit comments</h1>
  <div class="container">
    <div class="row">
      <div class="inall">


      <form  class="mt-3 mb-5 f_add" method="post" action="?page=update">
      <label>Comment</label>
    <textarea name="comment" id="" cols="5" rows="3" class="form-control"><?php echo $commentinfo['comment'] ; ?></textarea>
   
 
      <label class="mr-2">Status</label>
        <input 
          <?php
            if($commentinfo['status']=='0'){
            echo 'checked';
          }else{
            echo '';
          }
          ?>
        type="radio" name="status" value="0">pending
        <input
        <?php
            if($commentinfo['status']=='1'){
            echo 'checked';
          }else{
            echo '';
          }
          ?>
        type="radio" name="status" value="1">Approved
        <br>



           
     <label>Publisher</label>
        <select name="user_id" class="form-control">
         <option readonly>--Choose user--</option>
         <?php
         $commentuser=$connection->prepare('SELECT * FROM users');
         $commentuser->execute();
         $allUsers=$commentuser->fetchAll();
         foreach($allUsers as $user){
          echo '<option value="'.$user['id'].'">'.$user['username'].'</option>';
        }
         
         ?>

  
</select><br>
        <label>Post Title</label>
        <select name="post_id" class="form-control">
         <option readonly>--Choose post--</option>
         <?php

         $commentpost=$connection->prepare('SELECT * FROM posts');
         $commentpost->execute();
         $allposts=$commentpost->fetchAll();
         foreach($allposts as $postss){
         echo '<option value="'.$postss['id'].'"'. '>'.$postss['title'].'</option>';
         }
         
         
        
    ?> 
    </select>
     <br>
     <input type="hidden" name="commentid" value="<?php echo $commentinfo['id'] ; ?>"/>

    <input type="submit" class="btn btn-danger" name="save-comment" value="Save" />
  </form>
      </div>
    </div>
  </div>
        </div>

<!-- end html code  -->
<?php
}

elseif($page =="delete")
{

$commentid='';
if(isset($_GET['commentid'])&& is_numeric($_GET['commentid'])) {
$commentid= intval($_GET['commentid']);
}else{
echo"no data";
}


$checks = $connection->prepare("SELECT* FROM comments WHERE id=?");
$checks->execute(array($commentid));
$rowss=$checks->rowCount();

if($rowss > 0 ){
$delstatments = $connection->prepare('DELETE FROM comments WHERE id=?');
$delstatments->execute(array($commentid));
$delrowss=$delstatments->rowCount();

if($delrowss > 0) {
echo "<div class='alert alert-danger m-auto w-50'>comments has been deleted successfully</div>";

header('refresh:3;url=comment.php');
exit();
}

}


}
?>








<?php
     include("includes/templets/footer.php");
?>

