<?php
session_start();
require_once 'config.php';

if(isset($_POST["quantity"])) {
    $menuList = $_POST['menuList'];
    $quantity = $_POST['quantity'];
    
    $_SESSION['quantity'] = $quantity;
    $orderSummary = "SELECT * FROM a2_items WHERE id in (";
    $i = 0;
    foreach ($menuList as $key => $value) {
        if($quantity[$key] > 0) {
            if($i > 0) {
                $conditionStt .= ", ".$value;
            } else {
                $conditionStt = $value;
            } 
            $i++;
        }
    }
    $orderSummary .= $conditionStt . ") ORDER BY ID ASC";
    $result = mysqli_query($dbConn, $orderSummary);
    
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
        <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
        <title>Order Checkout</title>
        <style>
        #hideCol {
            display: none;
        }
        
        a,a:hover,a:focus {
            text-decoration: none;
        }
        
        td.rightAlign {
            text-align: right;
        }
        </style>
        <script type="text/javascript">
        $(document).ready(function() {
            $('#orderForm').on('submit', function(e){
                e.preventDefault();                
                var orderTable = new Array();
                
                $("#orderList tr").each(function(row,tr) {
                    orderTable[row] = {
                        "sno" : $(tr).find("td:eq(0)").text(),
                        "itemUS" : $(tr).find("td:eq(1)").text(),
                        "itemUK" : $(tr).find("td:eq(2)").text(),
                        "price" : $(tr).find("td:eq(3)").text(),
                        "quantity" : $(tr).find("td:eq(4)").text(),
                        "subTotal" : $(tr).find("td:eq(5)").text(),
                    };
                });
                orderTable.shift();
                orderTable = JSON.stringify(orderTable);
                console.log(orderTable);
                var email = $.trim($("#recipientemail").val());
                $.ajax({
                    url: 'customerAjax.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {'callFrom': 'checkoutOrder.php','action' : 'sendEmail','email':email,'tableData':orderTable},
                    success: function(output) {
                        if(output[0] === 1) {
                            alert("Thank you!  Please check your email");
                            window.location.href = "index.php";
                        } else if (output[0] === 0) {
                            alert(output[1]);
                    }
                },
                    error: function(error) {
                        console.log(error);
                        alert("Error: "+error);
                    }
                });
            });
        });
        </script>
    </head>
    <body>
        <form id="orderForm" method="POST" role="form" action="#">
        <div class="col-xs-offset-1 col-xs-10 col-xs-offset-1 alert alert-success">
            <h3 class="text-center">Order Checkout</h3>
            <table id="orderList" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>SNo</th>
                        <th>Dish Name & Description (US)</th>
                        <th id="hideCol">Dish Name & Description (UK)</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Sub Total</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $sno = 1;
                $subTotal = 0;
while ($order = mysqli_fetch_assoc($result)) {
                ?>
                    <tr>
                        <td><?php echo $sno;?></td>
                        <td>
                            <?php echo $order["item_name_us"];?> <br />
                            <?php echo $order["description_us"];?>
                        </td>
                        <td id="hideCol">
                            <?php echo $order["item_name_uk"];?> <br />
                            <?php echo $order["description_uk"];?>
                        </td>
                        <td><?php echo number_format($order["price"],2);?></td>
                        <?php
                    foreach ($menuList as $key => $value) {
                        if ($value == $order["id"]) {
                            $quantityMatch = $quantity[$key];
                        }
                    }
                        ?>
                        
                        <td><?php echo $quantityMatch;?></td>
                        <td class="rightAlign"><?php 
                        $totalPrice = $order["price"] * $quantityMatch;
                        $subTotal += $totalPrice;
                        echo number_format($totalPrice,2);
                        ?></td>
                    </tr> 
<?php 
$sno++;
} ?>
                    <tr>
                        <td colspan="4" class="rightAlign">Vat(1%)</td>
                        <td class="rightAlign"><?php 
                        $vat = $subTotal * 0.01;
                        echo number_format($vat,2);
                        ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="rightAlign">Service Charge(15%)</td>
                        <td class="rightAlign"><?php 
                        $serviceCharge = $subTotal * 0.15;
                        echo number_format($serviceCharge,2);
                        ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="rightAlign">Delivery Charge</td>
                        <td class="rightAlign"><?php 
                        $deliveryCharge = 15;
                        echo number_format($deliveryCharge,2);
                        ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="rightAlign">Grand Total</td>
                        <td class="rightAlign">$<?php 
                        $total = $subTotal + $vat + $serviceCharge + $deliveryCharge;
                        echo number_format($total,2);
                        ?></td>
                    </tr>
                </tbody>
            </table>
            <br /><br />
            <h4>Please enter your details to complete this order</h4><br />
            <div class="col-xs-offset-3 col-xs-6 col-xs-offset-3">
                <input id="recipientName" type="text" class="form-control" name="recipientName" placeholder="Enter recipient name here" required="required" autofocus="autofocus"/>
            </div>
            <div class="clearfix"></div>
            <br/>
            <div class="col-xs-offset-3 col-xs-6 col-xs-offset-3">
                <input id="recipientemail" type="email" class="form-control" name="recipientemail" placeholder="Enter email ID here" required="required" />
            </div>
            <div class="clearfix"></div>
            <br/>
            <div class="col-xs-offset-3 col-xs-6 col-xs-offset-3">
                <input id="recipientAddr" type="text" class="form-control" name="recipientAddr" placeholder="Enter mailing address here" required="required" />
            </div>
            <div class="clearfix"></div>
            <br/>
            <div class="col-xs-offset-3 col-xs-6 col-xs-offset-3">
                <input id="recipientMobNum" type="number" class="form-control" name="recipientMobNum" placeholder="Enter mobile number here" required="required" />
            </div>
            <div class="clearfix"></div>
            <br/>
            <div class="col-xs-4">
                <a href="home.php"><button type="button" class="btn btn-block btn-success" name="back2order" id="back2order">Back to Order</button></a>
            </div>
            <div class="col-xs-offset-4 col-xs-4">
                <input type="submit" class="btn btn-block btn-success" name="confirmOrder" id="confirmOrder" value="Confirm Order" />
            </div>
        </div>    
        </form>
    </body>
</html>
