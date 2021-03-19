<?php

declare(strict_types=1);

namespace Rector\DowngradePhp80\Tests\Rector\FunctionLike\DowngradeParamMixedTypeDeclarationRector;

use Iterator;
use Rector\DowngradePhp80\Rector\FunctionLike\DowngradeParamMixedTypeDeclarationRector;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class DowngradeParamMixedTypeDeclarationRectorTest extends AbstractRectorTestCase
{
    /**
     * @requires PHP 8.0
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fileInfo): void
    {
        $this->doTestFileInfo($fileInfo);
    }

    public function provideData(): Iterator
    {
        return $this->yieldFilesFromDirectory(__DIR__ . '/Fixture');
    }

    protected function getRectorClass(): string
    {
        return DowngradeParamMixedTypeDeclarationRector::class;
    }
}