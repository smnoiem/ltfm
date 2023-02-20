<?php
    include('header.php');
    if(isset($_SESSION['email'])){
        header('Location: login.php');
    }
    $busId="%";
    $bus="";
    $type="";
    if(isset($_GET['id'])){
        $busId=$_GET['id'];
    }

    echo("<h1>Bus Details</h1>");
    //bus query
    $busQ = mysqli_query($conn, "SELECT * FROM bus WHERE status='approved' AND id LIKE '$busId'");
    while($busRes = mysqli_fetch_array($busQ)){
        $busId=$busRes['id'];
        $bus=$busRes['name'];
        $type=$busRes['type'];
        $farePass = $busRes['fare_pass_status'];
        $minFare = $busRes['min_fare'];
        $companyId = $busRes['company_id'];
        $rating=0;
        $reviewQ = mysqli_query($conn, "SELECT rating, review, user_name FROM review WHERE bus_id=$busId");
        $reviewQ2 = mysqli_query($conn, "SELECT AVG(rating) as avgr FROM review WHERE bus_id=$busId");
        $rvwRating = mysqli_fetch_array($reviewQ2);
        $avgRating = $rvwRating['avgr'];
        $avgRating = round($avgRating, 2);
        echo("
                <div class='bus_rvw_section'>
                <h3>Bus Name: <a href='bus.php?id=$busId'>$bus</a></h3>
                <h4>Company Name:</h4>
                <h4>Average Rating: $avgRating</h4>
                <hr>
            ");
        while($reviewRes = mysqli_fetch_array($reviewQ)){
            echo("
                <div class='usr_review'>
                    <b>Rating:</b> <span>$reviewRes[rating]</span><br>
                    <b>Review:</b> <p>$reviewRes[review]</p>
                    <b>Reviewed By:</b> <p>$reviewRes[user_name]</p>
                    <br><br>
                </div>
                </div>
            ");
        }
        echo("<hr>");
    }
    if(isset($_POST['review'])){
        $busIdr = $_POST['busId'];
        $rate=$_POST['rate'];
        $rvw=$_POST['rvw'];
        $user_name=$_POST['user_name'];
        $insertRvw = "INSERT INTO review (bus_id, rating, review, user_name) VALUES ('$busIdr', '$rate', '$rvw', '$user_name')";
        if(mysqli_query($conn, $insertRvw)){
            echo("<p>Review Added!</p>");
        }

    }
    if(isset($_GET['id'])){
        $busId=$_GET['id'];
        echo("
            <div class='new-rvw'>
                <h3>Review It!</h3>
                <p>Have Experience with this Transport? Share your experience by Reviewing..</p>
                <form method='POST' action=''>
                    <input type='hidden' name='busId' value='$busId'>
                    <p>Rate It: (5 for most satisfaction)</p>
                    <input type='number' name='rate' min='1' max='5' value=5> <span>*****</span>
                    <p>Describe Your Experience:</p>
                    <textarea name='rvw' rows='10' cols='40'></textarea>
                    <p>Share Your Name?</p>
                    <input type='text' name='user_name'>
                    <input type='submit' name='review' value='Submit'>
                </form>
            </div>
        ");
    }

?>
<?php
    include('footer.php');
?>
