# Tracking Examples #

**Tracking Example** - With Customer Name
```
$tracking = new UpsAPI_Tracking($tracking_number);
$xml = $tracking->buildRequest('Customer Name');

// returns an array
$response = $tracking->sendRequest($xml, false);
```

###  ###
**Tracking Example** - With Customer Data Array
```
$customer_data = array(
    'CustomerName' => 'Test Customer',
    'Product' => 'Ebay Shipment Tracker 2.0',
    'Location' => 'Camp Hill, PA',
); // end $customer_data

$tracking = new UpsAPI_Tracking($tracking_number);
$xml = $tracking->buildRequest($customer_data);

// returns XML
$response = $tracking->sendRequest($xml, true);
```

###  ###
**getNumberOfPackages()** - To get the number of packages:
```
$tracking = new UpsAPI_Tracking($tracking_number);
$xml = $tracking->buildRequest($customer_data);

$tracking->sendRequest($xml, true);
$package_qty = $tracking->getNumberOfPackages();
```
Returns an integer:
```
2
```

###  ###
**getPackageStatus()** - To get the status of each package:
```
$tracking = new UpsAPI_Tracking($tracking_number);
$xml = $tracking->buildRequest($customer_data);

$tracking->sendRequest($xml, true);
$status_array = $tracking->getPackageStatus();
```
Returns an array:
```
array
  0 => 
    array
      'code' => 'D'
      'description' => 'DELIVERED'
  1 => 
    array
      'code' => 'M'
      'description' => 'BILLING INFORMATION RECEIVED. SHIPMENT DATE PENDING.'
```

###  ###
**getShippingAddress()** - To get the package(s)' shipping address
```
$tracking = new UpsAPI_Tracking($tracking_number);
$xml = $tracking->buildRequest($customer_data);

$tracking->sendRequest($xml, true);
$shipping_address = $tracking->getShippingAddress();
```
Returns an array:
```
array
  'address1' => 'SAMPLE CONSIGNEE'
  'address2' => '1307 PEACHTREE STREET'
  'city' => 'ANYTOWN'
  'state' => 'GA'
  'zip_code' => '30340'
  'country' => 'US'
```

###  ###
**getShippingMethod()** - To get the shipping method of the package(s)
```
$tracking = new UpsAPI_Tracking($tracking_number);
$xml = $tracking->buildRequest($customer_data);

$tracking->sendRequest($xml, true);
$shipping_method = $tracking->getShippingMethod();
```
Returns an array:
```
array
  'code' => '002'
  'description' => '2ND DAY AIR'
```