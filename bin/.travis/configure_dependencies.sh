#!/bin/sh

if [ "$KERNEL_VERSION" != "" ] ; then
    echo "> Update ezsystems/ezpublish-kernel requirement to ${KERNEL_VERSION}"
    composer require --no-update ezsystems/ezpublish-kernel="${KERNEL_VERSION}"
fi
