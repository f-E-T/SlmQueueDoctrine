#!/usr/bin/sh

rm -rf lib
rm -f composer.lock

# copy code to `lib` directory. This prevents Composer from symlinking the root directory,
# which would cause PHPUnit to crash. The `lib` directories of the integration projects are excluded, otherwise each
# project would copy the other's copy.
rsync -av --progress ../../../ lib/ --exclude=".*"  --exclude="vendor/" --exclude="/tests/integration/*/lib/"
composer install
