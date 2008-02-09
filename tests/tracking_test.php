<?php
/**
 * Include the configuration file
 */
require_once '../inc/config.php';

// check if the form was submitted
if (!empty($_POST['submit']))
{
	$tracking_number = $_POST['tracking_number'];
	
	$tracking = new UpsAPI_Tracking($tracking_number);
	$xml = $tracking->buildRequest();
	
	// check the output type
	if ($_POST['output'] == 'array')
	{
		$response = $tracking->sendRequest($xml, false);
		var_dump($response);
	} // end if the output type is an array
	else
	{
		$response = $tracking->sendRequest($xml, true);
		echo '<pre>'.htmlentities($response).'</pre>';
	} // end else the output type is XML
	
} // end if the form has been submitted
else
{
?>
<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="text" name="tracking_number" id="tracking_number" size="25"
		value="1Z12345E0291980793" />
	<select name="output" id="output">
		<option value="array">Array</option>
		<option value="xml">XML</option>
	</select>
	<input type="hidden" id="submit" name="submit" value="1" />
	<input type="submit" />
</form>
<?php
} // end else the form has not been submitted

?>