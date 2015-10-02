# Time In Transit Examples #

**Time In Transit Example** - With Customer Name
```
$origin = array(
    'name' => $_POST['origin_name'],
    'street_number' => $_POST['origin_street_number'],
    'street' => $_POST['origin_street'],
    'street_type' => $_POST['origin_street_type'],
    'city' => $_POST['origin_city'],
    'state' => $_POST['origin_state'],
    'zip_code' => $_POST['origin_zip_code'],
    'country' => $_POST['origin_country'],
); // end $origin

$destination = array(
    'name' => $_POST['destination_name'],
    'street_number' => $_POST['destination_street_number'],
    'street' => $_POST['destination_street'],
    'street_type' => $_POST['destination_street_type'],
    'city' => $_POST['destination_city'],
    'state' => $_POST['destination_state'],
    'zip_code' => $_POST['destination_zip_code'],
    'country' => $_POST['destination_country'],
); // end $destination

$data = array(
        'pickup_date' => $_POST['pickup_date'],
        'max_list_size' => $_POST['max_list_size'],
        'invoice' => array(
            'currency_code' => $_POST['currency_code'],
            'monetary_value' => $_POST['monetary_value'],
        ), // end pickup_date
        'weight' => array(
        'unit_of_measure' => array(
            'code' => $_POST['weight_um'],
            'desc' => $_POST['weight_desc'],
        ), // end unit_of_measure
        'weight' => $_POST['weight'],
    ), // end weight
); // end $data

$time_in_transit = new UpsAPI_TimeInTransit($origin, $destination, $data);
$xml = $time_in_transit->buildRequest('Customer Name');

// returns an array
$response = $time_in_transit->sendRequest($xml, false);
```

###  ###
**Time In Transit Example** - With Customer Data Array
```
$customer_data = array(
    'CustomerName' => 'Test Customer',
    'Product' => 'Ebay Shipment Tracker 2.0',
    'Location' => 'Camp Hill, PA',
); // end $customer_data

$time_in_transit = new UpsAPI_TimeInTransit($origin, $destination, $data);
$xml = $time_in_transit->buildRequest($customer_data);

// returns XML
$response = $time_in_transit->sendRequest($xml, true);
```

###  ###
**getNumberOrServices()** - To get the number of packages:
```
$time_in_transit = new UpsAPI_TimeInTransit($origin, $destination, $data);
$xml = $time_in_transit->buildRequest($customer_data);

$time_in_transit->sendRequest($xml, true);
$services_count = $time_in_transit->getNumberOrServices();
```
Returns an integer:
```
7
```

###  ###
**getservices()** - To get the status of each package:
```
$time_in_transit = new UpsAPI_TimeInTransit($origin, $destination, $data);
$xml = $time_in_transit->buildRequest($customer_data);

$time_in_transit->sendRequest($xml, true);
$services_array = $time_in_transit->getservices();
```
Returns an array:
```
array
  0 => 
    array
      'service' => 
        array
          'code' => '1DM'
          'description' => 'UPS Next Day Air Early A.M.'
      'guaranteed' => 'yes'
      'estimated_arival' => 
        array
          'days' => '1'
          'time' => '08:00:00'
          'pickup' => '2008-04-21'
          'date' => '2008-04-22'
          'day' => 'TUE'
  1 => 
    array
      'service' => 
        array
          'code' => '1DA'
          'description' => 'UPS Next Day Air'
      'guaranteed' => 'yes'
      'estimated_arival' => 
        array
          'days' => '1'
          'time' => '10:30:00'
          'pickup' => '2008-04-21'
          'date' => '2008-04-22'
          'day' => 'TUE'
```