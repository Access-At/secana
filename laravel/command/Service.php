<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class Service extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'make:service {name}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create a new service';

  /**
   * Execute the console command.
   */
  public function handle()
  {
    $name = $this->argument('name');

    if (!File::exists("App/Services")) {
      File::makeDirectory("App/Services", 0755, true);
    }

    $servicePath = "App/Services/{$name}Service.php";
    File::put($servicePath, $this->getBaseCode($name));

    $this->info('Service created successfully.');

    $this->call('make:controller', ['name' => "{$name}Controller"]);
    $this->call('make:repository', ['name' => "{$name}"]);
    $this->call('make:interface', ['name' => "RepositoriesInterface/{$name}RepositoryInterface"]);
  }

  private function getBaseCode($name)
  {

    return "<?php
namespace App\Services;

class {$name}Service {
  // Add your service methods here
} 
    ";
  }
}
