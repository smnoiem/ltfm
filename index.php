<?php
    include('header.php');
    if(isset($_SESSION['email'])){
        header('Location: login.php');
    }
	$startId=0;
	$endId=0;
	$busTable="";
	$found="Sorry! No bus available for this route.";
	//if Searched
	if(isset($_POST['start'])&&isset($_POST['end'])){
        $startId = $_POST['start'];
        $endId = $_POST['end'];
        //search query
        $srcQ = "SELECT route_id
            FROM contains con1 GROUP BY route_id
            HAVING (
            SELECT count(route_id) FROM contains con2
            WHERE route_id=con1.route_id and (stop_id=$startId OR stop_id=$endId)
            )=2";

        $srcRes = mysqli_query($conn, $srcQ);
        $sl=0;
        while($val = mysqli_fetch_array($srcRes)){
            $routeId = $val['route_id'];
            //echo("$routeId <br>");
            $sqRes = mysqli_query($conn, "SELECT sequence FROM contains WHERE route_id=$routeId AND stop_id=$startId");
            $startSq = mysqli_fetch_assoc($sqRes);
            $sqRes = mysqli_query($conn, "SELECT sequence FROM contains WHERE route_id=$routeId AND stop_id=$endId");
            $endSq = mysqli_fetch_assoc($sqRes);
            $startSq = $startSq['sequence'];
            $endSq = $endSq['sequence'];
            //echo("$startSq $endSq <br>");
            if($startSq>$endSq) $fareQ="SELECT sequence, fare FROM contains WHERE route_id=$routeId ORDER BY sequence";
            else $fareQ="SELECT sequence, fare FROM contains WHERE route_id=$routeId ORDER BY sequence DESC";
            $fareRes = mysqli_query($conn, $fareQ);
            $sum=0;
            $flag = false;
            while($fareVal=mysqli_fetch_array($fareRes)){
                $sqNo = $fareVal['sequence'];
                $fareTemp = $fareVal['fare'];
                if($startSq<$endSq){
                    if($sqNo>$startSq&&$sqNo<=$endSq) $sum += $fareTemp;
                    if($sqNo==$endSq&&$fareTemp==0) $flag = true;
                    if($sqNo==($endSq+1)&&$flag) $sum += $fareTemp;
                }
                else{
                    if($sqNo<=$startSq&&$sqNo>$endSq) $sum += $fareTemp;
                    if($sqNo==$startSq&&$fareTemp==0) $flag = true;
                    if($sqNo==($startSq+1)&&$flag) $sum += $fareTemp;
                }
            }
            $bus="";
            $type="";
            //bus query
            $busQ = mysqli_query($conn, "SELECT * FROM bus WHERE status='approved' AND route_id=$routeId");
            if(mysqli_num_rows($busQ)>=1){
                $busRes = mysqli_fetch_array($busQ);
                $sl++;
                $busId=$busRes['id'];
                $bus=$busRes['name'];
                $type=$busRes['type'];
                $farePass = $busRes['fare_pass_status'];
                $minFare = $busRes['min_fare'];
                $companyId = $busRes['company_id'];
                $rating=0;
                $reviewQ = mysqli_query($conn, "SELECT AVG(rating) as rating FROM review WHERE bus_id=$busId");
                if(mysqli_num_rows($reviewQ)>0){
                    $reviewRes = mysqli_fetch_array($reviewQ);
                    $rating = $reviewRes['rating'];
                    $rating = round($rating, 2);
                }
                $found = "<p>We have found these buses for your travel:</p>";
                $busTable .= "
                    \t<tr>
                        \t\t<td>$sl</td><td> <a href='bus.php?id=$busId'>$bus</a> </td><td>$type</td><td>$sum</td><td>$rating</td>
                    \t</tr>
                ";
            }
        }
    }
    $stoppage1="";
    $stoppage2="";
    $q = "SELECT * FROM stoppage";
    $res = mysqli_query($conn, $q);
    while($val = mysqli_fetch_array($res)){
        $selected1="";
        $selected2="";
        if($val['id']==$startId) $selected1="selected";
        if($val['id']==$endId) $selected2="selected";
        $stoppage1 .= "<option value=".$val['id']." ".$selected1.">".$val['name']."</option>\n";
        $stoppage2 .= "<option value=".$val['id']." ".$selected2.">".$val['name']."</option>\n";
    }

?>
    <div style="min-height: 60%;">
        <a href='login.php'>Login</a>
        <hr>
        <form action="" method="POST" style="">
            <p>Select Start:</p>
            <select name="start">
                <?= $stoppage1; ?>
            </select>
            <p>Select Destination:</p>
            <select name="end">
                <?= $stoppage2; ?>
            </select>
            <input type="submit" value="Search">

        </form>
        <br><br>
        <div class="sug_bus">
            <?= $found; ?>
            <table border="1">
                <tr>
                    <th>Sl.</th><th>Bus Name</th><th>Type</th><th>Fare</th><th>Rating</th>
                </tr>
                <?= $busTable; ?>
            </table>

        </div>
    </div>
<?php
    include('footer.php');
?>
