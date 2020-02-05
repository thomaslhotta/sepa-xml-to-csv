<?php

// Load xml string
$xml_file = $argv[1];
$xml = file_get_contents( $xml_file );

// Simple way to convert the xml to an array
$json = json_encode(simplexml_load_string( $xml ) );
$array = json_decode( $json, true );

// Strip unneeded info
$array =  $array['CstmrCdtTrfInitn']['PmtInf']['CdtTrfTxInf'];
$array = array_filter( $array, 'is_array' );
$array = array_filter(
    $array,
    function($row) {
        return array_key_exists( 'PmtId', $row );
    }
);

// Output csv to stdout
$handle = fopen('php://stdout', 'r+');
fputcsv( $handle, ['Cdtr','CdtrAcct','Amt'] );

foreach ( $array as $e ) {
    $values = [
        $e['Cdtr']['Nm'],
        $e['CdtrAcct']['Id']['IBAN'],
        $e['Amt']['InstdAmt'],
    ];

    fputcsv( $handle, $values );
}

fclose( $handle );