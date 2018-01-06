<?php
namespace fabianofa\JsonbOperatorsBundle\Doctrine\Query;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;

/**
 * jsonbvl DQL function uses the notation #>>'{path}' to retrieve the value from
 * the path given the first parameters. This function would be translated as:
 *
 * (jsonbcolumn#>>'{property1,property2}')
 *
 * The syntax to use it as DQL, using the default configuration included in
 * this bundle is:
 *
 * jsonvl(jsonbcolumn, 'property1.property2')
 *
 * For more information about jsonb operators, visit the official documentation:
 * https://www.postgresql.org/docs/current/static/functions-json.html
 */
class JsonbByPath extends FunctionNode
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

        return "(" .
        $this->column->dispatch($sqlWalker) ."#>>'{".
        $path .
        "}')";
    }
}
