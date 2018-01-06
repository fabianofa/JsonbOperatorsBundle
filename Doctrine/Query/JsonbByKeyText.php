<?php
namespace fabianofa\JsonbOperatorsBundle\Doctrine\Query;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;

class JsonbByKeyText extends FunctionNode
{
    public $column = null;
    public $path = null;

    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->column = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->path = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        // The rightHandSide argument is wrapped with single quotes. For
        // a valid SQL syntax, these have to be removed.
        $path = str_replace("'", "", $this->path->dispatch($sqlWalker));
        $path = explode(",", $path);
        $waypoints = array_map(function ($value) {
            if (is_numeric($value)) {
                return $value;
            }

            return "'{$value}'";
        }, $path);

        $path = implode("->>", $waypoints);

        return "(" .
        $this->column->dispatch($sqlWalker) ."->>".
        $path .
        ")";
    }
}
