
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$user_mail = $user_phone = $user_name = $user_surname = $user_birthdate = "";

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "registratiom_form";


$responseToCall = array(
    "result"    => null,
    "message"   => null,
    "redirect"  => null,
    "error"     => null,
);

$validationMessages = array(
    "nameError"     => null,
    "surnameError"   => null,
    "emailError"    => null,
    "phoneError"    => null,
    "dateError"     => null,
);

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
};

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["name"])) {
        $validationMessages["nameError"] = "* Παρακαλώ συμπληρώστε το όνομα χρήστη";
    } else {
        $user_name = test_input($_POST["name"]);
        if (!preg_match("/^[a-zA-Zα-ωΑ-Ω]/", $user_name)) {
            $validationMessages["nameError"] = "Το όνομα χρήστη δεν είναι έγκυρο";
        }
        // a-zA-Zα-ωΑ-Ω
    }
    if (empty($_POST["surname"])) {
        $validationMessages["surnameError"] = "* Παρακαλώ συμπληρώστε το επώνυμο χρήστη";
    } else {
        $user_surname = test_input($_POST["surname"]);
        if (!preg_match("/^[a-zA-Zα-ωΑ-Ω]/", $user_surname)) {
            $validationMessages["surnameError"] = "Το επώνυμο χρήστη δεν είναι έγκυρο";
        }
    }
    if (empty($_POST["telephone"])) {
        $validationMessages["phoneError"] = "* Παρακαλώ συμπληρώστε το τηλέφωνο επικοινωνίας";
    } else {
        $user_phone = test_input($_POST["telephone"]);
        if (!preg_match("/^\\+?[1-9][0-9]{7,14}$/", $user_phone)) {
            $validationMessages["phoneError"] = "Το τηλέφωνο επικοινωνίας δεν είναι έγκυρο";
        } else {
        }
    }
    if (empty($_POST["mail"])) {
        $validationMessages["emailError"] = "* Παρακαλώ συμπληρώστε το email χρήστη";
    } else {
        $user_mail = test_input($_POST["mail"]);
        if (!filter_var($user_mail, FILTER_VALIDATE_EMAIL)) {
            $validationMessages["emailError"] = "* Το email δεν ειναι έγκυρο";
        }
    }
    if (empty($_POST["birthdate"])) {
        $validationMessages["dateError"] = "* Παρακαλώ συμπληρώστε την ημερομηνία γέννησης";
    } else {
        $user_birthdate = test_input($_POST["birthdate"]);
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $user_birthdate)) {
            $validationMessages["dateError"] = "* Παρακαλώ συμπληρώστε την σωστή ημερομηνία γέννησης";
        }
    }






    if (is_null($validationMessages["nameError"]) && is_null($validationMessages["surnameError"]) && is_null($validationMessages["emailError"]) && is_null($validationMessages["phoneError"]) && is_null($validationMessages["dateError"])) {

        //Edw request gia email
        $sqlSelect_mail = "SELECT email FROM users WHERE email = '" . $user_mail . "'";
        //edw request gia phone
        $sqlSelect_phone = "SELECT phone FROM users WHERE phone = '" . $user_phone . "'";
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $resultEmail = $conn->query($sqlSelect_mail);
        $resultPhone = $conn->query($sqlSelect_phone);
        $emailRowCount = mysqli_num_rows($resultEmail);
        $phoneRowCount = mysqli_num_rows($resultPhone);



        $isDuplicate = false;
        if ($emailRowCount > 0 && $phoneRowCount > 0) {
            $isDuplicate = true;
            $validationMessages["phoneError"] = "* Το τηλέφωνο που καταχωρήσατε υπάρχει ήδη";
            $validationMessages["emailError"] = "* Το email που καταχωρήσατε υπάρχει ήδη";
            $responseToCall = array(
                "result" => false,
                "message" => $validationMessages,
                "error" => null,
            );
            print_r(json_encode($responseToCall));
        } else if ($phoneRowCount > 0) {
            $isDuplicate = true;
            $validationMessages["phoneError"] = "* Το τηλέφωνο που καταχωρήσατε υπάρχει ήδη";
            $responseToCall = array(
                "result" => false,
                "message" => $validationMessages,
                "error" => null,
            );
            print_r(json_encode($responseToCall));
        } else if ($emailRowCount > 0) {
            $isDuplicate = true;
            $validationMessages["emailError"] = "* Το email που καταχωρήσατε υπάρχει ήδη";
            $responseToCall = array(
                "result" => false,
                "message" => $validationMessages,
                "error" => null,
            );
            print_r(json_encode($responseToCall));
        }


        if (!$isDuplicate) {
            $sql = "INSERT INTO users (firstname, lastname, phone, email, birth_date)
            VALUES ('" . $user_name . "', '" . $user_surname . "', '" . $user_phone . "', '" . $user_mail . "', '" . $user_birthdate . "')";

            if ($conn->query($sql) === TRUE) {
                $responseToCall["result"] = true;
                $responseToCall["message"] = "New record created successfully";
                $responseToCall["redirect"] = "succesful_submit.html";

                print_r(json_encode($responseToCall));
                $conn->close();
            }
        }
    } else {
        $responseToCall = array(
            "result" => false,
            "message" => $validationMessages,
            "error" => null,
        );
        print_r(json_encode($responseToCall));
    }
}
?>




