<?php

declare(strict_types=1);

namespace Rector\ComposerPlugin\PhpParser\Printer;

use PhpParser\PrettyPrinter\Standard;

final class IndentAwarePrinter extends Standard
{
    public function prettyPrintWithIndent(array $stmts, int $indentLevel): string
    {
        $this->resetState();
        $this->preprocessNodes($stmts);

        $printedStmts = $this->pStmts($stmts, true);

        return ltrim($this->handleMagicTokens($printedStmts), "\n");
    }
}
