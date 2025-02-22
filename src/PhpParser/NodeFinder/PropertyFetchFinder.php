<?php

declare(strict_types=1);

namespace Rector\Core\PhpParser\NodeFinder;

use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Property;
use PHPStan\Type\TypeWithClassName;
use Rector\Core\Enum\ObjectReference;
use Rector\Core\PhpParser\AstResolver;
use Rector\Core\PhpParser\Node\BetterNodeFinder;
use Rector\Core\Reflection\ReflectionResolver;
use Rector\NodeNameResolver\NodeNameResolver;
use Rector\NodeTypeResolver\NodeTypeResolver;

final class PropertyFetchFinder
{
    /**
     * @var string
     */
    private const THIS = 'this';

    public function __construct(
        private readonly BetterNodeFinder $betterNodeFinder,
        private readonly NodeNameResolver $nodeNameResolver,
        private readonly ReflectionResolver $reflectionResolver,
        private readonly AstResolver $astResolver,
        private readonly NodeTypeResolver $nodeTypeResolver
    ) {
    }

    /**
     * @return PropertyFetch[]|StaticPropertyFetch[]
     */
    public function findPrivatePropertyFetches(Property | Param $propertyOrPromotedParam): array
    {
        $classLike = $this->betterNodeFinder->findParentType($propertyOrPromotedParam, ClassLike::class);
        if (! $classLike instanceof Class_) {
            return [];
        }

        $propertyName = $this->resolvePropertyName($propertyOrPromotedParam);
        if ($propertyName === null) {
            return [];
        }

        $classReflection = $this->reflectionResolver->resolveClassAndAnonymousClass($classLike);

        $nodes = [$classLike];
        $nodesTrait = $this->astResolver->parseClassReflectionTraits($classReflection);
        $hasTrait = $nodesTrait !== [];
        $nodes = array_merge($nodes, $nodesTrait);

        return $this->findPropertyFetchesInClassLike($classLike, $nodes, $propertyName, $hasTrait);
    }

    /**
     * @return PropertyFetch[]|StaticPropertyFetch[]
     */
    public function findLocalPropertyFetchesByName(Class_ $class, string $paramName): array
    {
        /** @var PropertyFetch[]|StaticPropertyFetch[] $propertyFetches */
        $propertyFetches = $this->betterNodeFinder->findInstancesOf(
            $class,
            [PropertyFetch::class, StaticPropertyFetch::class]
        );

        $foundPropertyFetches = [];

        foreach ($propertyFetches as $propertyFetch) {
            if ($propertyFetch instanceof PropertyFetch && ! $this->nodeNameResolver->isName(
                $propertyFetch->var,
                self::THIS
            )) {
                continue;
            }

            if ($propertyFetch instanceof StaticPropertyFetch && ! $this->nodeNameResolver->isName(
                $propertyFetch->class,
                ObjectReference::SELF()->getValue()
            )) {
                continue;
            }

            if (! $this->nodeNameResolver->isName($propertyFetch->name, $paramName)) {
                continue;
            }

            $foundPropertyFetches[] = $propertyFetch;
        }

        return $foundPropertyFetches;
    }

    /**
     * @param Stmt[] $stmts
     * @return PropertyFetch[]|StaticPropertyFetch[]
     */
    private function findPropertyFetchesInClassLike(
        Class_ $class,
        array $stmts,
        string $propertyName,
        bool $hasTrait
    ): array {
        /** @var PropertyFetch[] $propertyFetches */
        $propertyFetches = $this->betterNodeFinder->findInstanceOf($stmts, PropertyFetch::class);

        /** @var PropertyFetch[] $matchingPropertyFetches */
        $matchingPropertyFetches = array_filter($propertyFetches, function (PropertyFetch $propertyFetch) use (
            $propertyName,
            $class,
            $hasTrait
        ): bool {
            if ($this->isInAnonymous($propertyFetch, $class, $hasTrait)) {
                return false;
            }

            return $this->isNamePropertyNameEquals($propertyFetch, $propertyName, $class);
        });

        /** @var StaticPropertyFetch[] $staticPropertyFetches */
        $staticPropertyFetches = $this->betterNodeFinder->findInstanceOf($stmts, StaticPropertyFetch::class);

        /** @var StaticPropertyFetch[] $matchingStaticPropertyFetches */
        $matchingStaticPropertyFetches = array_filter(
            $staticPropertyFetches,
            fn (StaticPropertyFetch $staticPropertyFetch): bool => $this->isLocalStaticPropertyByFetchName(
                $staticPropertyFetch,
                $propertyName
            )
        );

        return array_merge($matchingPropertyFetches, $matchingStaticPropertyFetches);
    }

    private function isInAnonymous(PropertyFetch $propertyFetch, Class_ $class, bool $hasTrait): bool
    {
        $parent = $this->betterNodeFinder->findParentType($propertyFetch, Class_::class);
        if (! $parent instanceof Class_) {
            return false;
        }

        return $parent !== $class && ! $hasTrait;
    }

    private function isNamePropertyNameEquals(PropertyFetch $propertyFetch, string $propertyName, Class_ $class): bool
    {
        // early check if property fetch name is not equals with property name
        // so next check is check var name and var type only
        if (! $this->nodeNameResolver->isName($propertyFetch->name, $propertyName)) {
            return false;
        }

        if ($this->nodeNameResolver->isName($propertyFetch->var, self::THIS)) {
            return true;
        }

        $propertyFetchVarType = $this->nodeTypeResolver->getType($propertyFetch->var);
        if (! $propertyFetchVarType instanceof TypeWithClassName) {
            return false;
        }

        $propertyFetchVarTypeClassName = $propertyFetchVarType->getClassName();
        $classLikeName = $this->nodeNameResolver->getName($class);

        return $propertyFetchVarTypeClassName === $classLikeName;
    }

    private function resolvePropertyName(Property | Param $propertyOrPromotedParam): ?string
    {
        if ($propertyOrPromotedParam instanceof Property) {
            return $this->nodeNameResolver->getName($propertyOrPromotedParam->props[0]);
        }

        return $this->nodeNameResolver->getName($propertyOrPromotedParam->var);
    }

    private function isLocalStaticPropertyByFetchName(
        StaticPropertyFetch $staticPropertyFetch,
        string $propertyName
    ): bool {
        $class = $this->nodeNameResolver->getName($staticPropertyFetch->class);
        if (! in_array(
            $class,
            [ObjectReference::SELF()->getValue(), ObjectReference::STATIC()->getValue(), self::THIS],
            true
        )) {
            return false;
        }

        return $this->nodeNameResolver->isName($staticPropertyFetch->name, $propertyName);
    }
}
