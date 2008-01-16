<?php
/**
 * Include the configuration file
 */
require_once 'inc/config.php';

if (!empty($_POST['submit']))
{
	$tracking_number = $_POST['tracking_number'];
	
	$tracking = new UpsAPI_Tracking($tracking_number);
	$xml = $tracking->buildRequest();
	$response = $tracking->sendRequest($xml, true);
	
	var_dump($response);
}
else
{
?>
<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="text" name="tracking_number" id="tracking_number" size="25" value="1Z12345E0291980793" />
	<input type="hidden" id="submit" name="submit" value="1" />
	<input type="submit" />
</form>
<?php
}

?>