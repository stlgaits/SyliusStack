<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Doctrine\Query\AST\Function;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;

final class Day extends FunctionNode
{
    /** @var Node|string|null */
    public $date;

    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);

        $this->date = $parser->ArithmeticPrimary();

        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        $platform = $sqlWalker->getConnection()->getDatabasePlatform();

        if ($platform instanceof MySQLPlatform) {
            return sprintf('DAY(%s)', $sqlWalker->walkArithmeticPrimary($this->date));
        }

        if ($platform instanceof PostgreSQLPlatform) {
            return sprintf('EXTRACT(DAY FROM %s)', $sqlWalker->walkArithmeticPrimary($this->date));
        }

        if ($platform instanceof SqlitePlatform) {
            return sprintf('CAST(STRFTIME("%%d", %s) AS NUMBER)', $sqlWalker->walkArithmeticPrimary($this->date));
        }

        throw new \RuntimeException(sprintf('Platform "%s" is not supported!', $platform::class));
    }
}
