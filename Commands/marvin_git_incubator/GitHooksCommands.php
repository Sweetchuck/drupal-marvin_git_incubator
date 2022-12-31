<?php

declare(strict_types = 1);

namespace Drush\Commands\marvin_git_incubator;

use Drush\Commands\marvin\CommandsBase;
use Drupal\marvin_incubator\CommandsBaseTrait;
use Drupal\marvin_incubator\Robo\GitHooksTaskLoader;
use Drupal\marvin_incubator\Utils;
use Robo\Collection\CollectionBuilder;
use Robo\Contract\TaskInterface;

class GitHooksCommands extends CommandsBase {

  use CommandsBaseTrait;
  use GitHooksTaskLoader;

  /**
   * @hook on-event marvin:composer:post-install-cmd
   * @hook on-event marvin:composer:post-update-cmd
   */
  public function composerPostInstallAndUpdateCmd(): array {
    $tasks = [];
    foreach ($this->getManagedDrupalExtensions() as $extensionName => $extension) {
      $tasks["marvin.gitHooks.deploy.$extensionName"] = [
        'weight' => -200,
        'task' => $this->getTaskDeployGitHooksPackage($extension['path']),
      ];
    }

    return $tasks;
  }

  /**
   * @command marvin:git-hooks:deploy
   * @bootstrap none
   * @hidden
   *
   * @marvinArgPackages packages
   */
  public function gitHooksDeploy(array $packages): CollectionBuilder {
    return $this->getTaskGitHooksDeploy($packages);
  }

  protected function getTaskGitHooksDeploy(array $packages): CollectionBuilder {
    $managedDrupalExtensions = $this->getManagedDrupalExtensions();
    $cb = $this->collectionBuilder();
    foreach ($packages as $packageName) {
      $extension = $managedDrupalExtensions[$packageName];
      $cb->addTask($this->getTaskDeployGitHooksPackage($extension['path']));
    }

    return $cb;
  }

  protected function getTaskDeployGitHooksPackage(string $packagePath): TaskInterface {
    $config = $this->getConfig();
    $marvinIncubatorDir = Utils::marvinIncubatorDir();

    return $this
      ->taskMarvinGitHooksDeploy()
      ->setRootProjectDir($config->get('env.cwd'))
      ->setComposerExecutable($config->get('marvin.composerExecutable'))
      ->setPackagePath($packagePath)
      ->setHookFilesSourceDir("$marvinIncubatorDir/gitHooks/managedExtension")
      ->setCommonTemplateFileName("$marvinIncubatorDir/gitHooks/managedExtension/_common.php");
  }

}
