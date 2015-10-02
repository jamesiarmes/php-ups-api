# Rates & Service Selection Examples #

**Rates & Service Selection Example** - With Customer Name
```
$shipment = array(
    'pickup_type' => array(
        'code' => $_POST['pickup_type'],
        'description' => $pickup_codes[$_POST ['pickup_type']],
    ),
    'service' => $_POST['service'],
    'packages' => array(
        array(
            'packaging' => array(
                'code' => 21,
                'description' => 'Express Box',
            ),
            'description' => 'Package from customer',
            'units' => 'LBS',
            'weight' => 100.0,
        ),
        array (
            'packaging' => array(
                'code' => '02',
                'description' => 'Package',
            ),
            'description' => 'Package from customer',
            'units' => 'LBS',
            'weight' => 23.6,
       )
    ),
    'saturday' => array(
        'pickup' => true,
        'deliver' => false,
    ),
    'pickup_day' => '02',
    'scheduling_method' => '02',
); // end $shipment

$shipper = array(
    'name' => 'Shipper Name',
    'phone' => '1234567890',
    'number' => null,
    'street' => 'Address Line1',
    'street2' => 'Address Line2',
    'city' => 'West Chester',
    'state' => 'PA',
    'zip' => '19380',
    'country' => 'US',
); // end $shipper

$ship_from = array(
    'name' => 'Ship F. Name',
    'phone' => '1234567890',
    'street' => 'Address Line1',
    'street2' => 'Address Line2',
    'street3' => 'Address Line3',
    'city' => 'Carlisle',
    'state' => 'PA',
    'zip' => '17013',
    'country' => 'US',
); // end $ship_from
	
$destination = array(
    'name' => 'Recipients Name',
    'phone' => '1234567890',
    'street' => 'Address Line',
    'city' => 'Duncannon',
    'state' => 'PA',
    'zip' => '17020',
    'country' => 'US'
); // end $destination

$rate = new UpsAPI_RatesAndService($shipment, $shipper, $ship_from, $destination);
$xml = $rate->buildRequest('Customer Name');

// returns an array
$response = $rate->sendRequest($xml, false);
```

###  ###
**Rates & Service Selection Example** - With Customer Data Array
```
$customer_data = array(
    'CustomerName' => 'Test Customer',
    'Product' => 'Ebay Shipment Tracker 2.0',
    'Location' => 'Camp Hill, PA',
); // end $customer_data

$rate = new UpsAPI_RatesAndService($shipment, $shipper, $ship_from, $destination);
$xml = $rate->buildRequest($customer_data);

// returns XML
$response = $rate->sendRequest($xml, true);
```

###  ###
**getPackageCharges()** - Returns charges for each package
```
$rate = new UpsAPI_RatesAndService($shipment, $shipper, $ship_from, $destination);
$xml = $rate->buildRequest($customer_data);

$response = $rate->sendRequest($xml, true);
$package_charges = $validation->getPackageCharges();
```
Returns an array:
```
array
  0 => 
    array
      'currency_code' => 'USD'
      'transportation' => '200.28'
      'service_options' => '23.00'
      'total' => '223.28'
  1 => 
    array
      'currency_code' => 'USD'
      'transportation' => '81.20'
      'service_options' => '15.00'
      'total' => '96.20'
```

###  ###
**getShipmentCharges()** - Returns charges for the entire shipment
```
$rate = new UpsAPI_RatesAndService($shipment, $shipper, $ship_from, $destination);
$xml = $rate->buildRequest($customer_data);

$response = $rate->sendRequest($xml, true);
$package_charges = $validation->getShipmentCharges();
```
Returns an array:
```
array
  'currency_code' => 'USD'
  'transportation' => '281.48'
  'service_options' => '38.00'
  'total' => '319.48'
```

###  ###
**getPackageWeight()** - Returns charges for each package
```
$rate = new UpsAPI_RatesAndService($shipment, $shipper, $ship_from, $destination);
$xml = $rate->buildRequest($customer_data);

$response = $rate->sendRequest($xml, true);
$package_charges = $validation->getPackageWeight();
```
Returns an array:
```
array
  0 => 
    array
      'weight' => '100.0'
      'units' => 'LBS'
  1 => 
    array
      'weight' => '24.0'
      'units' => 'LBS'
```

###  ###
**getShipmentWeight()** - Returns billing weight for the entire shipment
```
$rate = new UpsAPI_RatesAndService($shipment, $shipper, $ship_from, $destination);
$xml = $rate->buildRequest($customer_data);

$response = $rate->sendRequest($xml, true);
$package_charges = $validation->getShipmentWeight();
```
Returns an array:
```
array
  'weight' => '124.0'
  'units' => 'LBS'
```