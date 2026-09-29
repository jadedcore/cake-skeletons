#!/bin/bash
# Install and run your CakePHP 5 Skeleton

GREEN="\033[0;32m";
YELLOW='\033[0;33m';
RED="\033[0;31m";
NC="\033[0m";

echo "What would you like to name this project?";
read project_name;

while ! [[ "$project_name" =~ ^[a-z0-9][a-z0-9_-]*$ ]]; do
	echo
	echo -e "${RED}Invalid project name \"$project_name\": must consist only of lowercase alphanumeric characters, hyphens, and underscores as well as start with a letter or number.${NC}";
	echo "Please try again:";
	read project_name;
done

if [ -n "$(ls -A app 2>/dev/null)" ]; then
	echo
	echo -e "Directory ${RED}app${NC} is not empty, if you continue the directory will be"\
	"overwritten. Continue (y/N)?";
	read choice;
	if [ "$choice" == "y" -o "$choice" == "Y" ]; then
		rm -rf ./app;
	else
		echo
		echo -e "${RED}Installation aborted.";
		echo
		echo
		exit 0;
	fi
fi

echo -e "${GREEN}Creating CakePHP Project...${NC}";
composer self-update;
composer create-project --prefer-dist cakephp/app:5.* app;

echo -e "${GREEN}Installing custom project configurations...${NC}";
cp -f ./contrib/phpcs.xml "./phpcs.xml";
cp -f ./contrib/AGENTS.md "./AGENTS.md";
cp -f ./contrib/README.md "./README.md";
cp -f ./contrib/pre-commit "./git/hooks/pre-commit"
rm -rf ./LICENSE;
rm -rf ./contrib;

echo -e "${GREEN}Installing docker .env...${NC}";
cp ./docker/.env.example ./docker/.env;
if [ -n "$project_name" ]; then
	sed -i.bak "s/^COMPOSE_PROJECT_NAME=.*/COMPOSE_PROJECT_NAME=${project_name}/" ./docker/.env;
	rm -f ./docker/.env.bak;
fi

echo -e "${GREEN}Checking if Docker is running...${NC}";
if ! docker info >/dev/null 2>&1; then
	echo -e "${YELLOW}Docker is not running. Starting Docker Desktop...${NC}";
	open -a Docker

	echo -e "${YELLOW}Waiting for Docker to start...${NC}";
	until docker info >/dev/null 2>&1; do
		sleep 2;
	done

	echo -e "${GREEN}Docker is running.${NC}";
fi;

echo -e "${GREEN}Starting container...${NC}";
./toolkit compose up -d
./toolkit generate-keys
./toolkit open
