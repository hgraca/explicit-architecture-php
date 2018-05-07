<?php

declare(strict_types=1);

/*
 * This file is part of the Explicit Architecture POC,
 * which is created on top of the Symfony Demo application.
 *
 * (c) Herberto Graça <herberto.graca@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Acme\App\Test\TestCase\Core\Port\TemplateEngine;

use Acme\App\Core\Port\TemplateEngine\TemplateEngineInterface;
use Acme\App\Test\Framework\AbstractIntegrationTest;
use Symfony\Component\Finder\Finder;

final class AllTemplatesCalledExistIntegrationTest extends AbstractIntegrationTest
{
    private const EXCLUDE_IF_CONTAINS_CHARS = ['\'', '"', '~', '%'];
    private const REGEX = <<<'REGEXP'
/[\'\"]@(.*?\.twig)[\'\"]/
REGEXP;

    private const PATHS_TO_SEARCH = [
        'src/Presentation/Web',
    ];

    private const FILE_NAME_PATTERN_TO_SEARCH = [
        '*.twig',
        '*.php',
    ];

    /**
     * @test
     */
    public function all_templates_called_can_be_found_by_the_template_engine(): void
    {
        $templateCalls = $this->findAllTemplateCalls();
        $templateEngine = $this->getTemplateEngine();

        foreach ($templateCalls as $template) {
            $template = "@$template";
            self::assertTrue(
                $templateEngine->exists($template),
                "Template '$template' is being called in a template or controller but could not be found!"
            );
        }
    }

    private function findAllTemplateCalls()
    {
        $finder = new Finder();

        $this->limitToFileNamePatterns($finder, self::FILE_NAME_PATTERN_TO_SEARCH);
        $this->limitToFolders($finder, self::PATHS_TO_SEARCH);

        $templateCallsMatchList = [];
        $matches = [];
        foreach ($finder as $file) {
            $contents = $file->getContents();
            if ($contents) {
                preg_match_all(self::REGEX, $contents, $matches);
                if (\count($matches[1])) {
                    $templateCallsMatchList[] = array_flip($matches[1]);
                }
            }
        }

        return  array_filter(
            array_keys(array_merge(...$templateCallsMatchList)),
            function (string $templateName) {
                return !$this->stringContainsOneOf($templateName, self::EXCLUDE_IF_CONTAINS_CHARS);
            }
        );
    }

    private function stringContainsOneOf(string $haystack, array $needleList): bool
    {
        foreach ($needleList as $needle) {
            if (mb_stripos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    private function getTemplateEngine(): TemplateEngineInterface
    {
        return $this->getService(TemplateEngineInterface::class);
    }

    /**
     * @param string[] $fileNamePatternList
     */
    private function limitToFileNamePatterns(Finder $finder, array $fileNamePatternList): void
    {
        foreach ($fileNamePatternList as $pattern) {
            $finder->files()->name($pattern);
        }
    }

    /**
     * @param string[] $folderList
     */
    private function limitToFolders(Finder $finder, array $folderList): void
    {
        foreach ($folderList as $path) {
            $finder->in(self::getParameter('kernel.project_dir') . '/' . $path);
        }
    }
}
