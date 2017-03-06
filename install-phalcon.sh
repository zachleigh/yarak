#!/usr/bin/env bash
#
# This script was taken from this project:
# https://github.com/techpivot/phalcon-ci-installer

set -e
START_TIME=$(date +%s.%3N)


# Ensure that this is being run inside a CI container
if [ "${CI}" != "true" ]; then
    echo "This script is designed to run inside a CI container only. Exiting"
    exit 1
fi


CI_APP_DIR=${PWD}
PHALCON_INSTALL_REF=${1:-master}
PHALCON_DIR=${HOME}/cphalcon
PHALCON_CACHE_DIR=${PHALCON_DIR}/cache
PHP_VER=$(phpenv version-name)
PHP_ENV_DIR=$(dirname $(dirname $(which phpenv)))/versions/${PHP_VER}
PHP_EXT_DIR=$(php-config --extension-dir)
PHP_CONF_DIR=${PHP_ENV_DIR}/etc/conf.d

if [ "${CI_NAME}" == "codeship" ]; then
    # Codeship doesn't support specifying a directory so nest the phalcon build cache inside the 
    # special dependency caching folder
    
    PHALCON_DIR=${HOME}/cache/cphalcon
    PHALCON_CACHE_DIR=${PHALCON_DIR}/cache
fi

# Prior to building, attempt to enable phalcon from a cached dependency 
# which may have been set via the CI environment YML declaration. This is
# important as it helps improve performance for fast builds.

# Note: Travis creates the folder ahead of time so it's important to explicitly
# check for the .git folder to ensure we can perform git operations.
if [ -d "${PHALCON_DIR}" ] && [ -d "${PHALCON_DIR}/.git" ]; then
    cd ${PHALCON_DIR}
    TMP_PHALCON_SAVED_MODULES_DIR=$(mktemp -d)

    # Prior to resetting the current clone, save any previously cached modules.
    if [ -d "${PHALCON_CACHE_DIR}" ]; then
        echo "Saving current cached Phalcon module(s) ..."
        for file in "${PHALCON_CACHE_DIR}"/*; do
            if [ -f ${file} ]; then
                name=${file##*/}
                echo "Found cached file: ${name} ..."
                cp ${PHALCON_CACHE_DIR}/${name} ${TMP_PHALCON_SAVED_MODULES_DIR}/${name}
            fi
        done
    fi

    # Now reset and update
    echo "Cleaning Phalcon directory ..."
    git reset --hard
    git clean --force
    
    # Note: If we are in a tag we won't be on a branch. Therefore, fail silently
    set +e
    git pull &> /dev/null
    set -e

    # Checkout specific ref    
    echo "Updating Phalcon to latest revision for ref: ${PHALCON_INSTALL_REF}"
    set +e
    git checkout ${PHALCON_INSTALL_REF} &>/dev/null

    # This could potentially fail for older versions that had a depth limiter from < 1.0.2 of the installer.
    # Handle gracefully and clean the cache automatically.
    if [ $? -ne 0 ]; then
        echo "Unable to checkout specific ref: ${PHALCON_INSTALL_REF}"
        echo "Rebuilding full Phalcon source ..."
        rm -rf ${PHALCON_DIR}
        cd ${HOME}
        git clone https://github.com/phalcon/cphalcon.git ${PHALCON_DIR}
        cd ${PHALCON_DIR}
        
        # Reset pipe to ensure we fail on second attempt with full clone
        set -e
        git checkout ${PHALCON_INSTALL_REF}
    fi
    set -e

    # Restore any cached modules
    mkdir -p ${PHALCON_CACHE_DIR}
    for file in "${TMP_PHALCON_SAVED_MODULES_DIR}"/*; do
        if [ -f ${file} ]; then
            name=${file##*/}
            echo "Restoring saved cached file: ${name} ..."
            cp ${TMP_PHALCON_SAVED_MODULES_DIR}/${name} ${PHALCON_CACHE_DIR}/${name}    
        fi
    done
    rm -rf ${TMP_PHALCON_SAVED_MODULES_DIR}

    # Debug
    PHALCON_GIT_REF=$(git rev-parse @ 2>/dev/null || true)
    echo "PHP Version: ${PHP_VER}"
    echo "Phalcon Version: ${PHALCON_GIT_REF}"
    
    # Determine if we have the cached module?
    if [ -f "${PHALCON_CACHE_DIR}/phalcon-${PHP_VER}-${PHALCON_GIT_REF:0:7}.so" ]; then
        echo -e "\u2714  Found cached module."
        echo "Enabling cached version ..."
        cp --verbose ${PHALCON_CACHE_DIR}/phalcon-${PHP_VER}-${PHALCON_GIT_REF:0:7}.so ${PHP_EXT_DIR}/phalcon.so
        echo "extension=phalcon.so" > ${PHP_CONF_DIR}/phalcon.ini
        ELAPSED_TIME=$(python -c "print round(($(date +%s.%3N) - ${START_TIME}), 3)")
        echo "Phalcon extension enabled in ${ELAPSED_TIME} sec"
        exit
    else
        echo -e "\u2716  Cache module not available."
    fi
