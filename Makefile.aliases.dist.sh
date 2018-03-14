#!/usr/bin/env bash

# To add your personal aliases, create a new file called Makefile.custom.sh, with something like:

##### Makefile.custom.sh
# #!/usr/bin/env bash
#
# . Makefile.aliases.dist.sh > /dev/null 2>&1
#
# function bar {
#     echo 'bar function'
# }
#
# call_or_show_help $1
#
#####

# Instead of function bar, create your own functions and call them with make as `make bar`

function command_exists {
    COMMAND="$1"

    declare -f ${COMMAND} > /dev/null

    if [ $? -eq 0 ]; then
        echo 1
    else
        echo 0
    fi
}

function call_or_show_help {
    MAKE_TARGET="${1}"

    MAKE_WAS_CALLED_WITH_EMPTY_TARGET="[ '${MAKE_TARGET}' == 'default' ]"
    if ${MAKE_WAS_CALLED_WITH_EMPTY_TARGET}; then
        show_help
        exit 0
    fi

    BASH_FUNCTION_DOES_NOT_EXIST="[ '$(command_exists ${MAKE_TARGET})' == '0' ]"
    if ${BASH_FUNCTION_DOES_NOT_EXIST}; then
        echo "Command '${MAKE_TARGET}' not found."
        show_help
        exit 2
    fi

    $("${MAKE_TARGET}")
}

function show_help {
    make help
    echo "Available aliases and custom commands:"
    IFS=$'\n'
    for f in $(declare -F); do
        if [ "${f:11}" != "command_exists" ] && [ "${f:11}" != "call_or_show_help" ] && [ "${f:11}" != "show_help" ]; then
            echo " - ${f:11}"
        fi
    done
}

# If this script is being run, as opposed to sourced/included in another script, then:
if [[ $_ == $0 ]]; then
    call_or_show_help "$1"
fi
