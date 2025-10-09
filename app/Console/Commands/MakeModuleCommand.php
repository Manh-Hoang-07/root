<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeModuleCommand extends Command
{
    protected $signature = 'make:module {name}
        {--A|api : Generate under Api namespace}
        {--W|web : Generate under Web namespace}
        {--admin : Place in Admin sub-namespace}
        {--core : Place in Core sub-namespace}
        {--public : Place in Public sub-namespace}
        {--user : Place in User sub-namespace}';

    protected $description = 'Generate a module with Controller, Service, Repository, and Model. Supports API/Web and namespace grouping.';

    public function handle()
    {
        $name = ucfirst($this->argument('name'));

        // Determine type (Api or Web)
        $type = $this->option('api') ? 'Api' : 'Web';

        // Determine module scope (default Core)
        $scope = 'Core';
        foreach (['admin', 'core', 'public', 'user'] as $option) {
            if ($this->option($option)) {
                $scope = ucfirst($option);
                break;
            }
        }

        // Base paths
        $controllerDir = app_path("Http/Controllers/{$type}/{$scope}/{$name}");
        $serviceDir = app_path("Services/{$scope}/{$name}");
        $repoDir = app_path("Repositories/{$name}");
        $modelDir = app_path("Models");

        // Ensure directories exist
        File::ensureDirectoryExists($controllerDir);
        File::ensureDirectoryExists($serviceDir);
        File::ensureDirectoryExists($repoDir);
        File::ensureDirectoryExists($modelDir);

        // Create files
        File::put("{$controllerDir}/{$name}Controller.php", $this->controllerTemplate($name, $type, $scope));
        File::put("{$serviceDir}/{$name}Service.php", $this->serviceTemplate($name, $scope));
        File::put("{$repoDir}/{$name}Repository.php", $this->repositoryTemplate($name));
        File::put("{$modelDir}/{$name}.php", $this->modelTemplate($name));

        $this->info("✅ [{$type}/{$scope}] module [{$name}] generated successfully!");
    }

    protected function controllerTemplate($name, $type, $scope)
    {
        $baseController = $type === 'Api'
            ? "App\Http\Controllers\Api\BaseController"
            : "App\Http\Controllers\Controller";

        return <<<PHP
<?php

namespace App\Http\Controllers\\{$type}\\{$scope}\\{$name};

use {$baseController};
use App\Services\\{$scope}\\{$name}\\{$name}Service;

class {$name}Controller extends BaseController
{
    /**
     * @var {$name}Service
     */
    protected \$service;

    public function __construct({$name}Service \$service)
    {
        parent::__construct(\$service);
    }
}
PHP;
    }

    protected function serviceTemplate($name, $scope)
    {
        return <<<PHP
<?php

namespace App\Services\\{$scope}\\{$name};

use App\Services\BaseService;
use App\Repositories\\{$name}\\{$name}Repository;

class {$name}Service extends BaseService
{
    public function __construct({$name}Repository \$repo)
    {
        parent::__construct(\$repo);
    }
}
PHP;
    }

    protected function repositoryTemplate($name)
    {
        return <<<PHP
<?php

namespace App\Repositories\\{$name};

use App\Repositories\BaseRepository;
use App\Models\\{$name};

class {$name}Repository extends BaseRepository
{
    public function model()
    {
        return {$name}::class;
    }
}
PHP;
    }

    protected function modelTemplate($name)
    {
        return <<<PHP
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class {$name} extends Model
{
    protected \$fillable = [];
}
PHP;
    }

    private function lc($string)
    {
        return lcfirst($string);
    }
}