else
    echo "No Phalcon cache available."

    # Clone the updated Phalcon source directly into the cached phalcon directory
    cd ${HOME}
    git clone https://github.com/phalcon/cphalcon.git ${PHALCON_DIR}
    
    echo "Checking out: ${PHALCON_INSTALL_REF} ..."
    cd ${PHALCON_DIR}
    git checkout ${PHALCON_INSTALL_REF}
    
    PHALCON_GIT_REF=$(git rev-parse @ 2>/dev/null || true)
fi

# Clean headers. In the event the cache is shared amongst multiple PHP CI containers
# (e.g. Travis supports multiple parallel environments using a similar cache) we
# need to remove headers from previous version.

# Clean
cd ${PHALCON_DIR}/ext
./clean
        
# Build Phalcon.
echo "Building Phalcon for ${PHP_VER} ..."

# Temporarilly using zephir for all builds. Once https://github.com/phalcon/cphalcon/issues/11961 
# gets fixed we can revert to only PHP7 using zephir.

# Various CI Providers including Codeship, CircleCI have issues when compiling Zephir 
# and require higher PHP limits even though the container has adaquete memory.
echo "memory_limit=-1" > ${PHP_CONF_DIR}/phalcon-ci-installer.ini

# if [[ $PHP_VER == 7* ]]; then

    # Note that Codeship has potential to set only the local PHP environment since this
    # option is how the environment is setup. Documentation references using `phpenv local ...`
    # Therefore, since Zephir uses global PHP, ensure we set globally.
    phpenv global ${PHP_VER}
        
    # Clean parsers
    cd ${CI_APP_DIR}/vendor/phalcon/zephir/parser
    phpize --clean
    
    # Compile
    cd ${PHALCON_DIR}
    if [[ $PHP_VER == 7* ]]; then
        ${CI_APP_DIR}/vendor/phalcon/zephir/bin/zephir compile --backend=ZendEngine3
    else 
        ${CI_APP_DIR}/vendor/phalcon/zephir/bin/zephir compile
    fi
        
    # Install
    cd ${PHALCON_DIR}/ext
    # Using debug flags (Production flags: "-O2 -fvisibility=hidden -Wparentheses -DZEPHIR_RELEASE=1")
    export CFLAGS="-g3 -O1 -std=gnu90 -Wall -DZEPHIR_RELEASE=0"
    phpize
    ./configure --enable-phalcon
    make --silent -j4
    make --silent install
# else
#     cd ${PHALCON_DIR}/build
#     ./install
# fi

# Ensure extension exists
echo "extension=phalcon.so" > ${PHP_CONF_DIR}/phalcon.ini
echo "Added phalcon PHP extension."

# Cache the executable specific to the PHP/Phalcon version combination for future builds
mkdir -p ${PHALCON_CACHE_DIR}
cp ${PHP_EXT_DIR}/phalcon.so ${PHALCON_CACHE_DIR}/phalcon-${PHP_VER}-${PHALCON_GIT_REF:0:7}.so
echo "Cached phalcon extension [ phalcon-${PHP_VER}-${PHALCON_GIT_REF:0:7}.so ] for future builds."

# Complete
ELAPSED_TIME=$(python -c "print round(($(date +%s.%3N) - ${START_TIME}), 3)")
echo "Phalcon extension compiled and installed in ${ELAPSED_TIME} sec"
