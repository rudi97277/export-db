install this package using
`composer require rudi97277/export-db`

## How to Use This Package

1. **Run the Migration**

   - Execute the migration to create the necessary database structure.

2. **Table Creation**

   - A new table named `export_configs` will be generated.

3. **Insert Data**

   - Insert new data into the `export_configs` table to create a new report.

4. **Create a New Route**

   - Define a new route that creates an instance of the `Rudi9277/ExportDb/GenerateReport` class and calls the `generate` function. Pass the `request` as a parameter.

   **Example route definition:**

   ```php
   Route::get('export', function () {
       return Rudi9277\ExportDb\GenerateReport::generate(request());
   });
   ```

5. **Run Symlink**

   - If you never run a symlink command before, please run it `php artisan storage:link`

**Note:**

- Available `export_type` are `xlsx` and `csv`.
- You can create a new module in the database.

**Table columns:**

1. **module**: The name of the module that will be exported.
2. **title**: The title of the sheet in the Excel that will be generated.
3. **query**: The query that will be used to generate the Excel.
4. **formatter**: JSON object that will help format the data the way you want.

   **Example:**

   ```json
   [
     {
       "name": "Product",
       "value": "--- {product_name} ----"
     }
   ]
   ```

   **Note:**

   - The `"name"` key is the header in the Excel that will be generated.
   - `{product_name}` is the column name in the SQL query result.

5. **validator**: JSON object that will help to validate the required data for the query. The validator is from [Laravel Validator](https://laravel.com/docs/11.x/validation).

   **Example:**

   ```json
   {
     "name": "required|string"
   }
   ```

6. **default**: A default JSON object that sets what the default value of the validator in No. 5 is.

   **Example:**

   ```json
   {
     "name": null
   }
   ```

7. To style the data, you can use ExportDTO to pass callable function to the generator like this example

   ```php
   Route::get('export', function () {
        $style = function (Worksheet $sheet) {
            return [
                'A' => [
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ],
            ];
        };

        $reg = function () {
            return [
                AfterSheet::class => function ($event) {
                    $sheet = $event->sheet->getDelegate();
                    $sheet->getStyle('B:B')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                },
            ];
        };

        $dto = new ExportDTO($func, $style);

        return  Rudi97277\ExportDb\GenerateReport::generate(request());
   });
   ```

   please check [Laravel Excel](https://laravel-excel.com/) how to use the styles
