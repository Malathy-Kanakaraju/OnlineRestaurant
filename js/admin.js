$(document).ready(function() {
    
    //Execute on body load to get all products
    $.ajax({
        url: "adminAjax.php",
        method: "POST",
        data: {"action":"getProductList"},
        dataType: "json",
        success: function(output){
            populateTable(output);
        }, error: function(error) {
            console.log(error);
        }
    });
    
    var dTOptions = {

    columns:[
            {data:'id'},
            {data:'image'},
            {data:'name'},
            {data:'sku'},
            {data:'description'},
            {data:'price'},
            {data:'stock'},
            {data:'meal_type'},
            {data:'serving_time'},
            {data:'availability'},
            {data:'action'}
        ]
/*    ,
    
    "columnDefs": [{
        className: "hideCol",
        "targets": [ 0 ]
    }]
    */
    };

    var table = $('#itemTable').DataTable(dTOptions);
    
    function populateTable(output) {

    table
            .clear()
            .draw();

    table.rows.add(output).draw();
    }

    //Open modal to add new product
    $('#newItemForm').on('submit', function(e){
        e.preventDefault();
        var data = new FormData(this);
        data.append("action","addNewItem");
        data.append("fileToUpload","fileToUpload");
        console.log('hi');
        $.ajax({
            url: 'adminAjax.php',
            type: 'POST',
            data: data,  
            dataType: 'json',
            contentType: false,     
            processData:false, 
            success: function(output) {
                if(output[0] === 1) {
                    alert(output[1]);
                    $('#addItemModal').modal('hide');
                    window.location.href = "adminHome.php";
                } else if (output[0] === 0) {
                    $('#modalMsg').html(output[1]);
            } 
        }, error: function(error) {
                console.log(error);
                alert("Error: Check console for more details.");
            }
        });
    });

});