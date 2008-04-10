<?php
/**
 * Include the configuration file
 */
require_once '../inc/config.php';

echo '<img src="ups_logo.gif" /><br />';

// check if the form was submitted
if (!empty($_POST['submit']))
{
	$data = array(
		'origin' => array(
			'name' => $_POST['origin_name'],
			'street1' => $_POST['origin_street1'],
			'street2' => $_POST['origin_street2'],
			'city' => $_POST['origin_city'],
			'state' => $_POST['origin_state'],
			'zip_code' => $_POST['origin_zip_code'],
			),
		'destination' => array(
			'name' => $_POST['destination_name'],
			'street1' => $_POST['destination_street1'],
			'street2' => $_POST['destination_street2'],
			'city' => $_POST['destination_city'],
			'state' => $_POST['destination_state'],
			'zip_code' => $_POST['destination_zip_code'],
			),
		'pickup_date' => '',
	); // end $data
	
	$validation = new UpsAPI_TimeInTransit($data);
	$xml = $validation->buildRequest();
	
	// check the output type
	if ($_POST['output'] == 'array')
	{
		$response = $validation->sendRequest($xml, false);
		echo 'Response Output:<br />';
		var_dump($response);
	} // end if the output type is an array
	else
	{
		$response = $validation->sendRequest($xml, true);
		echo 'Response Output:<br />';
		echo '<pre>'.htmlentities($response).'</pre>';
	} // end else the output type is XML
	
} // end if the form has been submitted
else
{
?>
<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table border="0">
<tr>
	<td>&nbsp;</td>
	<td style="text-align: center; font-weight: bold;">Origin</td>
	<td style="text-align: center; font-weight: bold;">Destination</td>
</tr>
<tr>
	<td>
		<label for="origin_name">Name: </label>
	</td>
	<td>
		<input type="text" name="origin_name" id="origin_name" size="25"
			value="Camp Hill" />
	</td>
	<td>
		<input type="text" name="destination_name" id="destination_name"
			size="25" value="Camp Hill" />
	</td>
</tr>
<tr>
	<td>
		<label for="origin_street1">Street 1: </label>
	</td>
	<td>
		<input type="text" name="origin_street1" id="origin_street1" size="25"
			value="Camp Hill" />
	</td>
	<td>
		<input type="text" name="destination_street1" id="destination_street1"
			size="25" value="Camp Hill" />
	</td>
</tr>
<tr>
	<td>
		<label for="origin_street2">Street 2: </label>
	</td>
	<td>
		<input type="text" name="origin_street2" id="origin_street2" size="25"
			value="Camp Hill" />
	</td>
	<td>
		<input type="text" name="destination_street2" id="destination_street2"
			size="25" value="Camp Hill" />
	</td>
</tr>
<tr>
	<td>
		<label for="origin_city">City: </label>
	</td>
	<td>
		<input type="text" name="origin_city" id="origin_city" size="25"
			value="Camp Hill" />
	</td>
	<td>
		<input type="text" name="destination_city" id="destination_city"
			size="25" value="Camp Hill" />
	</td>
</tr>
<tr>
	<td>
		<label for="origin_state">State/Zip Code: </label>
	</td>
	<td>
		<input type="text" name="origin_state" id="origin_state" size="2"
			maxlength="2" value="PA" /> , 
		<input type="text" name="origin_zip_code" id="origin_zip_code" size="5"
			maxlength="5" value="17011" />
	</td>
	<td>
		<input type="text" name="destination_state" id="destination_state"
			size="2" maxlength="2" value="PA" />
		<input type="text" name="destination_zip_code" id="destination_zip_code"
			size="5" maxlength="5" value="17011" />
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td>
		<select name="output" id="output">
			<option value="array">Array</option>
			<option value="xml">XML</option>
		</select>
	</td>
	<td>
		<input type="hidden" id="submit" name="submit" value="1" />
		<input type="submit" />
	</td>
</tr>
</table>
</form>
<?php
} // end else the form has not been submitted

?>
