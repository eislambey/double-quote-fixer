<?php

namespace Islambey\Fixers;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

/**
 * This file copied from SingleQuoteFixer
 * @see https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/2.16/src/Fixer/StringNotation/SingleQuoteFixer.php
 * @author Gregor Harlan <gharlan@web.de>
 */
final class DoubleQuoteFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface
{
    /**
     * {@inheritDoc}
     */
    public function getDefinition()
    {
        $codeSample = <<<'EOF'
<?php

$a = 'sample';
$b = 'sample with "double-quotes"';

EOF;
        return new FixerDefinition(
            "Convert single quotes to double quotes for all strings.",
            [
                new CodeSample($codeSample),
                new CodeSample(
                    $codeSample,
                    ["strings_containing_double_quote_chars" => true]
                ),
            ]
        );
    }

    /**
     * {@inheritDoc}
     *
     * Must run after EscapeImplicitBackslashesFixer.
     */
    public function getPriority()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_CONSTANT_ENCAPSED_STRING);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_CONSTANT_ENCAPSED_STRING)) {
                continue;
            }
            $content = $token->getContent();
            $prefix = "";

            if ("b" === strtolower($content[0])) {
                $prefix = $content[0];
                $content = substr($content, 1);
            }

            if (
                "'" === $content[0] &&
                (true === $this->configuration["strings_containing_double_quote_chars"] || false === strpos($content, "\"")) &&
                false === strpos($content, "\\$") &&
                // regex: odd number of backslashes, not followed by single quote or dollar
                !Preg::match("/(?<!\\\\)(?:\\\\{2})*\\\\(?!['$\\\\])/", $content)
            ) {
                $content = substr($content, 1, -1);
                $content = str_replace(["\'", "$", "\""], ["'", "\\$", "\\\""], $content);
                $tokens[$index] = new Token([T_CONSTANT_ENCAPSED_STRING, $prefix . "\"" . $content . "\""]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createConfigurationDefinition()
    {
        $builder = new FixerOptionBuilder(
            "strings_containing_double_quote_chars",
            "Whether to fix single-quoted strings that contains double-quotes."
        );

        $builder->setAllowedTypes(["bool"])
            ->setDefault(false);

        return new FixerConfigurationResolver([
            $builder->getOption(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return "Islambey/double_quote";
    }
}