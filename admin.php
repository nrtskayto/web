<?php
include 'dbh.php';
//include 'auth.php';


//$curr_user = json_encode($user);

if (isset($_GET['query']) && $_GET['query'] === 'statssss') {


    $resp = json_encode([
        
            'users_count' => users_count(),
            'entriesCount_requestType' => count(entriesCount_requestType()),
            'entriesCount_responseStatus' => count(entriesCount_responseStatus()),
            'unique_Domains' => unique_Domains(),
            'tUniqueUserISPs' => tUniqueUserISPs(),
            //'AvgAgeByContentType' => AvgAgeByContentType()
        
    ]);
    
    echo $resp;

}

function users_count() {
    $query = "SELECT COUNT(*) as cnt FROM `users`";
    $results = executeQueryAndGetResults($query);
    return count($results) === 1 ? $results[0]['cnt'] : 0;
}

function entriesCount_requestType() {
    $query = "SELECT r.method as tag, count(*) as value FROM `request` as r INNER JOIN `entry` as e ON r.entry_id = e.id GROUP BY r.method";
    return executeQueryAndGetResults($query);
}

function entriesCount_responseStatus() {
    $query = "SELECT r.status as tag, count(*) as value FROM `response` as r INNER JOIN `entry` as e ON r.entry_id = e.id GROUP BY r.status";
    return executeQueryAndGetResults($query);
}

function unique_Domains() {
    $query = "SELECT COUNT(DISTINCT url) as cnt FROM `request`";
    $results = executeQueryAndGetResults($query);
    return count($results) === 1 ? $results[0]['cnt'] : 0;
}
function tUniqueUserISPs() {
    $query = "SELECT COUNT(DISTINCT user_isp) as cnt FROM `uploads`";
    $results = executeQueryAndGetResults($query);
    return count($results) === 1 ? $results[0]['cnt'] : 0;
}

function AvgAgeByContentType() {
    $query = "SELECT JSON_EXTRACT(`headers`, '$.content-type') as tag, AVG(JSON_EXTRACT(`headers`, '$.age')) as value "
            ."FROM `response` WHERE JSON_EXTRACT(`headers`, '$.content-type') IS NOT NULL AND JSON_EXTRACT(`headers`, '$.age') "
            ."IS NOT NULL GROUP BY tag";
    return executeQueryAndGetResults($query);
}

function executeQueryAndGetResults($query) {
    global $conn;
    $rows = [];
    if ($result = $conn->query($query)) {
        while($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $result->free();
    }
    return $rows;
}

?>