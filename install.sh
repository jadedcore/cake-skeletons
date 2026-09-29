#!/usr/bin/env bash

set -euo pipefail

GREEN="\033[0;32m"
YELLOW="\033[0;33m"
RED="\033[0;31m"
NC="\033[0m"

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PARENT_DIR="$(dirname "$SCRIPT_DIR")"

while true; do
	read -r -p "What would you like to name this project? " project_name || exit 1
	if [[ "$project_name" =~ ^[a-z0-9][a-z0-9_-]*$ ]]; then
		break
	fi
	printf "\n${RED}Invalid project name: use lowercase letters, numbers, hyphens, and underscores, starting with a letter or number.${NC}\n"
done

PROJECT_DIR="$PARENT_DIR/$project_name"
if [[ -e "$PROJECT_DIR" || -L "$PROJECT_DIR" ]]; then
	printf "${RED}Destination already exists: %s${NC}\n" "$PROJECT_DIR"
	exit 1
fi

mkdir "$PROJECT_DIR"
mkdir "$PROJECT_DIR/docker"

printf "${GREEN}Copying project tooling...${NC}\n"
cp -R "$SCRIPT_DIR/toolkit-commands" "$PROJECT_DIR/"
cp "$SCRIPT_DIR/toolkit" "$SCRIPT_DIR/.gitignore" "$PROJECT_DIR/"
cp "$SCRIPT_DIR/docker/000-default.conf" \
	"$SCRIPT_DIR/docker/apache2.conf" \
	"$SCRIPT_DIR/docker/docker-compose.yml" \
	"$SCRIPT_DIR/docker/Dockerfile" \
	"$SCRIPT_DIR/docker/kube.yml" \
	"$SCRIPT_DIR/docker/.env.example" \
	"$SCRIPT_DIR/docker/.gitignore" \
	"$PROJECT_DIR/docker/"

printf "${GREEN}Creating CakePHP application...${NC}\n"
composer create-project --prefer-dist 'cakephp/app:5.*' "$PROJECT_DIR/app"

printf "${GREEN}Applying project configuration...${NC}\n"
cp "$SCRIPT_DIR/contrib/phpcs.xml" "$PROJECT_DIR/phpcs.xml"
cp "$SCRIPT_DIR/contrib/AGENTS.md" "$PROJECT_DIR/AGENTS.md"
cp "$SCRIPT_DIR/contrib/README.md" "$PROJECT_DIR/README.md"
cp "$PROJECT_DIR/docker/.env.example" "$PROJECT_DIR/docker/.env"

sed -i.bak "s/^COMPOSE_PROJECT_NAME=.*/COMPOSE_PROJECT_NAME=$project_name/" "$PROJECT_DIR/docker/.env"
rm -f "$PROJECT_DIR/docker/.env.bak"

sed -i.bak "s/^# Project Name$/# $project_name/" "$PROJECT_DIR/README.md"
rm -f "$PROJECT_DIR/README.md.bak"

# Keep CakePHP's generated Git metadata from becoming a nested repository.
rm -rf "$PROJECT_DIR/app/.git"
git -C "$PROJECT_DIR" init
cp "$SCRIPT_DIR/contrib/pre-commit" "$PROJECT_DIR/.git/hooks/pre-commit"
chmod +x "$PROJECT_DIR/.git/hooks/pre-commit"

printf "${GREEN}Checking if Docker is running...${NC}\n"
if ! docker info >/dev/null 2>&1; then
	printf "${YELLOW}Docker is not running. Starting Docker Desktop...${NC}\n"
	open -a Docker

	printf "${YELLOW}Waiting for Docker to start...${NC}\n"
	until docker info >/dev/null 2>&1; do
		sleep 2
	done
fi

printf "${GREEN}Starting project...${NC}\n"
(
	cd "$PROJECT_DIR"
	./toolkit compose up -d
	./toolkit generate-keys
	./toolkit open
)

printf "${GREEN}Project created at %s${NC}\n" "$PROJECT_DIR"
