<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

/**
 * Cleans HTML coming from the admin rich-text editor so that only simple formatting
 * (paragraphs, line breaks, bold, italic, underline, lists) is ever stored and shown.
 */
class RichText
{
    private const ALLOWED = ['p', 'br', 'b', 'strong', 'i', 'em', 'u', 'ul', 'ol', 'li'];

    private const DROP_WITH_CONTENT = ['script', 'style', 'iframe', 'object', 'embed', 'noscript', 'template'];

    public static function clean(?string $html): ?string
    {
        if ($html === null || trim($html) === '') {
            return null;
        }

        $doc = new DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $doc->loadHTML(
            '<?xml encoding="utf-8" ?><div id="rich-text-root">' . $html . '</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();

        $root = $doc->getElementById('rich-text-root') ?? $doc->getElementsByTagName('div')->item(0);
        if (!$root) {
            return trim(strip_tags($html)) ?: null;
        }

        self::scrub($root, $doc);

        $output = '';
        foreach ($root->childNodes as $child) {
            $output .= $doc->saveHTML($child);
        }
        $output = trim($output);

        return self::plain($output) === '' && !str_contains($output, '<br') ? null : $output;
    }

    /** Text content without any markup, used for length checks. */
    public static function plain(?string $html): string
    {
        return trim(html_entity_decode(strip_tags((string) $html), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    private static function scrub(DOMNode $node, DOMDocument $doc): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child instanceof DOMElement) {
                $tag = strtolower($child->tagName);

                if (in_array($tag, self::DROP_WITH_CONTENT, true)) {
                    $node->removeChild($child);
                    continue;
                }

                self::scrub($child, $doc);

                if ($tag === 'div') {
                    $child = self::rename($child, 'p', $doc);
                    $tag = 'p';
                }

                if (!in_array($tag, self::ALLOWED, true)) {
                    while ($child->firstChild) {
                        $node->insertBefore($child->firstChild, $child);
                    }
                    $node->removeChild($child);
                    continue;
                }

                while ($child->attributes->length > 0) {
                    $child->removeAttributeNode($child->attributes->item(0));
                }
            } elseif ($child->nodeType !== XML_TEXT_NODE) {
                $node->removeChild($child);
            }
        }
    }

    private static function rename(DOMElement $element, string $name, DOMDocument $doc): DOMElement
    {
        $replacement = $doc->createElement($name);
        while ($element->firstChild) {
            $replacement->appendChild($element->firstChild);
        }
        $element->parentNode->replaceChild($replacement, $element);

        return $replacement;
    }
}
