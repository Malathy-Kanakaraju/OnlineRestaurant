<?php 
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
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
        <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.css">
        <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.js"></script>
        <title>Food-e-Comm - Admin Page</title>
        <style>
        .hideCol {
            display: none;
        }
        </style>

        <script type="text/javascript">
        $(document).ready(function() {
            
            
            $(document).on('click',"td #editItem",function(e) {
                e.preventDefault();
                document.getElementById("editItemForm").reset();
                $("#editItemModal").show();

                $("#itemId").val($(this).closest('tr').find("td:eq(0)").text());
                $("#itemNameUS_e").val($(this).closest('tr').find("td:eq(1)").text());
                $("#itemNameUK_e").val($(this).parents('tr').find("td:eq(2)").text());
                $("#descriptionUS_e").val($(this).parents('tr').find("td:eq(3)").text());
                $("#descriptionUK_e").val($(this).parents('tr').find("td:eq(4)").text());
                $("#price_e").val($(this).parents('tr').find("td:eq(5)").text());
                $('#modalMsg_e').html("");
            });
    
            $("#editItemForm").on("submit",function(e) {
                e.preventDefault();

                var data = new FormData(this);
                data.append("action","editItem");
                data.append("fileToUpload","fileToUpload_e");
                
                $.ajax({
                    url: 'adminAjax.php',
                    type: 'POST',
                    dataType: 'json',
                    contentType: false,     
                    processData:false, 
                    data: data, 
                    success: function(output) {
                        if(output[0] === 1) {
                            alert(output[1]);
                            $('#editItemModal').hide();
                            window.location.href = "adminHome.php";
                        } else if (output[0] === 0) {
                            $('#modalMsg_e').html(output[1]);
                    } 
                },
                    error: function(error) {
                        console.log(error);
                        alert("Error: Check console for more details.");
                    }
                });
            });

            $(document).on('click',"td button#deleteItem",function(e) {
                e.preventDefault();
                var id = $(this).closest('tr').find("td:eq(0)").text();
                if (confirm("Are you sure to delete this item?")) {
                    $.ajax({
                        url: 'adminAjax.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {'action':'deleteItem','id':id},
                        success: function(output) {
                            if(output[0] === 1) {
                                alert(output[1]);
                                window.location.href = "adminHome.php";
                            } else if (output[0] === 0) {
                                alert("output[1]");
                        }
                    },
                        error: function(error) {
                            console.log(error);
                            alert("Error: Check console for more details.");
                        }
                    });
                    
                }
        });
    });
        
        function resetForm() {
            document.getElementById("newItemForm").reset();
            $('#modalMsg').html("");
        };

        </script>
        
    </head>
    <body>
        <div class="row">
            <p class="pull-right">Welcome <?php echo $_SESSION['user_name'];?>!<br /><a href="index.php?logout=1">Logout</a></p>
        </div>
        <div class="col-xs-offset-1 col-xs-10 col-xs-offset-1 alert alert-info">
            <h3 class="text-center bg-warning">Item Inventory</h3>
            <table id="itemTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Item name</th>
                        <th>SKU code</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Meal Type</th>
                        <th>Serving Time</th>
                        <th>Available from</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
            <button type="button" id="addNewProduct" class="btn btn-primary" data-toggle="modal" data-target="#addItemModal" onclick="resetForm()"> Add new item</button>
        </div>
        
        <div class="modal fade" id="addItemModal" role="dialog" aria-labelledby="subjectModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <div class="model-title"><h3>Add New Item to Inventory</h3></div>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal" role="form" id="newItemForm" enctype="multipart/form-data" method="POST">
                            <div class="form-group">
                                <label class="control-label col-xs-4" for="itemName">Item Name</label>
                                <div class="col-xs-8"><input id="itemName" type="text" class="form-control" name="itemName" placeholder="Item name" required="required"/></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-xs-4" for="code">SKU code</label>
                                <div class="col-xs-8"><input id="code" type="text" class="form-control" name="code" placeholder="SKU code" required="required"/></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-xs-4" for="description">Description</label>
                                <div class="col-xs-8"><input id="description" type="text" class="form-control" name="description" placeholder="Description" required="required"/></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-xs-4" for="price">Price</label>
                                <div class="col-xs-8"><input id="price" type="number" class="form-control" step="0.01" name="price" min="0" required="required"/></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-xs-4" for="stock">Stock (Inventory)</label>
                                <div class="col-xs-8"><input id="stock" type="number" class="form-control" name="stock" placeholder="Stock" required="required"/></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-xs-4" for="date1">Available From</label>
                                <div class="col-xs-8"><input id="date1" type="date" class="form-control" name="date1" placeholder="Available date" required="required"/></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-xs-4" for="fileToUpload">Upload Image</label>
                                <div class="col-xs-8"><input type="file" name="fileToUpload" id="fileToUpload"/></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-xs-4" for="courseType">Meal Course Type</label>
                                <div class="col-xs-8">
                                    <select name="courseType" class="form-control" required="required">
                                        <option value="starter">Starter</option>
                                        <option value="main_course">Main Course</option>
                                        <option value="dessert">Dessert</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-xs-4" for="servingTime">Serving Time</label>
                                <div class="col-xs-8">
                                    <select name="servingTime" class="form-control" required="required">
                                        <option value="breakfast">Breakfast</option>
                                        <option value="lunch">Lunch</option>
                                        <option value="dinner">Dinner</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-xs-offset-4 col-xs-8">
                                    <input type="submit" class="btn btn-primary" name="addNewItem" id="addNewItem" value=" Add ">&nbsp;
                                </div>
                            </div>
                            <br />
                            <div id="modalMsg" class="text-error"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editItemModal" role="dialog" aria-labelledby="subjectModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <div class="model-title"><h3>Edit Item in the Inventory</h3></div>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal" role="form" id="editItemForm" enctype="multipart/form-data" method="POST">
                            <div class="form-group">
                                <label class="control-label col-xs-4" for="itemNameUS_e">Item Name (US)</label>
                                <div class="col-xs-8"><input id="itemNameUS_e" type="text" class="form-control" name="itemNameUS_e" placeholder="Item name in US" required="required"/></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-xs-4" for="itemNameUK_e">Item Name (UK)</label>
                                <div class="col-xs-8"><input id="itemNameUK_e" type="text" class="form-control" name="itemNameUK_e" placeholder="Item name in UK" required="required"/></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-xs-4" for="descriptionUS_e">Item Type (US)</label>
                                <div class="col-xs-8"><input id="descriptionUS_e" type="text" class="form-control" name="descriptionUS_e" placeholder="Item type in US" required="required"/></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-xs-4" for="descriptionUK_e">Item Type (UK)</label>
                                <div class="col-xs-8"><input id="descriptionUK_e" type="text" class="form-control" name="descriptionUK_e" placeholder="Item type in UK" required="required"/></div>
                            </div>
                            <div class="form-group">    
                                <label class="control-label col-xs-4" for="price_e">Price</label>
                                <div class="col-xs-8"><input id="price_e" type="number" class="form-control" step="0.01" name="price_e" required="required"/></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-xs-4" for="fileToUpload_e">Upload Image</label>
                                <div class="col-xs-8"><input type="file" name="fileToUpload_e" id="fileToUpload_e"/></div>
                                <input id="itemId" name="itemId" type="hidden" />
                            </div>
                            <div class="form-group">     
                                <div class="col-xs-offset-4 col-xs-8">
                                    <input type="submit" class="btn btn-primary btn-primary" name="addNewItem_e" id="editItemModalBtn" value=" Edit ">&nbsp;
                                </div>
                            </div><br />
                            <div id="modalMsg_e" class="text-error"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script src="../js/admin.js"></script>
    </body>
</html>
