<?php $thisPage = "displayIsolate" ?>

<?php include("../meta.php");?>
<link rel="stylesheet" href="css/displayIsolate.css" type="text/css" />

</head>

<body>

<?php include("../header.php"); ?>

<div id="isolate_display">
   <?php echo '<h1 id="title">Isolate: '.$_GET['isolateName'].'</h1>'; ?>
</div>

<?php include("isolateMetaInfo.php"); ?>

</body>
</html>
