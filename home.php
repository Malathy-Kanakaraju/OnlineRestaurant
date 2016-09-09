<?php
session_start();

if (!isset($_SESSION['customer_id'])) {
    header("Location: index.php");
}

require_once 'config.php';

$result = mysqli_query($dbConn, "SELECT * FROM products ORDER BY ID ASC");

if(!$result) {
    die('Database error: '.  mysqli_error($dbConn));
} 

if(isset($_SESSION['quantity'])) {
    $quantity = $_SESSION["quantity"];
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
        <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
        <title>Food-e-Comm - Home</title>
        <style>
        #hideCol {
            display: none;
        }
        </style>
        <script type="text/javascript">
        $(document).ready(function() {

            $('#orderForm').submit(function(e) {
                var quantityTotal = 0;
                $(".quantity").each(function(){
                        quantityTotal = parseInt($(this).val()) + quantityTotal;
                });
                
                if (quantityTotal === 0) {
                    e.preventDefault();
                    alert('Update the quantity of atleast one item before proceeding with checkout');
                };
            });
            

        });

        </script>
    </head>
    <body>
        <div class="col-xs-offset-1 col-xs-10 col-xs-offset-1 alert alert-warning">
            <h3 class="text-center">Menu - Bon Appetit!</h3>
            <p>Select your items by increasing the quantity and proceed to checkout</p>
            <br />
            <form id="orderForm" role="form" method="POST" action="checkoutOrder.php">
                <table id="menuTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th id="hideCol">ID</th>
                            <th>Item Name (US)</th>
                            <th id="hideCol">Item Name (UK)</th>
                            <th>Description (US)</th>
                            <th id="hideCol">Description (UK)</th>
                            <th>Price</th>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if(mysqli_num_rows($result)>0) {
                            $i=0;
                        while ($menu=mysqli_fetch_assoc($result)) {
                        ?>
                        <tr>
                            <td id="hideCol"><input type="hidden" value="<?php echo $menu['id'] ?>" name="menuList[]" /></td>
                            <td><img src="admin/<?php echo $menu['image']?>" class="img-rounded" alt="food_photo" width="100" height="100" />&nbsp;<?php echo $menu['item_name_us'];?></td>
                            <td id="hideCol"><?php echo $menu['item_name_uk'];?></td>
                            <td><?php echo $menu['description_us'];?></td>
                            <td id="hideCol"><?php echo $menu['item_type_uk'];?></td>
                            <td>$<?php echo round($menu['price'],2);?></td>
                            <td><input type="number" value="<?php echo isset($quantity)? $quantity[$i]:'0'; $i++ ?>" min="0" max="10" class="quantity" name="quantity[]" /></td>
                        </tr>
                        <?php }}
                        else { ?>
                            <tr><td colspan="7"></td></tr>
<?php
                        }
?>
                    </tbody>
                </table>
                <button class="pull-right btn btn-warning" id="proceed2Cart" name="proceed2Cart">Proceed to checkout</button>
            </form>
        </div>
    </body>
</html>
