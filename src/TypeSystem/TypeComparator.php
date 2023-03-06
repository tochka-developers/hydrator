<?php

namespace Tochka\Hydrator\TypeSystem;

use Tochka\Hydrator\TypeSystem\Types\IntersectionType;
use Tochka\Hydrator\TypeSystem\Types\UnionType;

class TypeComparator
{
    public function compare(TypeInterface $type1, TypeInterface $type2): bool
    {
        if ($type1 instanceof TypeAliasInterface) {
            $type1 = $type1->type();
        }

        if ($type2 instanceof TypeAliasInterface) {
            $type2 = $type2->type();
        }

        if ($type1::class !== $type2::class) {
            return false;
        }

        if ($type1 instanceof UnionType || $type1 instanceof IntersectionType) {
            /** @var UnionType|IntersectionType $type2 Because $type1::class === $type2::class */
            $types1 = $type1->types;
            $types2 = $type2->types;

            if (count($types1) !== count($types2)) {
                return false;
            }

            foreach ($types1 as $type1SubType) {
                $intersectType = false;
                foreach ($types2 as $type2SubType) {
                    if ($type1SubType::class === $type2SubType::class) {
                        $result = $this->compare($type1SubType, $type2SubType);
                        if ($result === true) {
                            $intersectType = true;
                        }
                    }
                }

                if ($intersectType === false) {
                    return false;
                }
            }
        }

        return true;
    }
}
