#!/usr/bin/env sh

#
# This script is used to make it easy/possible for us to control the command that is executed when the container
# goes up, according to the environment.
#
# Heroku starts the app and assigns a random port to it. We need to make our app listen to that random port,
# so we can't just use the usual exec php command.
#

# If $PORT is not defined or empty, we set it to 8000
PORT=${PORT:-8000}

php bin/console server:run 0.0.0.0:${PORT}
