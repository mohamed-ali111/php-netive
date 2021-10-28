

<?php 
session_start();
include("init.php");
if(isset($_SESSION['Admin'])){ //هنا الكود ده ممتد للاخر وظيفته لايظهر الصفحه الا اذا عملت تسجيل دخوووول 



include("includes/templets/navbar.php");

$q1=$connection->prepare('SELECT * FROM users');
$q1->execute();
$tttCount=$q1->rowCount();


$q2=$connection->prepare('SELECT * FROM catogeries');
$q2->execute();
$catCount=$q2->rowCount();


$q3=$connection->prepare('SELECT * FROM posts');
$q3->execute();
$postCount=$q3->rowCount();


$q4=$connection->prepare('SELECT * FROM comments');
$q4->execute();
$commentCount=$q4->rowCount();

?>

<div class="static card">
    <div class="container">
        <div class="row">
            <div class="col-md-12 dashbord">
               <h1>local page that private to admin</h1>  
            </div>
           
            <div class="col-md-3">
                <div class="box">
                <i class="fas fa-users"></i>
                <h4>Users</h4>
                    <span><?php echo $tttCount;?></span>
                    <br>
                    <a href="user.php" class="btn btn-primary">show</a>
                </div>
            </div>


            <div class="col-md-3">
                <div class="box">
                <i class="fas fa-shapes"></i>
                    <h4>Catogeres</h4>
                    <span><?php echo $catCount;?></span>
                    <br>
                    <a href="cate.php" class="btn btn-danger">show</a>
                </div>
            </div>


            <div class="col-md-3">
                <div class="box">
                <i class="fas fa-address-card"></i>
                <h4>Posts</h4>
                    <span><?php echo $postCount;?></span>
                    <br>
                    <a href="posts.php" class="btn btn-warning">show</a>
                </div>
            </div>


            <div class="col-md-3">
                <div class="box">
                <i class="fas fa-comments"></i>
                <h4>Comments</h4>
                    <span><?php echo $commentCount;?></span>
                    <br>
                    <a href="comment.php" class="btn btn-success">show</a>
                </div>
            </div>
        </div>
    </div>
</div>








    <?php

     include("includes/templets/footer.php");

}else{
    echo "<div class='alert alert-primary m-auto w-50' role='alert'> you sre not authoniqated</div>";
   
    header('refresh:3;url=login.php');
    exit();
    
}

?>