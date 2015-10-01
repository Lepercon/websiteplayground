<?php $team = $_GET['judge'];?>
<html>
    <head>
        <title>Family Fortunes - Judge <?php echo $team; ?></title>
        <script type="text/javascript" src="jquery.min.js"></script>
        <script type="text/javascript" src="judge.js"></script>
    </head>
    <body style="font-family: Trebuchet MS; background-color: #c80000; color: #eeb300; font-size: 3em; text-align: center;">
    <h1>Family Fortunes</h1>
    <p id="result">Judge <?php echo $team; ?> - Click to Buzz</p>
    <span style="display:none" id="team-no"><?php echo $team; ?></span>
    </body>
</html>