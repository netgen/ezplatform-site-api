#!/bin/sh

if [ "$KERNEL_VERSION" != "" ] ; then
    echo "> Update ezsystems/ezplatform-kernel requirement to ${KERNEL_VERSION}"
    composer require --no-update ezsystems/ezplatform-kernel="${KERNEL_VERSION}"
fi
