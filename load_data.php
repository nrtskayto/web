<?php
include 'dbh.php';
include 'utils.php';

if(isset($_POST)){
    $file = $_POST['json'];
    //$filename = $_POST['filename'];
    //$username = $_POST['username'];
    $load = load_data_from_json($file, 1, 1, $conn);
}

function load_data_from_json($jsonfile, $filename, $username, $conn){
    
    $i = create_upload($filename,$username, $jsonfile, $conn);
    foreach ($jsonfile['entries'] as $entry){
        //entry insert
        $id = uniqid();
        $serverIPAddress = $entry['serverIPAddress'];
        $startedDateTime = $entry['startedDateTime'];
        $wait = $entry['timings']['wait'];
        $sql = "INSERT INTO entry (id, upload_id, startedDateTime, wait, serverIPAddress, server_lat, server_lng) VALUES ('$id', '$i', '$startedDateTime', '$wait', '$serverIPAddress', 40.712799, -74.005997)";
        if(mysqli_query($conn, $sql)){
            echo "ok";
        }else{
            echo "error $sql." . mysqli_error($conn)."<br>";
        }
        
        //request insert
        $request = $entry['request'];
        $headers = json_encode($request['headers']);
        //$headers = 1;
        $url = $request['url'];
        $method = $request['method'];
        $sql1 = "INSERT INTO `request` (`entry_id`, `method`, `url`, `headers`) VALUES ('$id', '$method', '$url', '$headers')";
        if(mysqli_query($conn, $sql1)){
            echo "ok";
        }else{
            echo "error $sql1." . mysqli_error($conn)."<br>";
        }

        //response insert
        $response = $entry['response'];
        $status = $response['status'];
        $statusText = $response['statusText'];
        $response_headers = json_encode($response['headers']);
        $sql2 = "INSERT INTO `response` (`entry_id`, `status`, `statusText`, `headers`) VALUES ('$id', '$status', '$statusText', '1')";
        if(mysqli_query($conn, $sql2)){
            echo "ok";
        }else{
            echo "error $sql2." . mysqli_error($conn)."<br>";
        }
    }
}

function create_upload($filename, $username, $file, $conn){
    $uploadId = guidv4();
    $userIP = getUserIpAddr();
    $userIP = gethostbyname("www.google.com");
    $userGeoData = geolocateIP($userIP);

    $user_isp = $userGeoData->isp;
    $user_lat = $userGeoData->lat;
    $user_lng = $userGeoData->lon;

    $sql = "INSERT INTO `uploads`(`id`, `user_id`, `filename`, `user_isp`, `user_lat`, `user_lng`) VALUES ('$uploadId', '$username', '$filename', '$user_isp', '$user_lat', '$user_lng')";
    if(mysqli_query($conn, $sql)){
        echo "ok";
    }else{
        echo "error $sql." . mysqli_error($conn)."<br>";
    }
    return $uploadId;
}
?>