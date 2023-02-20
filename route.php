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
        //adding new route for a bus
        if(isset($_GET['busId'])&&isset($_GET['newRouteId'])){
            $busId = $_GET['busId'];
            $newRouteId = $_GET['newRouteId'];
            $busQ = "SELECT name FROM bus WHERE id=$busId and company_id = $id";
            $busR = mysqli_query($conn, $busQ);
            if(mysqli_num_rows($busR)<=0){
                echo("<p>This Bus Doesn't belong to your company</p>");
            }
            else{
                $busNameArr = mysqli_fetch_array($busR);
                $busName = $busNameArr['name'];
                $rtAdQ = "UPDATE bus SET route_id = $newRouteId WHERE id=$busId";
                $routeQ = "SELECT route_no FROM route WHERE id=$newRouteId";
                $routeR = mysqli_query($conn, $routeQ);
                $routeNo = 0;
                if(mysqli_num_rows($routeR)>0){
                    $rt = mysqli_fetch_array($routeR);
                    $routeNo = $rt['route_no'];
                }
                if($routeNo<=0){
                    echo("<p>This Route Doesn't exist</p>");
                }
                else if(mysqli_query($conn, $rtAdQ)){
                    echo("<p>Route No $routeNo Added to the bus $busName</p>");
                }
            }
        }
        if(isset($_GET['busId'])){
            $busId = $_GET['busId'];
            //echo($busId);
            $busQ = "SELECT * FROM bus WHERE id = $busId";
            $busR = mysqli_query($conn, $busQ);
            if(mysqli_num_rows($busR)>0){
                $bus = mysqli_fetch_array($busR);
                $busName = $bus['name'];
                echo("<h3>Bus: $busName</h3>");
                if($bus['route_id']===NULL){
                    echo("<p>Please Add a Route</p>");
                    $addcng = "ADD";
                }
                else{
                    $busRoute = $bus['route_id'];

                    //catch sequence and fare, then insert
                    if(isset($_POST['adSeq'])){
                        //Adding Sequence and Fare
                        $erChk = 1;
                        $iRouteId = $_POST['routeId'];
                        $iBusId = $_POST['busId'];
                        $hasQ = "SELECT * FROM has WHERE route_id=$iRouteId";
                        $hasR = mysqli_query($conn, $hasQ);
                        if(mysqli_num_rows($hasR)>0){
                            while($has=mysqli_fetch_array($hasR)){
                                $iStpId = $has['stop_id'];
                                $sqName = "sqc".$iStpId;
                                $fareName = "fare".$iStpId;
                                $isq = $_POST[$sqName];
                                $ifare = $_POST[$fareName];
                                $insertQ = "INSERT INTO contains(bus_id, route_id, stop_id, sequence, fare)
                                    VALUES ($iBusId, $iRouteId, $iStpId, $isq, $ifare)";
                                if(mysqli_query($conn, $insertQ)) $erChk *= 1;
                                else {
                                        $erChk *=0;
                                    }
                            }
                        }
                        if($erChk==1){
                            echo("<p>Insertion Successful!</p>");
                        }
                        else echo("<p>Insertion Failed!</p>");
                    }

                    $addcng = "Change to";
                    //Add and Modify Sequence and Fare

                    $fareQ = "SELECT * FROM contains WHERE bus_id=$busId and route_id=$busRoute ORDER BY sequence";
                    $fareR = mysqli_query($conn, $fareQ);
                    if(mysqli_num_rows($fareR)>0){
                        if(isset($_POST['updateEx'])){
                            $erChk = 1;
                            while($fare=mysqli_fetch_array($fareR)){
                                $nStpId = $fare['stop_id'];
                                $sqName = "sqc".$nStpId;
                                $fareName = "fare".$nStpId;
                                $nsq = $_POST[$sqName];
                                $nfare = $_POST[$fareName];
                                $updateQ = "UPDATE contains SET sequence=$nsq, fare=$nfare WHERE stop_id=$nStpId";
                                if(mysqli_query($conn, $updateQ)) $erChk *= 1;
                                else $erChk *=0;
                            }
                            if($erChk==1){
                                echo("<p>Update Successful!</p>");
                            }
                            else echo("<p>Update Failed!</p>");
                        }
                        //Showing Sequence and Fare
                        $sl=0;
                        echo("
                            <form action='route.php?busId=$busId' method='POST'>
                                <input type='hidden' name='busId' value='$busId'>
                                <table border='1'>
                        ");
                        echo("
                            <tr>
                                <th>Sl.</th> <th>Stoppage</th> <th>Sequence</th> <th>Fare</th>
                            </tr>
                        ");
                        $fareR = mysqli_query($conn, $fareQ);
                        while($fare=mysqli_fetch_array($fareR)){
                            $sl++;
                            $fStpId = $fare['stop_id'];
                            $stpQ = "SELECT name FROM stoppage WHERE id=$fStpId";
                            $stpR = mysqli_fetch_array(mysqli_query($conn, $stpQ));
                            $fStpName = $stpR['name'];
                            $fsq = $fare['sequence'];
                            $ffare = $fare['fare'];
                            echo("
                                <tr>
                                    <td>$sl</td>
                                    <td>
                                        $fStpName
                                    </td>
                                    <td>
                                        <input type='text' name='sqc$fStpId' value='$fsq'>
                                    </td>
                                    <td>
                                        <input type='text' name='fare$fStpId' value='$ffare'>
                                    </td>
                                </tr>
                            ");
                        }
                        echo("
                                    </table>
                                    <input type='submit' name='updateEx' value='Update'>
                                </form>
                                <br><br>
                            ");

                        //Showing Sequence and Fare Ends
                    }
                    else{
                        //fare not added yet But route added
                        echo("<p>Be Careful to set sequence number and fare</p>");
                        echo("<p>Just add the amount in the stoppages where fare changes from a previous stoppage. Otherwise set 0.</p>");
                        $hasQ = "SELECT * FROM has WHERE route_id=$busRoute";
                        $hasR = mysqli_query($conn, $hasQ);
                        if(mysqli_num_rows($hasR)>0){
                            //Showing Stops from Route
                            $sl=0;
                            echo("
                                <form action='route.php?busId=$busId' method='POST'>
                                    <input type='hidden' name='busId' value='$busId'>
                                    <input type='hidden' name='routeId' value='$busRoute'>
                                    <table border='1'>
                            ");
                            echo("
                                <tr>
                                    <th>Sl.</th> <th>Stoppage</th> <th>Sequence</th> <th>Fare</th>
                                </tr>
                            ");
                            while($fare=mysqli_fetch_array($hasR)){
                                $sl++;
                                $hStpId = $fare['stop_id'];
                                $stpQ = "SELECT name FROM stoppage WHERE id=$hStpId";
                                $stpR = mysqli_fetch_array(mysqli_query($conn, $stpQ));
                                $hStpName = $stpR['name'];
                                $hsq = $sl;
                                $hfare = 0;
                                echo("
                                    <tr>
                                        <td>$sl</td>
                                        <td>
                                            $hStpName
                                        </td>
                                        <td>
                                            <input type='text' name='sqc$hStpId' value='$hsq'>
                                        </td>
                                        <td>
                                            <input type='text' min=0 name='fare$hStpId' value='$hfare'>
                                        </td>
                                    </tr>
                                ");
                            }
                            echo("
                                        </table>
                                        <input type='submit' name='adSeq' value='Insert'>
                                    </form>
                                    <br><br>
                                ");

                            //Showing Stops from Route Ends
                        }
                    }

                    //Add and Modify Sequence and Fare Ends
                }
                //Showing existing Routes

                $routeQ = "SELECT * FROM route";
                $routeR = mysqli_query($conn, $routeQ);
                echo("
                        <div>
                    ");
                while($route=mysqli_fetch_array($routeR)){
                    echo("Route No: $route[route_no] <a href='route.php?busId=$busId&newRouteId=$route[id]'>$addcng</a><br><br>");
                    echo("<table border='1'\n");
                    echo("
                        <tr>
                            <th>Stoppage</th><th>Location</th>
                        </tr>
                    ");
                    $hasQ = "SELECT * FROM has WHERE route_id = $route[id]";
                    $hasR = mysqli_query($conn, $hasQ);
                    while($stopId=mysqli_fetch_array($hasR)){
                        $stpId = $stopId['stop_id'];
                        $stopQ = "SELECT name, location FROM stoppage WHERE id = $stpId";
                        $stopR = mysqli_query($conn, $stopQ);
                        while($stp=mysqli_fetch_array($stopR)){
                            echo("
                                <tr>
                                <td>$stp[name]</td><td>$stp[location]</td>\n
                                </tr>
                            ");
                        }
                    }
                    echo("</table>\n");
                    echo("<br><br><br>");
                }
                echo("</div>");

                //Showing existing Routes Ends
            }
        }
    }
    else {

        $routeQ = "SELECT * FROM route";
        $routeR = mysqli_query($conn, $routeQ);
        echo("
                <div>
            ");
        while($route=mysqli_fetch_array($routeR)){
            echo("Route No: $route[route_no]<br><br>");
            echo("<table border='1'\n");
            echo("
                <tr>
                    <th>Stoppage</th><th>Location</th>
                </tr>
            ");
            $hasQ = "SELECT * FROM has WHERE route_id = $route[id]";
            $hasR = mysqli_query($conn, $hasQ);
            while($stopId=mysqli_fetch_array($hasR)){
                $stpId = $stopId['stop_id'];
                $stopQ = "SELECT name, location FROM stoppage WHERE id = $stpId";
                $stopR = mysqli_query($conn, $stopQ);
                while($stp=mysqli_fetch_array($stopR)){
                    echo("
                        <tr>
                        <td>$stp[name]</td><td>$stp[location]</td>\n
                        </tr>
                    ");
                }
            }
            echo("</table>\n");
            echo("<br><br><br>");
        }
        echo("</div>");
    }
?>
<?php
    include('footer.php');
?>
