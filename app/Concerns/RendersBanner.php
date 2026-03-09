<?php

namespace App\Concerns;

use Symfony\Component\Console\Output\OutputInterface;

trait RendersBanner
{
    public function renderBanner(OutputInterface $output): void
    {
        $lines = [
            '  ████████╗ ██╗  ██╗ ███████╗ ██████╗  ███████╗',
            '  ╚══██╔══╝ ██║  ██║ ██╔════╝ ██╔══██╗ ██╔════╝',
            '     ██║    ███████║ █████╗   ██████╔╝ █████╗  ',
            '     ██║    ██╔══██║ ██╔══╝   ██╔══██╗ ██╔══╝  ',
            '     ██║    ██║  ██║ ███████╗ ██║  ██║ ███████╗',
            '     ╚═╝    ╚═╝  ╚═╝ ╚══════╝ ╚═╝  ╚═╝ ╚══════╝',
        ];

        $gradient = [209, 203, 167, 173, 137, 131];

        $output->writeln('');

        foreach ($lines as $i => $line) {
            $output->writeln("\e[38;5;{$gradient[$i]}m{$line}\e[0m");
        }

        $output->writeln('');

        $baseUrl = env('THERE_THERE_BASE_URL', 'https://there-there.app/api');
        $host = parse_url($baseUrl, PHP_URL_HOST) ?? 'there-there.app';

        $tagline = " There There :: AI-powered helpdesk :: {$host} ";
        $output->writeln("\e[48;5;{$gradient[0]}m\e[30m\e[1m{$tagline}\e[0m");

        $output->writeln('');
    }
}
