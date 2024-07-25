<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CrudGeneratorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:crud
            {name : The name of the model. ex: ModelName}
            {--table= : The name of the table}
            {--force : Overwrite existing all CRUD files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simple CRUD API command';

    /**
     * @var File
     */
    private $file;

    /**
     * Create a new command instance.
     */
    public function __construct(Filesystem $file)
    {
        parent::__construct();

        $this->file = $file;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (! $this->isPascalCase()) {
            $this->error('The model name is not in PascalCase.');

            return;
        }

        $tableName = $this->tableName();

        if (! Schema::hasTable($tableName)) {
            $this->error("The table {$tableName} does not exist.");

            return;
        }

        $this->info('Generate controller...');
        $this->controller();

        $this->info('Generate model...');
        $this->model();

        $this->info('Generate request...');
        $this->request();

        $this->info('Generate resource...');
        $this->resource();

        $this->info('Generate test...');
        $this->factory();
        $this->test();

        $routeName = $this->routeName();

        $controllerName = $this->argument('name').'Controller::class';

        $this->info('Append route resources...');
        $routeContent = "\nRoute::get('{$routeName}/schema', [\\App\\Http\Controllers\\{$controllerName}, 'schema']);\n";
        $routeContent .= "Route::resource('{$routeName}', \\App\\Http\\Controllers\\{$controllerName});";

        File::append(
            base_path('routes/api.php'),
            $routeContent
        );

        $this->info('CRUD '.$this->argument('name').' successfully created.');
    }

    protected function isPascalCase()
    {
        return preg_match('/^[A-Z][a-zA-Z]*$/', $this->argument('name'));
    }

    protected function getStub($type)
    {
        return file_get_contents(__DIR__."/stubs/crud.{$type}.stub");
    }

    public function routeName()
    {
        return strtolower($this->plural(Str::kebab($this->argument('name'))));
    }

    protected function plural($name)
    {
        return Str::plural($name);
    }

    /**
     * Create table name
     *
     * @return string
     */
    protected function tableName(?bool $with_prefix = false)
    {
        if ($this->option('table')) {
            return ($with_prefix ? env('APP_PREFIX_SERVICE') : '').$this->option('table');
        }

        $tableName = Str::plural(Str::snake($this->argument('name')));

        return ($with_prefix ? env('APP_PREFIX_SERVICE') : '').$tableName;
    }

    public function columnRequired()
    {
        $tableName = $this->tableName(true);
        $primaryKey = $this->primaryKeyColumn($tableName);
        $columns = $this->tableDetails($tableName);
        $excludedColumns = ['created_at', 'updated_at', 'deleted_at'];

        $requiredColumns = [];
        foreach ($columns as $column) {
            $col = $column->column_name;  // Access column_name property

            if (in_array($col, $excludedColumns) || $col === $primaryKey) {
                continue;
            }

            if ($column->is_nullable === 'YES') {  // Access is_nullable property
                continue;
            }

            $requiredColumns[] = $col;
        }

        return $requiredColumns;
    }

    protected function postParams()
    {
        $columns = $this->columnRequired();

        $strRequest = '';
        $strResponse = '';

        if (! empty($columns)) {
            foreach ($columns as $key => $col) {
                $schemaProperty = $this->argument('name').'/properties/'.$col;
                $strRequest .= "*              @OA\Property(property=\"{$col}\", ref=\"#/components/schemas/{$schemaProperty}\"),\n";
                $strResponse .= "*              @OA\Property(property=\"{$col}\", type=\"array\", @OA\Items(example={\"{$col} field is required.\"})),\n";
            }
        }

        $data['request'] = $strRequest;
        $data['response'] = $strResponse;

        return $data;
    }

    protected function controller()
    {
        $tableName = $this->tableName(true);
        $primaryKey = $this->primaryKeyColumn($tableName);
        $postRequest = $this->postParams();
        $postParam = $postRequest['request'];
        $postResponse = $postRequest['response'];

        $controllerTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelNamePlural}}',
                '{{modelVariable}}',
                '{{routeName}}',
                '{{tableName}}',
                '{{primaryKey}}',
                '{{postParam}}',
                '{{postResponse}}',
            ],
            [
                $this->argument('name'),
                $this->plural($this->modelVariable($this->argument('name'))),
                $this->modelVariable($this->argument('name')),
                $this->routeName($this->argument('name')),
                $tableName,
                $primaryKey,
                $postParam,
                $postResponse,
            ],
            $this->getStub('controller')
        );

        $filePath = app_path("/Http/Controllers/{$this->argument('name')}Controller.php");

        if ($this->file->exists($filePath) && ! $this->option('force')) {
            if (! $this->confirm('Replace existing controller?')) {
                return;
            }
        }

        file_put_contents($filePath, $controllerTemplate);
    }

    protected function tableDetails($tableName)
    {
        return DB::select('SELECT * FROM information_schema.columns WHERE table_name = ?', [$tableName]);
    }

    protected function columns($tableName)
    {
        $columns = $this->tableDetails($tableName);

        return array_column($columns, 'column_name');
    }

    protected function modelVariable()
    {
        return Str::camel($this->argument('name'));
    }

    protected function makeFillable()
    {
        $tableName = $this->tableName(true);
        $columns = $this->tableDetails($tableName);
        $primaryKey = $this->primaryKeyColumn($tableName);
        $excludedColumns = ['created_at', 'updated_at', 'deleted_at'];
        $strFillable = '[';

        foreach ($columns as $column) {
            $col = $column->column_name;

            if (in_array($col, $excludedColumns) || $col == $primaryKey) {
                continue;
            }

            $strFillable .= "\n\t\t'{$col}',";
        }

        $strFillable .= "\n\t]";

        return $strFillable;
    }

    protected function makeColumnRules()
    {
        $tableName = $this->tableName(true);
        $columns = $this->tableDetails($tableName);
        $primaryKey = $this->primaryKeyColumn($tableName);
        $excludedColumns = ['created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at'];

        $strRules = '[';

        foreach ($columns as $column) {
            $col = $column->column_name;

            if (in_array($col, $excludedColumns) || $col === $primaryKey || (is_array($primaryKey) && in_array($col, $primaryKey))) {
                continue;
            }

            $rule = $column->is_nullable === 'YES' ? 'nullable' : 'required';
            $strRules .= "\n\t\t\t'{$col}' => ['{$rule}'],";
        }

        $strRules .= "\n\t\t]";

        return $strRules;
    }

    /**
     * Mapping data type from Schema to model properties
     *
     * @return array
     */
    public function dataTypes()
    {
        return [
            'smallint' => 'int',        // Small-range integer
            'integer' => 'int',         // Typical integer
            'bigint' => 'int',          // Large-range integer
            'decimal' => 'float',       // User-specified precision, exact
            'numeric' => 'float',       // User-specified precision, exact
            'real' => 'float',          // Variable-precision, inexact
            'double precision' => 'float', // Variable-precision, inexact
            'serial' => 'int',          // Auto-incrementing integer
            'bigserial' => 'int',       // Large auto-incrementing integer
            'boolean' => 'bool',        // Boolean value
            'char' => 'string',         // Fixed-length character string
            'varchar' => 'string',      // Variable-length character string
            'text' => 'string',         // Variable-length character string
            'bytea' => 'string',        // Binary data
            'timestamp' => '\DateTime', // Date and time (no time zone)
            'timestamptz' => '\DateTime', // Date and time (with time zone)
            'date' => '\DateTime',      // Calendar date (year, month, day)
            'time' => '\DateTime',      // Time of day (no time zone)
            'timetz' => '\DateTime',    // Time of day (with time zone)
            'interval' => '\DateInterval', // Time span
            'uuid' => 'string',         // Universally unique identifier
            'json' => 'array',          // JSON data
            'jsonb' => 'array',         // Binary JSON data
            'xml' => 'string',          // XML data
        ];
    }

    protected function columnType()
    {
        $tableName = $this->tableName();
        $columns = $this->columns(
            $this->tableName(true)
        );
        $dataTypes = $this->dataTypes();

        $columnTypes = [];

        foreach ($columns as $k => $v) {
            $columnType = Schema::getColumnType($tableName, $v);
            $columnTypes[$v] = isset($dataTypes[$columnType]) ? $dataTypes[$columnType] : 'string';
        }

        return $columnTypes;
    }

    protected function makeProperties()
    {
        $columnTypes = $this->columnType();
        $properties = '';

        foreach ($columnTypes as $col => $type) {
            $properties .= " * @property {$type} {$col}\n";
        }

        return $properties;
    }

    protected function makeParamProperties()
    {
        $columnTypes = $this->columnType();
        $properties = '';

        foreach ($columnTypes as $col => $type) {
            $properties .= " *      @OA\Property(property=\"{$col}\", type=\"{$type}\"),\n";
        }

        return $properties;
    }

    protected function model()
    {
        $fillable = $this->makeFillable();
        $paramProperties = $this->makeParamProperties();
        $properties = $this->makeProperties();

        $tableProperties = '';
        if ($this->option('table')) {
            $table = $this->tableName(true);
            $primaryKey = $this->primaryKeyColumn($table);
            $tableProperties = "protected \$table = '{$this->tableName()}';\n";
            $tableProperties .= "\tprotected \$primaryKey = '{$primaryKey}';";
        }

        $modelTemplate = str_replace(
            ['{{modelName}}', '{{tableProperties}}', '{{fillable}}', '{{paramProperties}}', '{{properties}}'],
            [$this->argument('name'), $tableProperties, $fillable, $paramProperties, $properties],
            $this->getStub('model')
        );

        $filePath = app_path("/Models/{$this->argument('name')}.php");

        if ($this->file->exists($filePath) && ! $this->option('force')) {
            if (! $this->confirm('Replace existing model?')) {
                return;
            }
        }

        file_put_contents($filePath, $modelTemplate);
    }

    protected function primaryKeyColumn($tableName)
    {
        $query = "SELECT kcu.column_name
                  FROM information_schema.table_constraints tc
                  JOIN information_schema.key_column_usage kcu ON kcu.constraint_name = tc.constraint_name AND kcu.table_name = tc.table_name
                  WHERE tc.table_name = ? AND tc.constraint_type = 'PRIMARY KEY'";

        $primaryKeyColumn = DB::selectOne($query, [$tableName]);

        if ($primaryKeyColumn) {
            return $primaryKeyColumn->column_name;
        } else {
            throw new \RuntimeException("Table '$tableName' does not have a primary key.");
        }
    }

    protected function request()
    {
        $columnRules = $this->makeColumnRules();

        $modelTemplate = str_replace(
            ['{{modelName}}', '{{columnRules}}'],
            [$this->argument('name'), $columnRules],
            $this->getStub('request')
        );

        $path = app_path('/Http/Requests');

        $path = $this->makeDirectory($path);

        $filePath = $path.DIRECTORY_SEPARATOR."Store{$this->argument('name')}Request.php";

        if ($this->file->exists($filePath) && ! $this->option('force')) {
            if (! $this->confirm('Replace existing request?')) {
                return;
            }
        }

        file_put_contents($filePath, $modelTemplate);
    }

    protected function resource()
    {
        $keyValues = $this->resourceKeyValue();

        $modelTemplate = str_replace(
            ['{{modelName}}', '{{keyValues}}'],
            [$this->argument('name'), $keyValues],
            $this->getStub('resource')
        );

        $path = app_path('/Http/Resources');

        $path = $this->makeDirectory($path);

        $filePath = $path.DIRECTORY_SEPARATOR."{$this->argument('name')}Resource.php";

        if ($this->file->exists($filePath) && ! $this->option('force')) {
            if (! $this->confirm('Replace existing resource?')) {
                return;
            }
        }

        file_put_contents($filePath, $modelTemplate);
    }

    protected function columnTobeTest()
    {
        $strFillable = "[\n";

        $tableName = $this->tableName(true);
        $columns = $this->tableDetails($tableName);
        $primaryKey = $this->primaryKeyColumn($tableName);
        $excludedColumns = ['created_at', 'updated_at', 'deleted_at'];

        foreach ($columns as $column) {
            $col = $column->column_name;

            if (in_array($col, $excludedColumns) || $col == $primaryKey) {
                continue;
            }

            $columnType = $column->data_type;
            $columnLength = $column->character_maximum_length;

            switch ($columnType) {
                case 'character varying':
                case 'text':
                    $fakerValue = $columnLength ? '$this->faker->text('.$columnLength.')' : '$this->faker->text(50)';
                    break;
                case 'integer':
                case 'bigint':
                    $fakerValue = '$this->faker->numberBetween(1, 10)';
                    break;
                case 'boolean':
                    $fakerValue = '$this->faker->randomElement([true, false])';
                    break;
                case 'timestamp without time zone':
                    $fakerValue = '$this->faker->dateTimeBetween("-1 year", "now")';
                    break;
                default:
                    $fakerValue = 'null';
            }

            $strFillable .= "\t\t\t'{$col}' => {$fakerValue},\n";
        }

        $strFillable .= "\t\t]";

        return $strFillable;
    }

    protected function factory()
    {
        $columns = $this->columnTobeTest();

        $modelTemplate = str_replace(
            ['{{modelName}}', '{{columns}}'],
            [$this->argument('name'), $columns],
            $this->getStub('factory')
        );

        $path = base_path('database/factories');

        $filePath = $path.DIRECTORY_SEPARATOR."{$this->argument('name')}Factory.php";

        if ($this->file->exists($filePath) && ! $this->option('force')) {
            if (! $this->confirm('Replace existing factory?')) {
                return;
            }
        }

        file_put_contents($filePath, $modelTemplate);
    }

    protected function test()
    {
        $routeName = $this->routeName();
        $tableName = $this->tableName(true);

        $modelTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelVariable}}',
                '{{tableName}}',
                '{{routeName}}',
                '{{primaryKey}}',
                '{{data}}',
            ],
            [
                $this->argument('name'),
                $this->modelVariable(),
                $tableName,
                $routeName,
                $this->primaryKeyColumn($tableName),
                $this->columnTobeTest(),
            ],
            $this->getStub('test')
        );

        $path = base_path('tests/Feature');

        $filePath = $path.DIRECTORY_SEPARATOR."{$this->argument('name')}Test.php";

        file_put_contents($filePath, $modelTemplate);
    }

    protected function resourceKeyValue()
    {
        $tableName = $this->tableName(true);
        $columns = $this->tableDetails($tableName);
        $strKey = "[\n";

        foreach ($columns as $column) {
            $col = $column->column_name;
            $strKey .= "\t\t\t'{$col}' => \$this->{$col},\n";
        }

        $strKey .= "\t\t]";

        return $strKey;
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string  $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (! $this->file->isDirectory($path) && ! $this->file->exists($path)) {
            $this->file->makeDirectory($path, 0777, true, true);
        }

        return $path;
    }
}
