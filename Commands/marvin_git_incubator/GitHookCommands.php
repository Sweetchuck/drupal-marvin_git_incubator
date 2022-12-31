<?php

declare(strict_types = 1);

namespace Drush\Commands\marvin_git_incubator;

use Drush\Commands\marvin_git\GitHookCommandsBase;
use Robo\Collection\CollectionBuilder;

/**
 * @todo Decide that the @bootstrap "none" or "max" is required or not.
 */
class GitHookCommands extends GitHookCommandsBase {

  /**
   * @command marvin:git-hook:applypatch-msg
   * @hidden
   */
  public function gitHookApplyPatchMsg(string $packagePath, string $commitMsgFileName): CollectionBuilder {
    return $this->delegate('applypatch-msg');
  }

  /**
   * @command marvin:git-hook:commit-msg
   * @hidden
   */
  public function gitHookCommitMsg(string $packagePath, string $commitMsgFileName): CollectionBuilder {
    return $this->delegate('commit-msg');
  }

  /**
   * @command marvin:git-hook:post-applypatch
   * @hidden
   */
  public function gitHookPostApplyPatch(string $packagePath): CollectionBuilder {
    return $this->delegate('post-applypatch');
  }

  /**
   * @command marvin:git-hook:post-checkout
   * @hidden
   */
  public function gitHookPostCheckout(string $packagePath, string $refPrevious, string $refHead, bool $isBranchCheckout): CollectionBuilder {
    return $this->delegate('post-checkout');
  }

  /**
   * @command marvin:git-hook:post-commit
   * @hidden
   */
  public function gitHookPostCommit(string $packagePath): CollectionBuilder {
    return $this->delegate('post-commit');
  }

  /**
   * @command marvin:git-hook:post-merge
   * @hidden
   */
  public function gitHookPostMerge(string $packagePath, bool $isSquashMerge): CollectionBuilder {
    return $this->delegate('post-merge');
  }

  /**
   * @command marvin:git-hook:post-receive
   * @hidden
   */
  public function gitHookPostReceive(string $packagePath): CollectionBuilder {
    return $this->delegate('post-receive');
  }

  /**
   * @command marvin:git-hook:post-rewrite
   * @hidden
   */
  public function gitHookPostRewrite(string $packagePath, string $commandType): CollectionBuilder {
    return $this->delegate('post-rewrite');
  }

  /**
   * @command marvin:git-hook:post-update
   * @hidden
   */
  public function gitHookPostUpdate(string $packagePath, array $refNames): CollectionBuilder {
    return $this->delegate('post-update');
  }

  /**
   * @command marvin:git-hook:pre-applypatch
   * @hidden
   */
  public function gitHookPreApplyPatch(string $packagePath): CollectionBuilder {
    return $this->delegate('pre-applypatch');
  }

  /**
   * @command marvin:git-hook:pre-auto-gc
   * @hidden
   */
  public function gitHookPreAutoGc(string $packagePath): CollectionBuilder {
    return $this->delegate('pre-auto-gc');
  }

  /**
   * @command marvin:git-hook:pre-commit
   * @hidden
   */
  public function gitHookPreCommit(string $packagePath): CollectionBuilder {
    return $this->delegate('pre-commit');
  }

  /**
   * @command marvin:git-hook:pre-push
   * @hidden
   */
  public function gitHookPrePush(string $packagePath, string $remoteName, string $remoteUrl): CollectionBuilder {
    return $this->delegate('pre-push');
  }

  /**
   * @command marvin:git-hook:pre-rebase
   * @hidden
   */
  public function gitHookPreRebase(string $packagePath, string $upstream, ?string $branch = NULL): CollectionBuilder {
    return $this->delegate('pre-rebase');
  }

  /**
   * @command marvin:git-hook:pre-receive
   * @hidden
   */
  public function gitHookPreReceive(string $packagePath): CollectionBuilder {
    return $this->delegate('pre-receive');
  }

  /**
   * @command marvin:git-hook:prepare-commit-msg
   * @hidden
   */
  public function gitHookPrepareCommitMsg(string $packagePath, string $commitMsgFileName, string $messageSource = '', string $sha1 = ''): CollectionBuilder {
    return $this->delegate('prepare-commit-msg');
  }

  /**
   * @command marvin:git-hook:push-to-checkout
   * @hidden
   */
  public function gitHookPushToCheckout(string $packagePath, string $newCommit): CollectionBuilder {
    return $this->delegate('push-to-checkout');
  }

  /**
   * @command marvin:git-hook:update
   * @hidden
   */
  public function gitHookUpdate(string $packagePath, string $refName, string $oldObjectName, string $newObjectName): CollectionBuilder {
    return $this->delegate('update');
  }

}
