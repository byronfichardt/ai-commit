# AI-Powered Git Automation

A self-contained PHP package that automates commit message generation and PR summaries using AI (OpenAI or Claude), integrated into Git and GitHub workflows.

## Features

### 1. AI-Generated Commit Messages

The tool analyzes `git diff` and generates clear, conventional commit messages using your choice of AI provider.

```bash
vendor/bin/ai-git
```

This command will:
1. Check for staged changes
2. Generate a commit message based on the diff
3. Ask for confirmation before committing

You can also specify a commit message manually:

```bash
vendor/bin/ai-git --message="Your commit message"
```

### 2. AI-Generated PR Summaries

Summarizes commit changes in pull request descriptions.

```bash
vendor/bin/ai-git ai-pr-summary base-branch head-branch
```

Options:
- `--main`: Generate a summary for merging to main branch (includes links to merged PRs)
- `--copy`: Copy the summary to clipboard
- `--output=file.md`: Save the summary to a file

### 3. GitHub Action Integration

The package includes a GitHub Action that automatically fills PR descriptions when opening a pull request to `dev` or `main`.

## Installation

### As a Project Dependency (Recommended)

1. Add the package to your project using Composer:
   ```bash
   composer require byron/ai-commit
   ```

2. Create a `.env` file in your project root (if not already present) and add your AI provider API key:
   ```bash
   # For OpenAI
   OPENAI_API_KEY=your-openai-api-key
   
   # For Claude
   CLAUDE_API_KEY=your-claude-api-key
   AI_PROVIDER=claude
   ```

That's it! The package is now installed and ready to use within your project.

### Manual Installation (For Development)

1. Clone the repository
   ```bash
   git clone https://github.com/byron/ai-commit.git
   cd ai-commit
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Set your AI provider API key:
   ```bash
   # For OpenAI
   export OPENAI_API_KEY=your-openai-api-key
   
   # For Claude
   export CLAUDE_API_KEY=your-claude-api-key
   export AI_PROVIDER=claude
   ```

## Usage

### Generating Commit Messages

1. Stage your changes using Git:
   ```bash
   git add .  # Add all changes
   # OR
   git add path/to/file  # Add specific files
   ```

2. Generate an AI-powered commit message:
   ```bash
   vendor/bin/ai-git
   ```

3. Review the suggested message. You can:
   - Accept it by typing `yes` or `y`
   - Reject it and enter your own message
   - Cancel the commit entirely

### Creating PR Summaries

#### For Feature Branch PRs

When creating a PR from a feature branch to your development branch:

```bash
# While on your feature branch
vendor/bin/ai-git ai-pr-summary development HEAD
```

This will generate a summary of all commits between your feature branch and the dev branch.

#### For Release PRs

When preparing to merge development changes to main:

```bash
# Generate a comprehensive release summary
vendor/bin/ai-git ai-pr-summary main dev --main --copy
```

The `--main` flag formats the summary as a release document, and `--copy` automatically copies it to your clipboard for easy pasting into GitHub.

#### Saving Summaries to Files

For documentation or review purposes:

```bash
vendor/bin/ai-git ai-pr-summary dev HEAD --output=pr-summary.md
```

### Using from Any Repository

The `ai-git` wrapper script allows you to run the tool from any Git repository on your system. It automatically:

1. Detects the current working directory
2. Passes it to the underlying commands
3. Ensures Git operations work correctly regardless of where you run the script from

Example workflow:
```bash
cd ~/my-project  # Your actual project repository
git add .        # Stage changes
vendor/bin/ai-git  # Generate commit message
```

### Integrating with GitHub Actions

The included GitHub Action automatically generates PR descriptions when you open a PR to `dev` or `main` branches. No manual steps required after setup!

### Tips for Best Results

- Write clear, descriptive commit messages for your individual commits
- Keep commits focused on single changes for better summaries
- For complex PRs, consider adding manual notes to the AI-generated summary

## Configuration

You can customize the behavior by setting environment variables in your project's `.env` file:

### AI Provider Selection
- `AI_PROVIDER`: Choose between 'openai' or 'claude' (default: openai)

### OpenAI Configuration
- `OPENAI_API_KEY`: Your OpenAI API key
- `OPENAI_MODEL`: The model to use (default: gpt-4o)
- `OPENAI_TEMPERATURE`: Temperature setting (default: 0.7)
- `OPENAI_MAX_TOKENS`: Maximum tokens for responses (default: 500)

### Claude Configuration
- `CLAUDE_API_KEY`: Your Anthropic Claude API key
- `CLAUDE_MODEL`: The model to use (default: claude-3-opus-20240229)
- `CLAUDE_TEMPERATURE`: Temperature setting (default: 0.7)
- `CLAUDE_MAX_TOKENS`: Maximum tokens for responses (default: 500)

### Custom Prompts
- `AI_COMMIT_MESSAGE_PROMPT`: Custom prompt for commit messages
- `AI_PR_SUMMARY_PROMPT`: Custom prompt for PR summaries
- `AI_PR_MAIN_SUMMARY_PROMPT`: Custom prompt for main branch PR summaries

## GitHub Action Setup

1. Add your AI provider API key as a repository secret:
   - `OPENAI_API_KEY` for OpenAI
   - `CLAUDE_API_KEY` for Claude
2. Optionally add `AI_PROVIDER=claude` to your repository secrets if using Claude
3. The workflow will automatically run when PRs are opened to `dev` or `main` branches

## Why This Matters

- **Saves time** for developers writing commit messages & PR summaries
- **Ensures consistency** in commit messages and PR descriptions
- **Improves documentation** by linking merged PRs in main branch updates
- **Flexible AI providers** let you choose between OpenAI and Claude based on your preferences
- **Easy to use** as a standalone package in any PHP project
- **Self-contained** - works directly within your project without system-wide installation
- **No PATH modifications needed** - runs directly from your project's vendor/bin directory

## License

This project is licensed under the MIT License.
