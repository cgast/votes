<?php 

    include( "inc/getVotingUrl.php");
    include( "inc/header.html" );

?>
<script>

    $(function () {

        var displayChart = function( votes ) {

            var projects = [], data = [], i= 0, ids = {};

            for (var project_name in votes) {
                projects[i] = project_name;
                data[i] = 0;
                for (var id in votes[project_name]) {
                    ids[id] = 1;
                    data[i] += parseInt("0" + votes[project_name][id]);
                }
                i++;
            }

            console.log(projects, data, ids);

            $('#votecount').html( Object.keys(ids).length );
            $('#barchart').highcharts({
                chart: {
                    type: 'bar',
                    animation: 'false'
                },
                title: {
                    text: '',
                    style: 'color:#fff'
                },
                xAxis: {
                    categories: projects,
                    title: {
                        text: null
                    }
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Invest in €',
                        align: 'high'
                    },
                    labels: {
                        overflow: 'justify'
                    }
                },
                tooltip: {
                    valueSuffix: ' €'
                },
                plotOptions: {
                    bar: {
                        dataLabels: {
                            enabled: true
                        }
                    }
                },
                legend: {
                    enabled: false
                },
                credits: {
                    enabled: false
                },
                series: [{
                    name: "Investment",
                    data: data
                }],
                exporting: {
                    enabled: false
                }
            });

        };


        // read json first time
        var votingUrl = '<?=$votingUrl?>'; // PHP hack
        console.log(votingUrl);

        // first display
        $.getJSON( votingUrl, function( data ) {
            displayChart( data.votes );
        });

        // repeat for ever
        window.setInterval( function () {
            $.getJSON( votingUrl, function( data ) {
                if ( data.running ) {
                    displayChart( data.votes );
                }
            });
        } , 5000);

    });


</script>

<div class="container">

    <p>
        <div class="jumbotron" id="barchart" style="min-width: 200px; height: 400px; margin: 0 auto"></div>
    </p>

    <div class="footer">
        <p>Votes collected:<span id="votecount"></span></p>
    </div>

</div> <!-- /container -->
</body>
</html>
