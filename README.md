How to use this package

1. Run the migration
2. A new table named `export_configs` will be generated
3. Insert a new data to the table to create a new report
4. create an instance of class `Rudi9277/ExportDb/GenerateReport` and call `generate` function
5. Pass the request to the function as a parameter

Table columns

1. module => the name of the module that will be exported
2. title => the title of the sheet in the excel that will be generated
3. query => the query that will be used to generate excel
4. formatter => json object that will help format the data the way you want

Example
[
{
"name" : "Product",
"value" : "--- {product_name} ----"
}
]

\*note :
"name" `key` is the header in the excel that will be generated
{product_name} is the column name in the sql query result

5. validator => json object that will help to validate the required data in for the query. The validator is from [Laravel Validator](https://laravel.com/docs/11.x/validation)

Example

{"name":"required|string"}

6. default => a default json object that will set what is the default value of the validator in No.5

Example
{"name":null}
