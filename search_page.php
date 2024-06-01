<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
};


if (isset($_POST['add_to_cart'])) {

   $product_name = $_POST['product_name'];
   $product_price = $_POST['product_price'];
   $product_image = $_POST['product_image'];
   $product_quantity = $_POST['product_quantity'];

   if($user_id != null){
      $check_cart_numbers = mysqli_query($conn, "SELECT * FROM cart WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');
      $select_product = mysqli_query($conn, "SELECT * FROM products WHERE name = '$product_name'") or die('query failed');
      $row = mysqli_fetch_assoc($select_product);
      $available_quantity =  $row['quantity'];

      if (mysqli_num_rows($check_cart_numbers) > 0) {
         $message[] = 'Already added to cart! You can update the quantity in the cart.';
      }else if($available_quantity==0){
         $message[] = '0ut of stock!';
      }else if($available_quantity<$product_quantity){
         $message[] = 'Ordered quantity is not available in the stock!';
      }else {
         mysqli_query($conn, "INSERT INTO cart(user_id, name, price, quantity, image) VALUES('$user_id', '$product_name', '$product_price', '$product_quantity', '$product_image')") or die('query failed');
         $message[] = 'Product added to cart!';
      }
   }else{
      $message[] = 'Please login to XOXO!';
   }
   
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Search page</title>
   <link rel="icon" type="image/x-icon" href="images/XOXO.png">


   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/user_style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<div class="heading">
   <h3>Search Page</h3>
   <p> <a href="home.php">Home</a> / Search </p>
</div>

<section class="search-form">
   <form action="" method="post">
      <input type="text" name="search" placeholder="search products..." class="box">
      <input type="submit" name="submit" value="search" class="btn">
   </form>
</section>

<section class="products" style="padding-top: 0;">

   <div class="box-container">
   <?php
      if(isset($_POST['submit'])){
         $search_item = $_POST['search'];
         if($search_item == null){
            echo '<p class="empty">search something!</p>';
         }else{
            $select_products = mysqli_query($conn, "SELECT * FROM `products` WHERE name LIKE '%{$search_item}%'") or die('query failed');
            if(mysqli_num_rows($select_products) > 0){
            while($fetch_product = mysqli_fetch_assoc($select_products)){
            ?>
            <form action="" method="post" class="box">
            
               <a href="details.php?id=<?php echo $fetch_product['id']; ?>">

               <img style="width:20rem;" src="uploaded_img/<?php echo $fetch_product['image']; ?>" alt="" class="image"></a>
               <div class="name"><?php echo $fetch_product['name']; ?></div>
               <div class="price">Rs <?php echo $fetch_product['price']; ?>/-</div>
               <?php 
                              if($fetch_product['quantity']==0){?>
                                 <div class="quantity"><?php echo "Out of Stock"; ?></div>
                                 <?php
                              }
                           ?>
               <input type="number"  class="qty" name="product_quantity" min="1" value="1">
               <input type="hidden" name="product_name" value="<?php echo $fetch_product['name']; ?>">
               <input type="hidden" name="product_price" value="<?php echo $fetch_product['price']; ?>">
               <input type="hidden" name="product_image" value="<?php echo $fetch_product['image']; ?>">
               <input type="submit" class="btn" value="add to cart" name="add_to_cart">
            </form>

            <?php
            }
            }else{
               echo '<p class="empty">no result found!</p>';
            }
         }

      }else{
         echo '<p class="empty">search something!</p>';
      }
   ?>
   </div>
  

</section>









<?php include 'footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>