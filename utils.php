<?php
    function getUserIpAddr() {
        if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
            //ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            //ip pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        // return $ip;
        return $ip;
    }

    
    $ipsCache = [];
    function geolocateIP($ip) {
        // Use simple cache
        if (isset($ipsCache[$ip])) {
            return $ipsCache[$ip];
        }
        return $ipsCache[$ip] = json_decode(file_get_contents("http://ip-api.com/json/$ip"));
    }
    

    function geolocateBatchIPs($ips) {

        // filter for unique ips only
        $uniqueIps = array_unique($ips);

        $ips = [];
        // check of those unique ips which are not in the cache
        foreach ($uniqueIps as $ip) {
            if (!isset($ipsCache[$ip])) {
                // the ip does not exist in cache, we need to query the API 
                $ips[]= $ip;
            }
        }

        $endpoint = 'http://ip-api.com/batch?fields=status,lat,lon,query';

        // We chuck the ips to 100 ips / chunk which is the max limit of the API
        foreach (array_chunk($ips, 100) as $ipsChunk) {
            $options = [
                'http' => [
                    'method' => 'POST',
                    'user_agent' => 'Batch-Example/1.0',
                    'header' => 'Content-Type: application/json',
                    'content' => json_encode($ipsChunk)
                ]
            ];
            $response = file_get_contents($endpoint, false, stream_context_create($options));
    
            // Decode the response and print it
            $geolocatedIPsData = json_decode($response, true);

            // populate the cache with all the results
            foreach ($geolocatedIPsData as $data) {
                $queryIp = $data['query'];
                $ipsCache[$queryIp] = $data;
            }
        }
        
        // Now that we have finished all the queries, return a map of ips
        // and their geolocated data, using the cache
        $resultMap = [];
        foreach ($ips as $ip) {
            $resultMap[$ip] = $ipsCache[$ip];
        }
        return $resultMap;
    }



    function guidv4() {
        return bin2hex(random_bytes(16));
    }
    ?>