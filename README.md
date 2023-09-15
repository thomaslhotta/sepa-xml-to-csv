# SEPA-XML-to-CSV
A simple PHP script that converts SEPA XML files (pain.001) to CSV.

## Usage:
### Sepa to CSV

Converts sepa.xml to CSV and saves it to sepa.csv:   
```
php sepa-to-csv.php sepa.xml > sepa.csv
```

### Sepa to Wise CSV format
```
 php sepa-to-wise.php mars-uk-sepa-2023-09-15.xml EUR GBP
```