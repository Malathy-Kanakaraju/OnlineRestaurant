<?php

require_once 'config.php';


if(isset($_POST["action"])) {
    switch ($_POST["action"]) {
        
        case "sendEmail":

            $mail_to = htmlspecialchars($_POST["email"]);
            $subject = 'Online Restaurant - Order confirmation';

            $headers = "From: its.mathy@gmail.com; \r\n";
    //        $headers .= "Reply-To: enquiries@onlinerestaurant.com; \r\n";
            $headers .= "Reply-To: its.mathy@gmail.com; \r\n";
            $headers .= "MIME-Version: 1.0; \r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1; \r\n";
    //        $headers .= "Bcc: enquiries@onlinerestaurant.com; \r\n";

            $message = "<html><head><style>table,tr,th,td {border: 1px solid black;border-collapse:collapse;padding:10px} </style></head><body>";
            $message .= "Dear Customer!<br />";
            $message .= "<p>Thank You! Your order is confirmed & ready for dispatch</p><br />"
                    . "<table><tr><th>SNo</th><th>Item Name & description(US)</th><th>Price</th><th>Quantity</th><th>Sub Total(in Rs)</th></tr>";

            $tableData = array();
            $tableData = json_decode(($_POST["tableData"]),true);
            $rowCount = count($tableData);

            foreach ($tableData as $key => $row) {
                if($key < ($rowCount-4)) {
                $message .= "<tr><td>".$row['sno']."</td><td>".$row['itemUS']."</td>"
                        . "<td>".$row['price']."</td><td>".$row['quantity']."</td><td style='text-align:right'>".$row['subTotal']."</td></tr>";
                } else {
                    $message .="<tr><td colspan='4' style='text-align:right'>".$row['sno']."</td><td style='text-align:right'>".$row['itemUS']."</td></tr>";
                }
            };


            $message .=  "</table><br />"
                    . "Regards,<br />Online Restaurant";
            $message .= "</body></html>";

            $mail_status = mail($mail_to, $subject, $message,$headers);
            if ($mail_status) {
                $output[0] = 1;
            } else {
                $output[0] = 0;
                $output[1] = "Error sending email";
            }
            echo json_encode($output);
            break;

        default:
            break;
    }
}
