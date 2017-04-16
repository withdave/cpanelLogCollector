<?php

// This file is designed to collect relevant CPanel statistics and present them externally for collection by an external service
// At the moment it simply collects data as per http://stackoverflow.com/questions/41629460/how-to-get-cpu-ram-usage-from-differenet-servers
// The ambition is to bring in logging data from another source and load to a MySQL database


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


// Print out a table containing the relevant information
echo
'<table>
  <td>
    <tr>
      <th>CPU (1 minute)</th>
      <th>CPU (5 minutes)</th>
      <th>CPU (15 minutes)</th>
      <th>RAM consumption</th>
    </tr>
    <tr>
      <td>' . get_server_cpu_usage_array()[0] . '</td>
      <td>' . get_server_cpu_usage_array()[1] . '</td>
      <td>' . get_server_cpu_usage_array()[2] . '</td>
      <td>' . number_format(get_server_memory_usage(), 2) . '</td>
    </tr>
  </td>
</table>';

/*
echo '<br><h4>Server CPU usage: '     . get_server_cpu_usage(0) . '% </h4><div class="meter"><span style="width:' . get_server_cpu_usage(0) . '%"></span></div><br>
<h4>Server Memory usage: ' . number_format(get_server_memory_usage(), 2) . '%</h4><div class="meter"><span style="width:' . get_server_memory_usage() . '%"></span></div>';
*/

?>
