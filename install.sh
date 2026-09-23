#!/bin/bash
# Install and run your CakePHP 4 Skeleton

GREEN="\033[0;32m";
RED="\033[0;31m";
NC="\033[0m";
echo "What will you name your project directory?";
read directory_name;

if [ -d $directory_name ]; then
	if [ -n "$(ls -A $directory_name 2>/dev/null)" ]; then
		echo
		echo -e "Directory ${GREEN}$directory_name${NC} is not empty, if you continue the directory will be"\
		"overwritten.  Continue (y/N)?";
		read choice;
		if [ "$choice" == "y" -o "$choice" == "Y" ]; then
			rm -rf ./$directory_name;
		else
			echo
			echo -e "${RED}Installation aborted.";
			echo
			echo
			exit 0;
		fi
	fi
fi

echo "Creating CakePHP Project...";
composer self-update;
composer create-project --prefer-dist cakephp/app:5.* $directory_name;

echo "Installing custom project configurations...";
cp -f ./contrib/phpcs.xml "./$directory_name/phpcs.xml";
cp -f ./contrib/AGENTS.md "./$directory_name/AGENTS.md";

echo "Installing docker .env...";
cp ./docker/.env.example ./docker/.env;

echo "Starting container..."
./toolkit.sh compose up -d
./toolkit.sh generate_keys
./toolkit.sh open
