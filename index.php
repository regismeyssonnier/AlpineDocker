<?php
 error_reporting(E_ALL);

    $pgsql_conn  =pg_connect("host=127.0.0.1 port=5432 dbname=eidb user=regis password=rmeysso");
    if ($pgsql_conn) {
        print "Successfully connected to database: " . pg_dbname($pgsql_conn) .
            " on " .  pg_host($pgsql_conn) . "<br/>\n";
    } else {
        print pg_last_error($pgsql_conn);
        
    }

    echo pg_dbname(); // affiche marie

        /*$result = pg_query($pgsql_conn, "SELECT auteur, email FROM auteurs");
    if (!$result) {
        echo "Une erreur s'est produite.\n";
        exit;
    }*/
    

//$strJsonFileContents = file_get_contents("data_sensors.json");
    // Convert to array 
    //$json = '{"a":"\'\ucb0 \'","b":2,"c":3,"d":4,"e":5}';
    $array = json_decode($json, true);

    print "The array is : <br>";
    var_dump($array);

    foreach($array[0] as $x => $x_value) {
  echo "name=" . $x ;//. ", Value=" . $x_value;
  echo "<br>";
}

	


?>