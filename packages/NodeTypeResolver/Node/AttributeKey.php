<?php

declare(strict_types=1);

namespace Rector\NodeTypeResolver\Node;

use Rector\Core\PhpParser\Node\BetterNodeFinder;
use Rector\NodeNameResolver\NodeNameResolver;

/**
 * @enum
 */
final class AttributeKey
{
    /**
     * @var string
     */
    final public const VIRTUAL_NODE = 'virtual_node';

    /**
     * @var string
     */
    final public const SCOPE = 'scope';

    /**
     * @deprecated
     * @var string
     */
    final public const USE_NODES = 'useNodes';

    /**
     * @deprecated Use
     * @see BetterNodeFinder and
     * @see NodeNameResolver to find your parent nodes.
     *
     * @var string
     */
    final public const CLASS_NAME = 'className';

    /**
     * @deprecated Use
     * @see BetterNodeFinder::findParentType($node, ClassLike::class) to find your parent nodes.
     *
     * @var string
     */
    final public const CLASS_NODE = 'class_node';

    /**
     * @deprecated Use
     * @see BetterNodeFinder and
     * @see NodeNameResolver
     *
     * @var string
     */
    final public const METHOD_NAME = 'methodName';

    /**
     * @deprecated Use
     * @see BetterNodeFinder::findParentType($node, ClassMethod::class) to find your parent nodes.
     *
     * @var string
     */
    final public const METHOD_NODE = 'methodNode';

    /**
     * Internal php-parser name.
     * Do not change this even if you want!
     *
     * @var string
     */
    final public const ORIGINAL_NODE = 'origNode';

    /**
     * Internal php-parser name.
     * Do not change this even if you want!
     *
     * @var string
     */
    final public const COMMENTS = 'comments';

    /**
     * Internal php-parser name.
     * Do not change this even if you want!
     *
     * @var string
     */
    final public const ORIGINAL_NAME = 'originalName';

    /**
     * Internal php-parser name. @see \PhpParser\NodeVisitor\NameResolver
     * Do not change this even if you want!
     *
     * @var string
     */
    final public const RESOLVED_NAME = 'resolvedName';

    /**
     * @internal of php-parser, do not change
     * @see https://github.com/nikic/PHP-Parser/pull/681/files
     * @var string
     */
    final public const PARENT_NODE = 'parent';

    /**
     * @internal of php-parser, do not change
     * @see https://github.com/nikic/PHP-Parser/pull/681/files
     * @var string
     */
    final public const PREVIOUS_NODE = 'previous';

    /**
     * @internal of php-parser, do not change
     * @see https://github.com/nikic/PHP-Parser/pull/681/files
     * @var string
     */
    final public const NEXT_NODE = 'next';

    /**
     * @var string
     */
    final public const PREVIOUS_STATEMENT = 'previousExpression';

    /**
     * @var string
     */
    final public const CURRENT_STATEMENT = 'currentExpression';

    /**
     * Internal php-parser name.
     * Do not change this even if you want!
     *
     * @var string
     */
    final public const NAMESPACED_NAME = 'namespacedName';

    /**
     * Internal php-parser name.
     * Do not change this even if you want!
     *
     * @var string
     */
    final public const DOC_INDENTATION = 'docIndentation';

    /**
     * Internal php-parser name.
     * Do not change this even if you want!
     *
     * @var string
     */
    final public const START_TOKEN_POSITION = 'startTokenPos';

    /**
     * @var string
     * Use often in php-parser
     */
    final public const KIND = 'kind';

    /**
     * @var string
     */
    final public const IS_UNREACHABLE = 'isUnreachable';

    /**
     * @var string
     */
    final public const PHP_DOC_INFO = 'php_doc_info';

    /**
     * @var string
     */
    final public const IS_REGULAR_PATTERN = 'is_regular_pattern';

    /**
     * @var string
     */
    final public const DO_NOT_CHANGE = 'do_not_change';

    /**
     * @var string
     */
    final public const PARAMETER_POSITION = 'parameter_position';

    /**
     * @var string
     */
    final public const ARGUMENT_POSITION = 'argument_position';

    /**
     * @var string
     */
    final public const FUNC_ARGS_TRAILING_COMMA = 'trailing_comma';

    /**
     * Contains current file object
     * @see \Rector\Core\ValueObject\Application\File
     *
     * @var string
     */
    final public const FILE = 'file';

    /**
     * In case the php-doc info just changed, for reporting of changed nodes
     * @var string
     */
    final public const HAS_PHP_DOC_INFO_JUST_CHANGED = 'has_php_doc_info_just_changed';

    /**
     * Helps with infinite loop detection
     * @var string
     */
    final public const CREATED_BY_RULE = 'created_by_rule';

    /**
     * Provided by PHPStan parser, depth in sense of nesting
     * @var string
     */
    final public const STATEMENT_DEPTH = 'statementDepth';

    /**
     * @var string
     */
    final public const HAS_NEW_INHERITED_TYPE = 'has_new_inherited_type';

    /**
     * @var string
     */
    final public const WRAPPED_IN_PARENTHESES = 'wrapped_in_parentheses';

    /**
     * @var string
     */
    final public const COMMENT_CLOSURE_RETURN_MIRRORED = 'comment_closure_return_mirrored';
}
