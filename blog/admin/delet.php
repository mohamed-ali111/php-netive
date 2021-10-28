<?php


if ($page=='delete'){


    $userid=1;
    if(isset($_GET['userid'])){
        $userid=$_GET['userid'];
    }else{
        $userid=1;
    }
 
 
     
   $statment = $connection->prepare("DELETE FROM  customers1 where CustomerID=?");
 $statment->execute(array($userid));
 
     header('Location:index.php');
 
 
 }

 $statment = $connection-> prepare("SELECT p. * FROM (( posts p 
 INNER JOIN users u ON p.user_id = u.id)
 INNER JOIN catogeries c ON p.catogery_id =c.id);");
 $statment->execute(array($userid));
 $postCount= $statment->rowCount();
 if($postCount > 0){
   $postInfo = $statment ->fetch();
 }

?>