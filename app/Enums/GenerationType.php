<?php

namespace App\Enums;

enum GenerationType: string
{
    case ProductDescription = 'product_description';
    case AdCopy = 'ad_copy';
    case TitleVariants = 'title_variants';
    case SeoMeta = 'seo_meta';

    public function label(): string
    {
        return match ($this) {
            self::ProductDescription => 'Product description',
            self::AdCopy => 'Ad copy',
            self::TitleVariants => 'Title variants',
            self::SeoMeta => 'SEO meta description',
        };
    }

    /**
     * Upper bound on response length. This caps thinking *and* visible output
     * together, so each value carries headroom well above the copy itself —
     * an SEO meta tag is ~160 characters but still needs room to reason.
     */
    public function maxTokens(): int
    {
        return match ($this) {
            self::ProductDescription => 4096,
            self::AdCopy => 3072,
            self::TitleVariants => 2048,
            self::SeoMeta => 1536,
        };
    }
}
