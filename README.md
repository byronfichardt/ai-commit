# AI Git Automation

A Laravel package that automates commit message generation and PR summaries using AI (OpenAI or Claude). This package helps developers save time and maintain consistent documentation by generating meaningful commit messages and pull request descriptions.

## Installation

You can install the package via composer:

```bash
composer require byron/ai-git-automation
```

The package will automatically register itself using Laravel's package discovery.

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="Byron\AiGit\AiGitServiceProvider"
```

This will create a `config/ai-git.php` configuration file. Add the following variables to your `.env` file:

```env
# Choose your AI provider (openai or claude)
AI_PROVIDER=openai

# OpenAI Configuration
OPENAI_API_KEY=your-openai-api-key-here
OPENAI_MODEL=gpt-4-turbo-preview
OPENAI_TEMPERATURE=0.7
OPENAI_MAX_TOKENS=500

# Claude Configuration (if using Claude)
CLAUDE_API_KEY=your-claude-api-key-here
CLAUDE_MODEL=claude-3-opus-20240229
CLAUDE_TEMPERATURE=0.7
CLAUDE_MAX_TOKENS=500
```

## Usage

### Generating Commit Messages

```bash
php artisan ai:commit
```

Options:
- `--m|message`: Manually specify a commit message
- `--working-dir`: Specify the working directory

The command will:
1. Check if there are staged changes
2. Generate a commit message using AI based on the diff
3. Show you the message and ask for confirmation
4. Allow you to edit the message if needed
5. Commit the changes with the approved message

### Generating PR Summaries

```bash
php artisan ai:pr-summary [base] [head]
```

Arguments:
- `base`: Base branch (default: current branch)
- `head`: Head branch (default: current branch)

Options:
- `--main`: Generate a summary for merging to main branch
- `--copy`: Copy the summary to clipboard
- `--output`: Output file path to save the summary
- `--working-dir`: Specify the working directory

### GitHub Action Integration

To automatically generate PR descriptions, create `.github/workflows/pr-description.yml`:

```yaml
name: Generate PR Description

on:
  pull_request:
    types: [opened, reopened]
    branches: [dev, main]

jobs:
  generate-description:
    runs-on: ubuntu-latest
    if: github.event.pull_request.body == ''
    
    steps:
      - uses: actions/checkout@v3
        with:
          fetch-depth: 0
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
      
      - name: Install Dependencies
        run: composer install --no-progress --prefer-dist --optimize-autoloader
      
      - name: Generate PR Description
        env:
          AI_PROVIDER: ${{ secrets.AI_PROVIDER || 'openai' }}
          OPENAI_API_KEY: ${{ secrets.OPENAI_API_KEY }}
          CLAUDE_API_KEY: ${{ secrets.CLAUDE_API_KEY }}
        run: php artisan ai:pr-summary ${{ github.event.pull_request.base.ref }} ${{ github.event.pull_request.head.ref }} --main --output=pr-description.txt
      
      - name: Update PR Description
        uses: actions/github-script@v6
        with:
          script: |
            const fs = require('fs');
            const description = fs.readFileSync('pr-description.txt', 'utf8');
            await github.rest.pulls.update({
              owner: context.repo.owner,
              repo: context.repo.repo,
              pull_number: context.issue.number,
              body: description
            });

```

Add these secrets to your repository:
- `AI_PROVIDER`: Your chosen AI provider (openai or claude)
- `OPENAI_API_KEY`: Your OpenAI API key (if using OpenAI)
- `CLAUDE_API_KEY`: Your Claude API key (if using Claude)

## Tips for Best Results

1. Stage your changes thoughtfully to generate more focused commit messages
2. Use conventional commit message format when possible
3. Keep diffs reasonably sized for better AI analysis
4. Review and edit generated messages/summaries as needed

## Why This Matters

- **Time Savings**: Automates the often time-consuming task of writing commit messages and PR descriptions
- **Consistency**: Maintains a consistent style across your commit history
- **Quality**: Leverages AI to generate comprehensive and meaningful documentation
- **Flexibility**: Choose between OpenAI and Claude based on your preferences and needs

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## Security

If you discover any security-related issues, please email your-email@example.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
