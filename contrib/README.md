# Project Name

Brief description of the project.

## Getting Started

### Requirements

- Docker Desktop
- Git

### Installation

The project was generated from the CakePHP skeleton repository and initialized with Git. Start the development environment with:

```bash
./toolkit compose up -d
./toolkit open
```

### Development Environment

Start the application:

```bash
./toolkit compose up -d
```

Open the application in your browser:

```bash
./toolkit open
```

Stop the application:

```bash
./toolkit compose down
```

## Toolkit

The project includes a command-line toolkit for common development tasks.

View available commands:

```bash
./toolkit
```

Examples:

```bash
./toolkit cake <command>
./toolkit composer <command>
./toolkit phpcs
./toolkit test
```

## Project Structure

```text
app/                CakePHP application
docker/             Docker configuration
toolkit-commands/   Development toolkit commands
AGENTS.md            AI development instructions
```

## Configuration

Local Docker configuration is stored in:

```text
docker/.env
```

Use `docker/.env.example` as the template when creating a new environment.

## Development

Before committing changes, run the project's tests and code-quality checks:

```bash
./toolkit phpcs
./toolkit test
```

See `AGENTS.md` for additional development conventions and instructions.
