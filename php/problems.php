<?php


function open_problems(){
    global $mysqli;

    $colors = [
        'warning' => 'orange',
        'error' => 'red',
        'notice' => 'green',
    ];

    if($mysqli->connect_error){ echo "<tr><td colspan='4'>MySQL connection error</td></tr>"; return; }

    $qr = "
        SELECT
          type, since, problem, message
        FROM
          problem_log l
        WHERE
          state != 'resolved'
          AND
          stamp = (SELECT MAX(stamp) FROM problem_log WHERE id = l.id)
        ORDER BY since DESC;
    ";
    $res = $mysqli->query($qr);
    if ($res === FALSE) { exit("ERROR: MySQL query error"); }
    if ($res === FALSE) { echo "<tr><td colspan='4'>MySQL query error</td></tr>"; return; }
    $body  = "";
    while($row = $res->fetch_assoc()){
        $color = (isset($colors[$row['type']]) ? $colors[$row['type']] : '');
        $body .= "<tr>";
        $body .= "<td>${row['since']}</td>";
        $body .= "<td class='$color'>${row['type']}</td>";
        $body .= "<td>${row['problem']}</td>";
        $body .= "<td>${row['message']}</td>";
        $body .= "</tr>";
    }
    echo "<tr> <th>Since</th> <th>Type</th> <th>Problem</th> <th>Message</th> </tr>";
    echo $body;
}


?>
