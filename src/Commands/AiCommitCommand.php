<?php

namespace Byron\AiGit\Commands;

use Byron\AiGit\Services\AIService;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class AiCommitCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'ai-commit {--m|message= : Manually specify a commit message} {--working-dir= : Specify the working directory}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Generate an AI-powered commit message based on your changes';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Change to the specified working directory if provided
        $workingDir = $this->option('working-dir');
        if ($workingDir) {
            if (!is_dir($workingDir)) {
                $this->error("The specified working directory does not exist: {$workingDir}");
                return 1;
            }
        }

        // Check if Git is installed
        if (!$this->isGitInstalled()) {
            $this->error('Git is not installed or not in your PATH.');
            return 1;
        }

        // Check if we're in a Git repository
        if (!$this->isGitRepository()) {
            $this->error('Not a Git repository. Please run this command in a Git repository.');
            return 1;
        }

        // Check if there are staged changes
        if (!$this->hasStagedChanges()) {
            $this->warn('No staged changes found. Please stage your changes with `git add` first.');
            return 1;
        }

        // If a message is provided, use it directly
        $message = $this->option('message');
        if ($message) {
            $this->commitWithMessage($message);
            return 0;
        }

        // Get the diff of staged changes
        $diff = $this->getStagedDiff();
        if (empty($diff)) {
            $this->warn('No changes detected in staged files.');
            return 1;
        }

        $this->info('Analyzing your changes...');

        try {
            // Generate commit message using AI
            $ai = new AIService();
            $commitMessage = $ai->generateCommitMessage($diff);

            // Show the generated message and ask for confirmation
            $this->info('Generated commit message:');
            $this->line($commitMessage);

            if ($this->confirm('Do you want to use this commit message?', true)) {
                $this->commitWithMessage($commitMessage);
                $this->info('Changes committed successfully!');
                return 0;
            }

            // If user doesn't like the message, allow them to edit it
            $editedMessage = $this->ask('Enter your preferred commit message');
            if (!empty($editedMessage)) {
                $this->commitWithMessage($editedMessage);
                $this->info('Changes committed successfully!');
                return 0;
            }

            $this->warn('Commit aborted.');
            return 1;
        } catch (\Exception $e) {
            $this->error('Error generating commit message: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Check if current directory is a Git repository
     */
    private function isGitRepository(): bool
    {
        // If using Laravel Sail, we need to check the host directory
        $workingDir = $this->option('working-dir') ?: getcwd();
        
        // Check if .git directory exists in the working directory
        if (is_dir($workingDir . '/.git')) {
            return true;
        }

        // Fallback to git command check
        $process = new Process(['git', 'rev-parse', '--is-inside-work-tree']);
        $process->setWorkingDirectory($workingDir);
        $process->run();

        return $process->isSuccessful();
    }

    /**
     * Get the diff of staged changes
     */
    private function getStagedDiff(): string
    {
        $workingDir = $this->option('working-dir') ?: getcwd();
        
        $process = new Process(['git', 'diff', '--cached']);
        $process->setWorkingDirectory($workingDir);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }

    /**
     * Check if there are staged changes
     */
    private function hasStagedChanges(): bool
    {
        $workingDir = $this->option('working-dir') ?: getcwd();
        
        $process = new Process(['git', 'diff', '--cached', '--name-only']);
        $process->setWorkingDirectory($workingDir);
        $process->run();

        return !empty(trim($process->getOutput()));
    }

    /**
     * Commit with the given message
     */
    private function commitWithMessage(string $message): void
    {
        $workingDir = $this->option('working-dir') ?: getcwd();
        
        $process = new Process(['git', 'commit', '-m', $message]);
        $process->setWorkingDirectory($workingDir);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }

    /**
     * Check if Git is installed
     */
    private function isGitInstalled(): bool
    {
        $workingDir = $this->option('working-dir') ?: getcwd();
        
        $process = new Process(['git', '--version']);
        $process->setWorkingDirectory($workingDir);
        $process->run();

        return $process->isSuccessful();
    }
} 