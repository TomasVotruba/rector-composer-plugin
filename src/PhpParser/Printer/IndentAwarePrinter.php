<?php

declare(strict_types=1);

namespace Rector\ComposerPlugin\PhpParser\Printer;

use PhpParser\Node\Stmt;
use PhpParser\PrettyPrinter\Standard;
use Webmozart\Assert\Assert;

final class IndentAwarePrinter extends Standard
{
    /**
     * @param Stmt[] $stmts
     */
    public function prettyPrintWithIndent(array $stmts): string
    {
        Assert::allIsAOf(Stmt::class, $stmts);

        $this->resetState();
        $this->preprocessNodes($stmts);

        $printedStmts = $this->pStmts($stmts, true);

        return ltrim($this->handleMagicTokens($printedStmts), "\n");
    }
}
