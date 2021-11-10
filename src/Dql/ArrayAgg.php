<?php
namespace App\Dql;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;

/*
 * AarrayAgg :
 * https://www.postgresql.org/docs/11/functions-aggregate.html
 *
 * Usage : AgregateFuntions ARRAY_AGG(expression)
 */
class ArrayAgg extends FunctionNode
{
    private $expr;

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return sprintf( 'ARRAY_AGG( %s )', $this->expr->dispatch($sqlWalker) );
    }

    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->expr = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}