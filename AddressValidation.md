# Address Validation Examples #

**Address Validation Example** - With Customer Name
```
$address = array(
    'city' => 'Duncannon',
    'state' => 'PA',
    'zip_code' => 17020,
); // end address

$validation = new UpsAPI_USAddressValidation($address);
$xml = $validation->buildRequest('Customer Name');

// returns an array
$response = $validation->sendRequest($xml, false);
```

###  ###
**Address Validation Example** - With Customer Data Array
```
$customer_data = array(
    'CustomerName' => 'Test Customer',
    'Product' => 'Ebay Shipment Tracker 2.0',
    'Location' => 'Camp Hill, PA',
); // end $customer_data

$validation = new UpsAPI_USAddressValidation($address);
$xml = $validation->buildRequest($customer_data);

// returns XML
$response = $validation->sendRequest($xml, true);
```

###  ###
**getMatchType()** - To get what type of match was returned
```
$validation = new UpsAPI_USAddressValidation($address);
$xml = $validation->buildRequest($customer_data);

$validation->sendRequest($xml, true);
$match_type = $validation->getMatchType();
```
Returns a string:
```
'Multiple Partial'
```

###  ###
**getMatches()** - To get the different matches
```
$validation = new UpsAPI_USAddressValidation($address);
$xml = $validation->buildRequest($customer_data);

$validation->sendRequest($xml, true);

// can be called with no arguments
$match_array = $validation->getMatches();

// can also be called with the results of getMatchType()
$match_type = $validation->getMatchType();
$match_array = $validation->getMatches($match_type);
```
Returns an array:
```
array
  0 => 
    array
      'quality' => '0.9125'
      'address' => 
        array
          'city' => 'CAMP HILL'
          'state' => 'PA'
      'zip_code_low' => '17001'
      'zip_code_high' => '17001'
  1 => 
    array
      'quality' => '0.9125'
      'address' => 
        array
          'city' => 'CAMP HILL'
          'state' => 'PA'
      'zip_code_low' => '17011'
      'zip_code_high' => '17012'
```