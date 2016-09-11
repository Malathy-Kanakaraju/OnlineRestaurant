$(document).ready(function() {
    
    //Execute on body load to get all products
    $.ajax({
        url: "adminAjax.php",
        method: "POST",
        data: {"action":"getProductList"},
        dataType: "json",
        success: function(output){
            if (output[0] === 0) {
//                $("#itemTable tbody").append("<tr><td colspan='11'>"+output[1]+"</td></tr>");
            }else {
                populateTable(output);
            }
            console.log(output);
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
    
    $(document).on('click',"td #editItem",function(e) {
        console.log('inside click edititem');
        e.preventDefault();
        document.getElementById("editItemForm").reset();
        $("#editItemModal").modal('show');

        $("#product_id").val($(this).closest('tr').find("td:eq(0)").text());
        $("#itemName_e").val($(this).closest('tr').find("td:eq(2)").text());
        $("#code_e").val($(this).parents('tr').find("td:eq(3)").text());
        $("#description_e").val($(this).parents('tr').find("td:eq(4)").text());
        $("#price_e").val($(this).parents('tr').find("td:eq(5)").text());
        $("#stock_e").val($(this).parents('tr').find("td:eq(6)").text());
        $("#date1_e").val($(this).parents('tr').find("td:eq(9)").text());
        var course = $.trim($(this).closest('tr').find("td:eq(7)").text());
        console.log(course);
        $("option[id='#courseType_e']","#editItemForm").each(function(){
            console.log($(this).text());
            if ($.trim($(this).text()) === course) {
                console.log($(this).text());
                $(this).attr("selected","selected");
            }
        }); 
        $("option[id='#servingTime_e']","#editItemForm").each(function(){
            if ($.trim($(this).text()) === $.trim($(this).closest('tr').find("td:eq(8)").text())) {
                $(this).attr("selected","selected");
            }
        }); 
        $('#modalMsg_e').html("");
    });



});