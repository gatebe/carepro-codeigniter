<!DOCTYPE html>
<html>

<head>
    <title>Daycare Invitation</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div>
        <p>Hello <?php echo $first_name.' '.$last_name; ?>,</p>
        <p>Your child <?php echo $child_first_name . ' '; ?><?php echo $child_last_name; ?> has been registered sucessfully for Daycarepro app</p>
        Visit Daycarecarepro: <a href="<?php echo base_url(); ?><?php echo $daycare_id ?>/dashboard">
        <?php echo base_url(); ?><?php echo $daycare_id ?>/dashboard</a><br/><br/>
        Thanks!<br />
        Team Daycarepro
        </a>
    </div>
</body>
</html>