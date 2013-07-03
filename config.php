<?php

    include( "inc/getVotingUrl.php");

    if ( !file_exists($votingUrl) ) {

        // create new voting file if none exists
        file_put_contents($votingUrl, json_encode(array("running"=>false,"votes"=>array())));

    }

    if ( isset($_POST["action"]) ) {

        // json datei einlesen
        $json = json_decode(file_get_contents( $votingUrl ), true);

        if ( $_POST["action"] == "Save" ) {
            $i = 0; $keys = array_keys($json["votes"]);

            while( isset( $_POST[ "project_" . $i ] )) {
                $new_name = $_POST[ "project_" . $i];
                $old_name = $keys[$i];

                if ( $old_name != $new_name ) {
                    if ( $new_name != "" ) {
                        $json["votes"][$new_name] =  $json["votes"][$old_name];
                    }
                    unset($json["votes"][$old_name]);
                }

                $i++;
            }

            if ( $_POST["project_new"] != "" ) {
                $json["votes"][$_POST["project_new"]] = array();
            }

        }

        if ( $_POST["action"] == "Reset" ) {

            $keys = array_keys($json["votes"]);
            foreach( $json["votes"] as $p => $v ) {
                $json["votes"][$p] = array();
            }

        }

        if (( $_POST["action"] == "Autoreload: On" ) || ( $_POST["action"] == "Autoreload: Off" )){

            $json["running"] = !($json["running"]);

        }

        // json schreiben
        file_put_contents($votingUrl, json_encode($json));

    }


    $data = json_decode(file_get_contents( $votingUrl ), true);

    // ---------------------------------------------------------------------

    include( "inc/header.html" );

?>

    <script>

    $(function () {

        // read json first time
        var votingUrl = '<?=$votingUrl?>'; // PHP hack
        console.log(votingUrl);

        // first display
        $.getJSON( votingUrl, function( data ) {

            console.log(data.votes);
            votes = data.votes;

            var projects = [], invest = [], i= 0, ids = {};

            for (var project_name in votes) {
                projects[i] = project_name;
                invest[i] = 0;

                var votes_here = 0;
                for (var id in votes[project_name]) {
                    ids[id] = 1;
                    invest[i] += parseInt(votes[project_name][id]);
                    votes_here++;
                }

                // add project as input field to table
                var row = $("<tr>");
                row.append("<td><input style='width:180px' type=text id='project_" + i + "' name='project_" + i + "' value='" + project_name + "'></td>");
                row.append("<td>" + invest[i] + " €</td>");
                $('#projects').append(row);

                i++;
            }

            $('#projects').append("<td><input style='width:180px' type=text id=project_new name=project_new value=''></td><td></td>");
            // add aditional row for new project

            $('#votes_counted').html(Object.keys(ids).length);
            console.log(projects, invest, ids);

        });


    });


</script>

    <div class="container">

        <form action="#" method="post">

            <div class="masthead">
                <h3 class="muted">Voting Administration</h3>
            </div>

            <div class="jumbotron">
                    <p class="lead">
                        <table class=table id="projects">
                        </table>
                    </p>

                    <p>
                        <input type="submit" class="doFreeze btn btn-large btn-success btn-block full-width " name="action" value="Save">
                    </p>
                    <p>
                        <input type="submit" class="doReset btn btn-small btn-danger" name="action" value="Reset">
                        <input type="submit" class="doFreeze btn btn-small btn-info" name="action" value="<?
                        if ( $json["running"] ) {
                            echo "Autoreload: On";
                        } else {
                            echo "Autoreload: Off";
                        }
                        ?>">
                    </p>

            </div>

        </form>

        <hr>

        <div class="footer">
            <p>&copy; cgast <span class=muted>| <?=$votingUrl?> | <span id=votes_counted></span> votes</span></p>
        </div>

    </div> <!-- /container -->

</body>
</html>
