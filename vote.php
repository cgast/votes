<?

    include( "inc/getVotingUrl.php");

    // voting file da? wenn nicht dann "heute kein voting anzeigen"
    if ( !file_exists($votingUrl) ) {

        include("not_ready.php");
        die(0);

    }

    // wenn keine cookie gesetzt ist, dann setzen mit gesamtsumme

    $to_invest = 1000000;
    $user_id = uniqid();


    if ( isset( $_POST["toinvest"]) )  {

        $user_id = $_COOKIE["voting_id"];
        $to_invest = $_POST["toinvest"];

        // json datei einlesen
        $json = json_decode(file_get_contents( $votingUrl ), true);

        foreach( $_POST["projects"] as $name ) {

            $newname = str_replace(" ", "_", $name);
            $to_invest -= $_POST[$newname];

            if ( isset($json["votes"][$name][$user_id]) ) {
                $json["votes"][$name][$user_id] +=  $_POST[str_replace(" ", "_", $name)];
            } else {
                $json["votes"][$name][$user_id] = $_POST[str_replace(" ", "_", $name)];
            }
        }

        // json schreiben
        file_put_contents($votingUrl, json_encode($json));

        // alle beträge von toinvest abzeiehne neu berechnen und im cookie speichern
        setcookie("voting_invest", $to_invest );

    } else {


        if ( !isset($_COOKIE["voting_id"]) ) {

            $expire=time()+60*60*24*30;
            setcookie("voting_invest", $to_invest, $expire);
            setcookie("voting_id", $user_id , $expire);

        } else {

            $user_id = $_COOKIE["voting_id"];

            $json = json_decode(file_get_contents( $votingUrl ), true);

            foreach( $json["votes"] as $name => $votes) {
                if ( isset($json["votes"][$name][$user_id]) ) {
                    $to_invest -= intval($json["votes"][$name][$user_id]);
                }
            }

            setcookie("voting_invest", $to_invest );

        }

    }

    $data = json_decode(file_get_contents( $votingUrl ), true);

    include( "inc/header.html" );

// ---------------------------------------------------------------------

?>

<script>

    $(function () {

        // read json first time
        var votingUrl = '<?=$votingUrl?>';
        var votingUserId = '<?=$user_id?>';
        var votingInvest = <?=$to_invest?>;
        var thisRound = [];

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
                    invest[i] += votes[project_name][id];
                    votes_here++;
                }

                thisRound[i] = 0;

                i++;
            }

            // add aditional row for new project

            $('#votes_counted').html(Object.keys(ids).length);
            $('#sum').html( votingInvest );
            $('#userId').html( votingUserId );

            console.log(projects, invest, ids);

            $('.investing').on("change", function() {
                $( ".investing" ).each(function( index ) {
                    console.log( index + ": " + $(this).val() );
                    var i = $(this).attr("id");
                    console.log("i",i);

                    if (( $(this).val() - thisRound[i] ) <= votingInvest ) {
                        votingInvest = votingInvest - ( $(this).val() - thisRound[i] );
                        thisRound[i] = $(this).val();
                        $('#sum').html( votingInvest );
                    } else {
                        $(this).val(thisRound[i]);
                    }
                });

            });


        });


    });


</script>

<div class="container">

    <div>
        <h3>Invest <span id=sum><?=$to_invest?></span> €</h3>
        <p class="lead">
            <form action="<?=$_SERVER["SCRIPT_NAME"]?>" method="post">
                <input type="hidden" name="toinvest" value="<?=$to_invest?>">
                <table class=table id="votes">
                    <?

                    $i = 0;
                    foreach( $data["votes"] as $project => $votes ) {

                        echo "<input type=hidden name='projects[]' value='" . $project . "'>";
                        echo "<tr><td>" . $project . "</td><td><input style='width:120px;height:2em;font-size:2em' class='investing' type=text name='" . $project . "' id='" . $i++ . "'></td><td>€</td></tr>";

                    }
                    ?>
                </table>
                <input type="submit" class="btn btn-block full-width btn-large btn-danger" value="Invest">
            </form>
        </p>
    </div>

</div> <!-- /container -->

</body>
</html>
