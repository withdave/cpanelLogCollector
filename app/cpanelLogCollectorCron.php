<?php

// This file is designed to collect relevant CPanel statistics and present them externally for collection by an external service
// At the moment it simply collects data as per descripbed in http://stackoverflow.com/questions/41629460/how-to-get-cpu-ram-usage-from-differenet-servers
// The ambition is to bring in logging data from another source and load to a MySQL database

// Set MySQL details
$mysqlHost = 'localhost';
$mysqlDatabase = 'database';
$mysqlUsername = 'username';
$mysqlPassword = 'password';


// Function: Get server CPU usage (using PHP function)
function get_server_cpu_usage($time){

    $load = sys_getloadavg();
    return $load[$time]; // Using $load[0] or 1/2 will return average over last 1, 5 and 15 minutes

}


function get_server_cpu_usage_array(){

    $load = sys_getloadavg();
    return $load; // Using $load[0] or 1/2 will return average over last 1, 5 and 15 minutes

}


// Function: Get server RAM usage (using command executed via shell)
function get_server_memory_usage(){

    $free = shell_exec('free');
    $free = (string)trim($free);
    $free_arr = explode("\n", $free);
    $mem = explode(" ", $free_arr[1]);
    $mem = array_filter($mem);
    $mem = array_merge($mem);
    $memory_usage = $mem[2]/$mem[1]*100;

    return $memory_usage;
}


// Insert values into the database
$sql = "INSERT INTO Performance (timestamp, cpu_1, cpu_5, cpu_15, ram)
VALUES (now(), " . number_format(get_server_memory_usage(), 2) . ", " . get_server_cpu_usage_array()[0] . ", " . get_server_cpu_usage_array()[1] . ", " . get_server_cpu_usage_array()[2] . ")";


try {
    $conn = new PDO("mysql:host=$mysqlHost;dbname=$mysqlDatabase", $mysqlUsername, $mysqlPassword);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // use exec() because no results are returned
    $conn->exec($sql);
    echo "New record created successfully";
    }
catch(PDOException $e)
    {
    echo $sql . "<br>" . $e->getMessage();
    }

// Close out the connection
$conn = null;

?>
