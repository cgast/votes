<?php

    // Include to get the Voting File
    // Voting File is either passed by querystring or a
    // generic file based on the date

    $votingUrl = "";

    if ( isset($_GET["file"]) ) {

        // übergebenes file nehmen (für alte votings)
        $votingUrl = "./votings/" . $_GET["file"] . ".json";

    } else {

        // zeitzone setzen
        date_default_timezone_set('Europe/Berlin');
        // file für heutigen tag nehmen
        $votingUrl = "votings/vote_" . date("Y_m_d") . ".json";

    }

?>