<?php
	session_start();
	if(!$_SESSION['username']) {
		header('location: login.php');
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title> Form Submit </title>
		<link rel="stylesheet" type="text/css" href="style.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	</head>
	<body>

	<div class="nav">
		<nav>
			<a href="calendar.php">Calendar</a>
			<a href="input.php">Input</a>
			<a href="admin.php">Admin</a>
			<a href="logout.php">Log out</a>
		</nav>
	</div>

	<p align="center" style="font-size: 25px;color: white; font-weight: bold;">
		Logged  in as <?php echo $_SESSION['username']?></p>
  <?php
		function clear_events()
		{
			file_put_contents('calendar.txt', '[]');
			header('location: calendar.php');
		}

		if (isset($_POST['clear'])) {
	    clear_events();
  	}

		if(!session_id()) {
			header('location: login.php');
		}

		$errors_exist = False;
		$name = $_POST['eventname'];
    $start = $_POST['starttime'];
    $end = $_POST['endtime'];
    $location = $_POST['location'];
    $day = $_POST['day'];

		$time_error = False;
		$location_error = False;

		$days = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday");

		if(!$name || !$location || !$start || !$end)
    {
      $errors_exist = True;
    }

		if(!in_array($day, $days) || !$day)
		{
			$errors_exist = True;
			$day = null;
		}

		if (preg_match("/[\^£$%&*()}{@#~?!><>|=_+¬-]/", $location))
		{
				$errors_exist = True;
				$location_error = True;
		}

		if($start > $end)
		{
			$errors_exist = True;
			$time_error = True;
		}

		if(!$errors_exist)
    {
			$new_event = array('event_name' => $name,
	            'start_time' => $start,
	            'end_time' => $end,
	            'location' => $location,
	            'day' => $day);

	    $file_contents = file_get_contents('calendar.txt');
	    $events = json_decode($file_contents, true);
	    array_push($events, $new_event);
	    $updated_events = json_encode($events);
	    file_put_contents('calendar.txt', $updated_events);

			header("Location: calendar.php");
			exit();
		}
  ?>

	<form method="post" action="input.php">

    <div id="error-msg-container">
      <?php if(!$name) echo "<span>\nPlease provide a value for Event Name.\n</span> <br/>";?>
      <?php if(!$start) echo "<span>\nPlease provide a value for Start Time.\n</span> <br/>";?>
      <?php if(!$end) echo "<span>\nPlease provide a value for End Time.\n</span> <br/>";?>
      <?php if(!$location) echo "<span>\nPlease provide a value for Location Name.\n</span> <br/>";?>
			<?php if($location_error) echo "<span>\nLocation can not include any special characters.\n</span> <br/>";?>
      <?php if(!$day) echo "<span>\nPlease select a value from the options for Day of the Week.\n</span> <br/>";?>
      <?php if($time_error) echo "<span>\nThe Start Time can not be before the End Time\n</span> <br/>";?>
    </div>

		<div id="form-container">
			<table class="content-table">

				<caption><h1> Submit an event! </h1></caption>

				<tr>
					<td>Event Name:</td>
					<td>
						<input name="eventname" type="text" size=25 maxlength="30">
					</td>
				</tr>
				<tr>
					<td>Start Time:</td>
					<td>
						<input name="starttime" type="time">
					</td>
				</tr>
				<tr>
					<td>End Time</td>
					<td>
						<input name="endtime" type="time">
					</td>
				</tr>
				<tr>
					<td>Location:</td>
					<td>
						<input name="location" type="text" size="70" maxlength="75">
					</td>
				</tr>
				<tr>
					<td>Day of the week:</td>
					<td>
						<input name="day" list="days">
						<datalist id="days">
							<option value="Monday">
							<option value="Tuesday">
							<option value="Wednesday">
							<option value="Thursday">
							<option value="Friday">
						</datalist>
					</td>
				</tr>
				<tr>
					<td colspan="2" class="center">
						<input type="submit" value="Submit">
						<input type="submit" class="clear-btn" name="clear" value="Clear" />
					</td>
				</tr>
			</table>
		</div>

	</form>

	</body>
</html>
