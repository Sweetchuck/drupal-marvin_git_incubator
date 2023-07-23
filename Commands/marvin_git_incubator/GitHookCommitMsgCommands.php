<?php

declare(strict_types = 1);

namespace Drush\Commands\marvin_git_incubator;

use Drupal\marvin_git\Robo\GitCommitMsgValidatorTaskLoader;
use Drush\Commands\marvin\CommandsBase;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Robo\State\Data as RoboStateData;
use Symfony\Component\Console\Input\InputInterface;

class GitHookCommitMsgCommands extends CommandsBase implements LoggerAwareInterface {

  use LoggerAwareTrait;
  use GitCommitMsgValidatorTaskLoader;

  /**
   * @hook on-event marvin:git-hook:commit-msg
   *
   * @phpstan-return array<string, marvin-task-definition>
   */
  public function gitHookCommitMsg(InputInterface $input): array {
    return [
      'marvin_git_incubator.commit-msg-validate' => [
        'weight' => -200,
        'task' => $this
          ->taskMarvinGitCommitMsgValidator()
          ->setFileName($input->getArgument('commitMsgFileName'))
          ->setRules($this->getRules()),
      ],
    ];
  }

  protected function getTaskRead(string $commitMsgFileName): \Closure {
    return function (RoboStateData $data) use ($commitMsgFileName): int {
      $content = @file_get_contents($commitMsgFileName);
      if ($content === FALSE) {
        throw new \RuntimeException(
          sprintf('Read file content from "%s" file failed', $commitMsgFileName),
          1,
        );
      }

      $data['commitMsg'] = $content;

      return 0;
    };
  }

  /**
   * @phpstan-return array<string, marvin-git-commit-msg-validator-rule>
   */
  protected function getRules(): array {
    // @todo Separated config for each managed extension.
    return array_replace_recursive(
      $this->getDefaultRules(),
      $this->getConfig()->get('command.marvin.git-hook.commit-msg.settings.rules') ?: []
    );
  }

  /**
   * @phpstan-return array<string, marvin-git-commit-msg-validator-rule>
   */
  protected function getDefaultRules(): array {
    return [
      'subjectLine' => [
        'enabled' => TRUE,
        'name' => 'subjectLine',
        'pattern' => "/^(Issue #[0-9]+ - .{5,})|(Merge( remote-tracking){0,1} branch '[^\\s]+?'(, '[^\\s]+?'){0,} into [^\\s]+?)(\\n|$)/u",
        'description' => 'Subject line contains reference to the issue number followed by a short description, or the subject line is an automatically generated message for merge commits',
        'examples' => [
          [
            'enabled' => TRUE,
            'is_valid' => TRUE,
            'description' => '',
            'example' => 'Issue #42 - Something',
          ],
          [
            'enabled' => TRUE,
            'is_valid' => TRUE,
            'description' => '',
            'example' => "Merge branch 'issue-42' into master",
          ],
          [
            'enabled' => TRUE,
            'is_valid' => TRUE,
            'description' => '',
            'example' => "Merge branch 'issue-42', 'issue-43' into master",
          ],
          [
            'enabled' => TRUE,
            'is_valid' => TRUE,
            'description' => '',
            'example' => "Merge remote-tracking branch 'issue-42' into master",
          ],
          [
            'enabled' => TRUE,
            'is_valid' => TRUE,
            'description' => '',
            'example' => "Merge remote-tracking branch 'issue-42', 'issue-43' into master",
          ],
        ],
      ],
    ];
  }

}
