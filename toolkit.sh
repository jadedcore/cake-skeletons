#/bin/bash
# Toolkit to use docker services

cd $(dirname $0)

DOCKER_COMPOSE_EXEC="docker compose"
DOCKER_COMPOSE_LOCATION=docker/docker-compose.yml
DOCKER_COMPOSE_COMMAND="$DOCKER_COMPOSE_EXEC -f $DOCKER_COMPOSE_LOCATION"

DOCKER_EXEC=docker
DOCKER_DIRECTORY=docker/

source docker/.env

case $1 in
compose)
    # drop into shell on mariadb or app
    case $2 in
    ssh)
        if [[ $3 = "mariadb" ]]; then
            $DOCKER_COMPOSE_COMMAND exec mariadb /bin/bash
        elif [[ $3 = "app" ]]; then
            $DOCKER_COMPOSE_COMMAND exec app /bin/bash
        else
            echo "Specify \"app\" or \"mariadb\" to ssh into service"
        fi
        ;;
    # Run command on app
    exec)
        # If $3 is -T, then we run in non-interactive mode
        if [[ $3 == "-T" ]]; then
            $DOCKER_COMPOSE_COMMAND exec -T app /bin/bash -c "$(echo $@ | sed 's/.*compose exec -T//')"
        else
            $DOCKER_COMPOSE_COMMAND exec app /bin/bash -c "$(echo $@ | sed 's/.*compose exec//')"
        fi
        ;;
    # Log in to mariadb as root
    mysql)
        $DOCKER_COMPOSE_COMMAND exec mariadb /bin/bash -c "mysql --password=\$MARIADB_ROOT_PASSWORD"
        ;;
    # pipe everything else to docker-compose
    *)
        $DOCKER_COMPOSE_COMMAND $(echo "$@" | sed 's/.*compose //')
        ;;
    esac
    ;;
cake)
    $DOCKER_COMPOSE_COMMAND exec app /bin/bash -c "bin/cake $(echo $@ | sed 's/.*cake//')"
    ;;
open)
    open "http://localhost$($DOCKER_COMPOSE_COMMAND port app 80 | sed 's/0.0.0.0//')"
    ;;
unit_test)
    $DOCKER_COMPOSE_COMMAND exec app /bin/bash -c "rm -f /tmp/cake_cache/*"
    $DOCKER_COMPOSE_COMMAND exec app /bin/bash -c "./bin/rebuildUnitTestTables.script"
    if [[ $2 == "all" ]]; then
        $DOCKER_COMPOSE_COMMAND exec app /bin/bash -c "vendor/bin/phpunit --testsuite FullTestSuite"
    else
        [[ $@ == *"app/"* ]] && FILEPATH=$(echo $@ | sed 's/.*unit_test app\///') || FILEPATH=$(echo $@ | sed 's/.*unit_test //')
        $DOCKER_COMPOSE_COMMAND exec app /bin/bash -c "vendor/bin/phpunit $FILEPATH"
    fi
    ;;
# This will execute either phpcs or phpcbf
phpc*)
    [[ $@ == *"app/"* ]] && FILEPATH=$(echo $@ | sed "s/.*$1 app\///") || FILEPATH=$(echo $@ | sed "s/.*$1 //")
    $DOCKER_COMPOSE_COMMAND exec -T app /bin/bash -c "vendor/bin/$1 --standard=ruleset.xml $FILEPATH"
    ;;
generate_keys)
    $DOCKER_COMPOSE_COMMAND exec app /bin/bash -c "openssl genrsa -out config/private.key 2048"
    $DOCKER_COMPOSE_COMMAND exec app /bin/bash -c "openssl rsa -in config/private.key -pubout -out config/public.key"
    $DOCKER_COMPOSE_COMMAND exec app /bin/bash -c "chmod 660 config/private.key config/public.key"
    ;;
finish_deployment)
    $DOCKER_COMPOSE_COMMAND exec app /bin/bash -c "composer install"
    # $DOCKER_COMPOSE_COMMAND exec app /bin/bash -c "chown -R jenkins:docker ."
    $DOCKER_COMPOSE_COMMAND exec app /bin/bash -c "chmod 770 bin/*"
	./toolkit.sh pre-commit-install
    # $DOCKER_COMPOSE_COMMAND exec app /bin/bash -c "chmod 770 vendor/bin/phpunit"
    # $DOCKER_COMPOSE_COMMAND exec app /bin/bash -c "php bin/createDatabases.php"
    # $DOCKER_COMPOSE_COMMAND exec app /bin/bash -c "./bin/phinxRunMigration.script"
    ;;
pre-commit-install)
    if [ -f ".git/hooks/pre-commit" ]; then
        echo "A pre-commit hook already existed. I've moved your existing hook to pre-commit.old"
        mv .git/hooks/pre-commit .git/hooks/pre-commit.old
    fi
    cp contrib/pre-commit .git/hooks/pre-commit
	chmod 755 .git/hooks/pre-commit

    diff contrib/pre-commit .git/hooks/pre-commit 2>&1 >/dev/null
    if [ $? -eq 0 ]; then
        echo "Successfully Installed pre-commit hook."
    fi
    ;;
--help)
    echo "\033[0;31;13mHydraCor Toolkit\033[0m\n"\
    "$0 compose * - docker-compose * \n" \
    "$0 compose ssh {app, mariadb} - Drop into shell on specified service \n" \
    "$0 compose exec \"command\" - Run command on app service \n" \
    "$0 compose mysql - Drop into mysql shell on mariadb as root \n" \
    " \n" \
    "$0 open - open app in browser \n" \
    "$0 cake * - bin/cake \n" \
    "$0 unit_test {all, testpath} - Run unit tests \n" \
    "$0 phpc{bf,s} {filepath (relative to host or container)} - Run phpc{bf,s} on specified filepath \n" \
    "$0 pre-commit-install - Install pre-commit hook \n" \
    "$0 generate_keys - Generate and install public/private keys \n" \
    "$0 --help - Show this message"
    ;;
*)
    echo "Invalid option. Run $0 --help"
    ;;
esac

# Do not continue if we are not in interactive mode
[ ! -t 1 ] && exit

# Status Checks

# Check if app is running
if [[ $($DOCKER_COMPOSE_COMMAND ps | grep app | wc -l) == *"0" && $@ != "compose down" ]]; then
    echo "App is not running... Do you wish to start the app? (y/n): \c" && read answer
    [ "$answer" != "${answer#[Yy]}" ] && $DOCKER_COMPOSE_COMMAND up -d && echo "App running at http://localhost$($DOCKER_COMPOSE_COMMAND port app 80 | sed 's/0.0.0.0//')"
fi

# Check if pre-commit hook is installed
diff contrib/pre-commit .git/hooks/pre-commit 2>&1 >/dev/null
if [ $? -ne 0 ]; then
    osascript -e "display notification \"run $0 pre-commit-install\" with title \"pre-commit hook missing or modifed\""\
	2> /dev/null || \
    echo "\n\n" \
    "\033[0;31;13mpre-commit hook missing or modifed!\033[0m\n" \
    "\033[0;31;13mrun $0 pre-commit-install\033[0m\n"
fi
