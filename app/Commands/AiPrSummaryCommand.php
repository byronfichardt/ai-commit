<?php

namespace App\Commands;

use App\Services\AIService;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class AiPrSummaryCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'ai-pr-summary 
                            {base? : Base branch (default: current branch)}
                            {head? : Head branch (default: current branch)}
                            {--main : Generate a summary for merging to main branch}
                            {--copy : Copy the summary to clipboard}
                            {--output= : Output file path to save the summary}
                            {--working-dir= : Specify the working directory}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Generate an AI-powered PR summary based on commits between branches';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Change to the specified working directory if provided
        $workingDir = $this->option('working-dir');
        if ($workingDir) {
            chdir($workingDir);
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

        // Get current branch if not specified
        $currentBranch = $this->getCurrentBranch();
        $base = $this->argument('base') ?: $currentBranch;
        $head = $this->argument('head') ?: $currentBranch;

        $this->info("Generating PR summary for changes between {$base} and {$head}...");

        try {
            // Get commits between branches
            $commits = $this->getCommitsBetweenBranches($base, $head);
            
            if (empty($commits)) {
                $this->warn("No commits found between {$base} and {$head}.");
                return 1;
            }

            // Check if this is a merge to main branch
            $isMainBranch = $this->option('main');
            
            // If merging to main, get PR information
            if ($isMainBranch) {
                $this->info('Generating summary for main branch merge...');
                // For main branch, we need to get PR information
                // This would typically involve GitHub API calls, but for simplicity
                // we'll just use the commit messages
            }

            // Generate PR summary using AI
            $ai = new AIService();
            $prSummary = $ai->generatePRSummary($commits, $isMainBranch);

            // Display the generated summary
            $this->info('Generated PR Summary:');
            $this->line("\n" . $prSummary . "\n");

            // Copy to clipboard if requested
            if ($this->option('copy')) {
                $this->copyToClipboard($prSummary);
                $this->info('Summary copied to clipboard!');
            }

            // Save to file if output path is provided
            $outputPath = $this->option('output');
            if ($outputPath) {
                file_put_contents($outputPath, $prSummary);
                $this->info("Summary saved to {$outputPath}");
            }

            return 0;
        } catch (\Exception $e) {
            $this->error('Error generating PR summary: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Check if Git is installed
     */
    private function isGitInstalled(): bool
    {
        $process = new Process(['git', '--version']);
        $process->run();

        return $process->isSuccessful();
    }

    /**
     * Check if current directory is a Git repository
     */
    private function isGitRepository(): bool
    {
        $process = new Process(['git', 'rev-parse', '--is-inside-work-tree']);
        $process->run();

        return $process->isSuccessful();
    }

    /**
     * Get the current branch name
     */
    private function getCurrentBranch(): string
    {
        $process = new Process(['git', 'rev-parse', '--abbrev-ref', 'HEAD']);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return trim($process->getOutput());
    }

    /**
     * Get commits between two branches
     */
    private function getCommitsBetweenBranches(string $base, string $head): string
    {
        // Get the commit log with details
        $process = new Process([
            'git', 'log', '--pretty=format:%h %s%n%b', "{$base}..{$head}"
        ]);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }

    /**
     * Copy text to clipboard
     */
    private function copyToClipboard(string $text): void
    {
        // Detect OS and use appropriate clipboard command
        if (PHP_OS_FAMILY === 'Darwin') {
            // macOS
            $process = new Process(['pbcopy']);
        } elseif (PHP_OS_FAMILY === 'Windows') {
            // Windows
            $process = new Process(['clip']);
        } elseif (PHP_OS_FAMILY === 'Linux') {
            // Linux (requires xclip)
            $process = new Process(['xclip', '-selection', 'clipboard']);
        } else {
            throw new \RuntimeException('Clipboard functionality not supported on this OS.');
        }

        $process->setInput($text);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }
} 