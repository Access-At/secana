<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class Repository extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'make:repository {name}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create a new repository';

  /**
   * Execute the console command.
   */
  public function handle()
  {
    $name = $this->argument('name');

    if (!File::exists("App/Repositories")) {
      File::makeDirectory("App/Repositories", 0755, true);
    }

    $filePath = "App/Repositories/{$name}Repository.php";
    File::put($filePath, $this->getBaseCode($name));

    $this->info('Repository created successfully.');
  }

  private function getBaseCode($name)
  {
    return "<?php
namespace App\Repositories;

use App\RepositoriesInterface\\{$name}RepositoryInterface;

class {$name}Repository implements {$name}RepositoryInterface {
  // Add your repository methods here
} 
    ";
  }
}
