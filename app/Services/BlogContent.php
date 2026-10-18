<?php

namespace App\Services;

use DOMDocument;
use DOMElement;
use DOMNode;
use Illuminate\Support\Str;

class BlogContent
{
    public function imageUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }
        $path = trim($path);
        if (preg_match('/[\x00-\x20\\\\]/', $path)) {
            return null;
        }
        if (str_starts_with($path, '//')) {
            $path = 'https:'.$path;
        }
        if (parse_url($path, PHP_URL_SCHEME)) {
            return in_array(strtolower((string) parse_url($path, PHP_URL_SCHEME)), ['http', 'https'], true)
                && filter_var($path, FILTER_VALIDATE_URL) ? $path : null;
        }

        return rtrim(config('services.blog.asset_url'), '/').'/'.ltrim($path, '/');
    }

    public function html(string $content): string
    {
        if ($content === '') {
            return '';
        }
        if (strip_tags($content) === $content) {
            $content = Str::markdown($content, ['html_input' => 'strip', 'allow_unsafe_links' => false]);
        }
        $document = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        try {
            $document->loadHTML('<?xml encoding="UTF-8"><body>'.$content.'</body>', LIBXML_NONET);
            $body = $document->getElementsByTagName('body')->item(0);

            return $body ? $this->children($body) : '';
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
    }

    private function children(DOMNode $node): string
    {
        $html = '';
        foreach ($node->childNodes as $child) {
            if ($child->nodeType === XML_TEXT_NODE) {
                $html .= htmlspecialchars($child->textContent, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

                continue;
            }
            if (! $child instanceof DOMElement) {
                continue;
            }
            $tag = strtolower($child->tagName);
            if (in_array($tag, ['script', 'style', 'iframe', 'object', 'embed', 'svg', 'math', 'form', 'input', 'textarea', 'button', 'template'], true)) {
                continue;
            }
            if (! in_array($tag, ['p', 'br', 'hr', 'h2', 'h3', 'h4', 'h5', 'h6', 'strong', 'b', 'em', 'i', 'u', 's', 'ul', 'ol', 'li', 'blockquote', 'pre', 'code', 'a', 'img', 'figure', 'figcaption', 'table', 'thead', 'tbody', 'tr', 'th', 'td', 'div', 'span'], true)) {
                $html .= $this->children($child);

                continue;
            }
            $attributes = '';
            if ($tag === 'a') {
                $href = trim($child->getAttribute('href'));
                if (preg_match('~^(https?://|mailto:|/[^/]|#)~i', $href) && ! preg_match('/[\x00-\x20\\\\]/', $href)) {
                    $attributes = ' href="'.htmlspecialchars($href, ENT_QUOTES, 'UTF-8').'" rel="noopener noreferrer"';
                }
            }
            if ($tag === 'img') {
                $url = $this->imageUrl($child->getAttribute('src'));
                if (! $url) {
                    continue;
                }
                $attributes = ' src="'.htmlspecialchars($url, ENT_QUOTES, 'UTF-8').'" alt="'.htmlspecialchars($child->getAttribute('alt'), ENT_QUOTES, 'UTF-8').'" loading="lazy" decoding="async"';
                $width = $child->getAttribute('width');
                $height = $child->getAttribute('height');
                if (ctype_digit($width) && ctype_digit($height) && (int) $width > 0 && (int) $height > 0
                    && (int) $width <= 8192 && (int) $height <= 8192) {
                    $attributes .= ' width="'.(int) $width.'" height="'.(int) $height.'"';
                }
            }
            $html .= '<'.$tag.$attributes.'>';
            if (! in_array($tag, ['img', 'br', 'hr'], true)) {
                $html .= $this->children($child).'</'.$tag.'>';
            }
        }

        return $html;
    }
}
