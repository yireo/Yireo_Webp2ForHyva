# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.1] - 26 November 2026
### Added
- Support for PHP 8.5

### Removed
- Support for PHP 8.1 (PHP 8.2 to 8.5 is now required)

### Deprecated
- The module is deprecated and marked as abandoned in favour of `yireo/magento2-webp2`, because current versions of Yireo_Webp2 and Hyvä no longer need it

### Fixed
- `url_webp` was added to the wrong path of the slider GraphQL query for non-linked product sliders
- Slider query rewrites now only accept the `related`, `crosssell` and `upsell` link types
- The slider query is left untouched instead of causing an error when the query container is missing or the query cannot be parsed
- Missing dependency on Yireo_Webp2GraphQl, which defines the `url_webp` field
- Missing dependency on Magento_Store
