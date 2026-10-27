# Upgrade to 5.0

This release adds support for PHP 8.4 and 8.5 and for Doctrine DBAL 4.

## BC BREAK: The package is now `fet/queue-doctrine` and requires `fet/queue`

`slm/queue-doctrine` and `slm/queue` are no longer maintained. Version 5.0 is published as `fet/queue-doctrine` and
requires [`fet/queue`](https://github.com/f-E-T/SlmQueue) 4.0, the maintained fork of `slm/queue`. Replace the packages
in your `composer.json`:

```sh
composer remove slm/queue-doctrine slm/queue
composer require fet/queue-doctrine:^5.0
```

The `SlmQueueDoctrine\` namespace is unchanged, so no code changes are needed for the rename. `fet/queue-doctrine`
conflicts with `slm/queue-doctrine`. Also read the [`fet/queue` upgrade notes](https://github.com/f-E-T/SlmQueue/blob/master/UPGRADE.md).

## BC BREAK: Dropped support for PHP 7.4, 8.0 and 8.1

SlmQueueDoctrine now supports the PHP versions that are
[supported by the PHP project](https://www.php.net/supported-versions.php): 8.2, 8.3, 8.4 and 8.5.

## Laminas MVC: still supported, but only on PHP 8.2 to 8.4

The `SlmQueueDoctrine` module works as before; no code changes are needed.
[Laminas MVC is retiring](https://getlaminas.org/blog/2026-03-06-laminas-mvc-eol-schedule.html): it is security-only
until 31 December 2028 and does not support PHP 8.5. So in a Laminas MVC application, SlmQueueDoctrine 5 runs on PHP
8.2, 8.3 and 8.4 only.

`SlmQueueDoctrine\Module` is deprecated. The Laminas MVC integration will be removed in a future major version after
Laminas MVC's end of life. The Laminas project recommends moving to Mezzio, which uses
`SlmQueueDoctrine\ConfigProvider`.

## BC BREAK: `config/module.config.php` uses the `dependencies` key

If you include `config/module.config.php` directly, note that it now registers its services under `dependencies`
instead of `service_manager`. `SlmQueueDoctrine\Module::getConfig()` still returns them under `service_manager`, and
`SlmQueueDoctrine\ConfigProvider` under `dependencies`, as before.

## BC BREAK: Requires Doctrine DBAL 3.10 or 4.x

`doctrine/dbal` 3.10 and 4.x are the [maintained versions](https://www.doctrine-project.org/projects/dbal.html). The
minimum versions are the first releases without PHP 8.5 deprecations: `^3.10.4 || ^4.3.4`. The queue no longer uses
DBAL APIs that were removed in DBAL 4. Row locking when popping a job now uses `QueryBuilder::forUpdate()`; SQLite,
which has no row locks, is queried without it.

## BC BREAK: Removed support for DoctrineModule's `ObjectManagerAwareInterface`

`ClearObjectManagerStrategy` only clears the object manager of jobs that implement
`SlmQueueDoctrine\Persistence\ObjectManagerAwareInterface`. Jobs implementing
`DoctrineModule\Persistence\ObjectManagerAwareInterface` must implement the SlmQueueDoctrine interface instead.
DoctrineModule does not support PHP 8.5.

## Dependencies

- `doctrine/persistence` (`^3.4.1 || ^4.0`) is now a direct requirement, as
  `SlmQueueDoctrine\Persistence\ObjectManagerAwareInterface` uses it.
- `laminas/laminas-servicemanager`, `laminas/laminas-stdlib`, `psr/container` and `symfony/console` are now direct
  requirements, with the same minimum versions as `fet/queue`.
- Removed `doctrine/annotations` and `laminas/laminas-config` (both abandoned and unused) and
  `laminas/laminas-router` (unused), as well as the conflict with `doctrine/cache`.
- `data/DefaultQueue.php` now uses PHP attributes instead of annotations.

## Fixes

- `slm-queue:doctrine:recover` now prints an error and exits with code 1 when the queue is not a Doctrine queue. It
  used to fail with a `TypeError`.

# Upgrade to 4.0

This release adds support for PHP 8.0.

## BC BREAK: require `slm\queue` >= 3.0

Upgrading to `slm\queue` 3+ will require some changes, see [upgrade notes](https://github.com/JouwWeb/SlmQueue/blob/master/UPGRADE.md).

## BC BREAK: changed recover CLI command

The recover CLI can now be invoked with the following command:

```sh
vendor/bin/laminas slm-queue:doctrine:recover <queueName> [--executionTime=]
```
