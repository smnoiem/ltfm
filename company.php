<?php
    include('header.php');
    include('server.php');
    if(isset($_SESSION['email'])){
        $email = $_SESSION['email'];
        $account_check_query = "SELECT * FROM company WHERE email='$email' LIMIT 1";
        $result = mysqli_query($conn, $account_check_query);
        $user = mysqli_fetch_assoc($result);
        $name = $user['name'];
        $id = $user['id'];
        echo("
            <div class='header'>
                <h2>Company Control Panel</h2>
                <p>Hello, $name</p>
                <a href=\"login.php?logout=true\">Logout</a>
                <br>
                <br>
                <hr>
            </div>
        ");
        $busQ = "SELECT * FROM bus WHERE company_id=$id";
        $busR = mysqli_query($conn, $busQ);
        if(mysqli_num_rows($busR)>0){
            if(isset($_GET['editId'])){
                $editId = $_GET['editId'];
                $editQ = "SELECT * FROM bus WHERE company_id=$id and id=$editId";
                $editR = mysqli_query($conn, $editQ);
                if(mysqli_num_rows($editR)>0){
                    $editInf = mysqli_fetch_array($editR);
                    echo("
                        <br><br>
                        <p>Edit Bus:</p>
                        <form method='POST' action='company.php'>
                            <p>Bus Name:</p>
                            <input type='text' name='name' value='$editInf[name]'>
                            <input type='hidden' name='busId' value='$editInf[id]'>
                            <p>Bus Type:</p>
                            <select name='type'>
                                <option>Economy Non-AC</option>
                                <option>Economy AC</option>
                                <option>Business Class</option>
                            </select>
                            <p>Minimum Fare:</p>
                            <input type='number'min=0 name='min_fare' value='$editInf[min_fare]'>
                            <p>Pass Level You Support:</p>
                            <select name='pass'>
                                <option>No</option>
                                <option>Half</option>
                                <option>Full</option>
                            </select>
                            <br><br><input type='submit' name='editbus' value='Edit'>
                        </form>
                    ");
                }
            }
            if(isset($_POST['editbus'])){
                $updateQ = "UPDATE bus
                    SET name='$_POST[name]', type='$_POST[type]', min_fare='$_POST[min_fare]', fare_pass_status='$_POST[pass]'
                    WHERE id=$_POST[busId]";
                if(mysqli_query($conn, $updateQ)){
                    echo("<p>Updated Successfully!</p>");
                }
                else echo("<p>Update Failed!</p>");
            }
            $busR = mysqli_query($conn, $busQ);
            $sl=0;
            echo("
                    <table border='1'>
                    <tr>
                        <th>Sl.</th><th> Bus Name </th><th>Type</th><th>Minimum Fare</th><th>Fare Pass Status</th><th>Route Id</th><th>Edit</th>
                    </tr>
                ");
            while($bus=mysqli_fetch_array($busR)){
                $sl++;
                if(!isset($bus['route_id'])){
                    $routeId = "Add Route/Fare";
                }
                else $routeId = $bus['route_id'] . " - Edit Route/Fare";
                echo("
                    <tr>
                        <td>$sl</td><td> <a href='bus.php?id=$id'>$bus[name]</a> </td><td>$bus[type]</td><td>$bus[min_fare]</td><td>$bus[fare_pass_status]</td><td><a href=route.php?busId=$bus[id]>$routeId</a></td><td><a href='company.php?editId=$bus[id]'>Edit</a></td>
                    </tr>
                ");
            }
            echo("</table>");
        }
        else echo("<p>No Buses Found. Please Add a New Bus</p>");
        //adding new bus
        if(isset($_POST['addbus'])){
            $newName = $_POST['name'];
            $newType = $_POST['type'];
            $newMinFare = $_POST['min_fare'];
            $newPass = $_POST['pass'];
            $newComId = $_POST['company_id'];
            echo("<br>$newName<br>$newType<br>$newMinFare<br>$newPass<br>$newComId<br>");
            $addBusQ = "
                INSERT INTO
                bus (name, type, min_fare, status, fare_pass_status, company_id)
                VALUES ('$newName', '$newType', '$newMinFare', 'PENDING', '$newPass', '$newComId')";
            if(mysqli_query($conn, $addBusQ)){
                $newBusId = mysqli_insert_id($conn);
                echo("New Bus ( ID: $newBusId ) Added.<br>Please add the traveling route with Fare changes per Stop<br>");

            }
            else{
                echo "error: " . mysqli_errno($conn);
            }
        }
    }
    else header('Location: login.php');
?>

        <br><br>
        <p>Add New Bus:</p>
        <form method='POST' action=''>
            <p>Bus Name:</p>
            <input type='text' name='name'>
            <p>Bus Type:</p>
            <select name='type'>
                <option>Economy Non-AC</option>
                <option>Economy AC</option>
                <option>Business Class</option>
            </select>
            <p>Minimum Fare:</p>
            <input type='number' value=0 min=0 name='min_fare'>
            <p>Pass Level You Support:</p>
            <select name='pass'>
                <option>No</option>
                <option>Half</option>
                <option>Full</option>
            </select>
            <input type='hidden' name='company_id' value='<?= $id; ?>'>
            <br><br><input type='submit' name='addbus' value='Add Bus'>
        </form>
</body>
</html>
