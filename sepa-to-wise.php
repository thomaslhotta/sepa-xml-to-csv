<?php

// Load xml string from first argument
$xml_file = $argv[1];
$xml = file_get_contents($xml_file);

// Get source and target currencies from the command line arguments
$sourceCurrency = $argv[2];
$targetCurrency = $argv[3];

// Convert the xml to an array
$json = json_encode(simplexml_load_string($xml));
$array = json_decode($json, true);

// Strip unneeded info
$array = $array['CstmrCdtTrfInitn']['PmtInf']['CdtTrfTxInf'];
$array = array_filter($array, 'is_array');
$array = array_filter(
    $array,
    function ($row) {
        return array_key_exists('PmtId', $row);
    }
);

// Output csv to stdout
$handle = fopen('php://stdout', 'w');

// Write the header row to match Wise template
fputcsv(
    $handle,
    [
        'name',
        'recipientEmail',
        'paymentReference',
        'receiverType',
        'amountCurrency',
        'amount',
        'sourceCurrency',
        'targetCurrency',
        'IBAN'
    ]
);

// Populate the CSV rows based on the SEPA XML
foreach ($array as $e) {
    // Extracting the Payment Reference from the XML
    $paymentReference = isset($e['RmtInf']['Ustrd']) ? $e['RmtInf']['Ustrd'] : '';

    $values = [
        $e['Cdtr']['Nm'],                // Name
        '',                              // recipientEmail (optional)
        $paymentReference,               // Payment Reference
        'PERSON',                        // receiverType (assuming this is always a PERSON)
        'target',                        // amountCurrency
        $e['Amt']['InstdAmt'],           // Amount
        $sourceCurrency,                 // sourceCurrency from argument
        $targetCurrency,                 // targetCurrency from argument
        $e['CdtrAcct']['Id']['IBAN']     // IBAN
    ];

    fputcsv($handle, $values);
}

fclose($handle);
