$(document).ready(function(){
    
    $("#signinForm").on("submit",function(e){
        e.preventDefault();
        var data = new FormData(this);
        data.append = ("action","SignIn");
        
        $.ajax({
            url: "customerAjax.php",
            method: "POST",
            //dataType: "json",
            data: data,
            contentType: false,
            processData: false,
            success: function(data){
                if (data[0] === 1) {
                    window.location.href="home.php";
                } else {
                    $("#errorMsg").html(data[1]);
                }
                console.log('hi');
            }, error: function(error) {
                alert("Error: check console");
                console.log(error);
            }
        });
    });
    
    $("#signupForm").on("submit",function(e){
        e.preventDefault();
        var data = new FormData(this);
        data.append("action","SignUp");
        
        $.ajax({
            url: "customerAjax.php",
            method: "POST",
            data: data,
            //dataType: "json",
            contentType: false,
            processData: false,
            success: function(output){
                if (output[0] === 1) {
                    
                } else if(output[0] === 0) {
                    alert(output[1]);
                };
                console.log(output);
            }, error: function(error) {
                alert("Error: check console");
                console.log(error);
            }
        });
    });
    
    
});