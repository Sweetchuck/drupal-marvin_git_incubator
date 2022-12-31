<?php

declare(strict_types = 1);

namespace Drush\Commands\marvin_git_incubator;

use Drush\Commands\marvin\CommandsBase;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Robo\Contract\TaskInterface;
use Robo\State\Data as RoboStateData;
use Sweetchuck\Robo\String\StringTaskLoader;
use Sweetchuck\Utils\ArrayFilterInterface;
use Sweetchuck\Utils\Filter\ArrayFilterEnabled;
use Symfony\Component\Console\Input\InputInterface;

class GitHookCommitMsgCommands extends CommandsBase implements LoggerAwareInterface {

  use StringTaskLoader;
  use LoggerAwareTrait;

  /**
   * @hook on-event marvin:git-hook:commit-msg
   */
  public function gitHookCommitMsg(InputInterface $input) {
    $commitMsgFileName = $input->getArgument('commitMsgFileName');

    return [
      'marvin.git-hook.commit-msg.read' => [
        'weight' => -200,
        'task' => $this->getTaskRead($commitMsgFileName),
      ],
      'marvin.git-hook.commit-msg.sanitize' => [
        'weight' => -190,
        'task' => $this->getTaskSanitize(),
      ],
      'marvin.git-hook.commit-msg.validate' => [
        'weight' => -180,
        'task' => $this->getTaskValidate(),
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

  protected function getTaskSanitize(): TaskInterface {
    return $this
      ->taskStringUnicode()
      ->callReplaceMatches('/(^|(\r\n)|(\n\r)|\r|\n)#([^\r\n]*)|$/', '')
      ->callTrim("\n\r")
      ->deferTaskConfiguration('setString', 'commitMsg');
  }

  protected function getTaskValidate(): \Closure {
    return function (RoboStateData $data): int {
      $exitCode = 0;
      foreach ($this->getRules() as $rule) {
        if (preg_match($rule['pattern'], $data['commitMsg']) !== 1) {
          $logEntry = $this->getRuleErrorLogEntry($rule);
          $this->logger->error($logEntry['message'], $logEntry['context']);
          $exitCode = 1;
        }
      }

      return $exitCode;
    };
  }

  protected function getRules(): array {
    $rules = array_replace_recursive(
      $this->getDefaultRules(),
      $this->getConfig()->get('command.marvin.git-hook.commit-msg.settings.rules') ?: []
    );

    foreach (array_keys($rules) as $ruleName) {
      $this->applyDefaultsToRule($ruleName, $rules[$ruleName]);
    }

    return array_filter($rules, $this->getRuleFilter());
  }

  protected function applyDefaultsToRule(string $ruleName, array &$rule) {
    $rule['name'] = $ruleName;
    $rule += [
      'enabled' => TRUE,
      'description' => '- Missing -',
      'examples' => [],
    ];

    return $this;
  }

  protected function getRuleFilter(): ArrayFilterInterface {
    return new ArrayFilterEnabled();
  }

  protected function getDefaultRules(): array {
    return [
      'subjectLine' => [
        'enabled' => TRUE,
        'name' => 'subjectLine',
        'pattern' => "/^(Issue #[0-9]+ - .{5,})|(Merge( remote-tracking){0,1} branch '[^\\s]+?'(, '[^\\s]+?'){0,} into [^\\s]+?)(\\n|$)/u",
        'description' => 'Subject line contains reference to the issue number followed by a short description, or the subject line is an automatically generated message for merge commits',
        'examples' => [
          'Issue #42 - Something' => TRUE,
          "Merge branch 'issue-42' into master" => TRUE,
          "Merge branch 'issue-42', 'issue-43' into master" => TRUE,
          "Merge remote-tracking branch 'issue-42' into master" => TRUE,
          "Merge remote-tracking branch 'issue-42', 'issue-43' into master" => TRUE,
        ],
      ],
    ];
  }

  protected function getRuleErrorLogEntry(array $rule): array {
    $entry = [
      'context' => [
        'ruleName' => $rule['name'],
      ],
      'message' => [
        'Commit message validation with rule <info>{ruleName}</info> failed.',
        $rule['description'],
      ],
    ];

    $examples = array_filter($rule['examples'], new ArrayFilterEnabled());
    if ($examples) {
      $entry['message'][] = 'Valid commit message examples are:';
      $entry['message'] = array_merge($entry['message'], array_keys($rule['examples']));
    }

    $entry['message'] = implode(PHP_EOL, $entry['message']);

    return $entry;
  }

}
