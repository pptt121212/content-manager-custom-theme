# Content Manager Custom Theme

A WordPress theme designed specifically for content management and automation, optimized to work with the Content Auto Manager plugin.

## Overview

The Content Manager Custom Theme is a specialized WordPress theme that provides advanced functionality for managing and displaying auto-generated content. It's designed with SEO and content clustering in mind, making it ideal for websites that rely on automated content generation.

This theme is specifically designed to work as a companion theme for the Content Auto Manager plugin. It cannot function independently and must be used together with the plugin.

## Chinese Documentation / 中文文档

For Chinese documentation, please see: 
- [中文说明](README-zh.md) (Chinese README)
- [插件说明](https://github.com/pptt121212/content-auto-manager/blob/main/README.md) (Plugin README in Chinese)

## Plugin Dependency

**Required Plugin**: Content Auto Manager  
**Plugin Repository**: [Content Auto Manager Plugin](https://github.com/pptt121212/content-auto-manager)  
**Plugin Compatibility**: Version 1.0.2 or higher  

## Features

- **SEO Optimized**: Built with SEO best practices for content-rich sites
- **Responsive Design**: Fully responsive across all device sizes
- **Content Clustering**: Built-in support for content clustering and related articles
- **Custom Templates**: Specialized templates for different content types
- **Optimized Performance**: Fast loading times for content-heavy sites
- **Plugin Integration**: Seamless integration with Content Auto Manager plugin

## Installation

### Prerequisites
1. Install and activate the Content Auto Manager plugin first
2. Plugin repository: https://github.com/pptt121212/content-auto-manager

### Theme Installation
1. Download the theme ZIP file from the [Releases](https://github.com/pptt121212/content-manager-custom-theme/releases) page
2. In your WordPress admin, go to Appearance > Themes > Add New
3. Click "Upload Theme" and select the downloaded ZIP file
4. Click "Install Now"
5. Activate the theme

## Configuration

1. After activating the theme, go to Appearance > Customize
2. Navigate to Theme Options for additional settings
3. Configure your content settings to work with the Content Auto Manager plugin

## Compatibility

- Compatible with WordPress 5.0+
- Designed to work seamlessly with Content Auto Manager plugin
- Compatible with popular SEO plugins
- Works with caching plugins

## File Structure

The theme includes the following files:

```
content-manager-custom-theme/
├── 404.php              # Custom 404 error page template
├── category.php         # Category archive template
├── footer.php           # Footer section with site information
├── functions.php        # Theme functions, setup, and feature support
├── header.php           # Header section with navigation menu
├── index.php            # Main template file for blog index and archive listings
├── public_key.pem       # Public key file for API verification
├── screenshot.png       # Theme preview image displayed in admin
├── single.php           # Template for single post display
├── style.css            # Main stylesheet with theme information header
└── tag-channel.php      # Specialized tag archive template for channel pages
```

## Requirements

- WordPress 5.0 or higher
- PHP 7.0 or higher
- MySQL 5.6 or higher
- Content Auto Manager plugin (v1.0.2 or newer)
- WebP or JPEG support for images

## Changelog

### v1.0.4
- Added comprehensive README with bilingual support
- Clarified plugin dependency requirements
- Updated compatibility information

## Support

For support, please open an issue in the GitHub repository: [Issues](https://github.com/pptt121212/content-manager-custom-theme/issues)

**Note: This theme only works when used together with the Content Auto Manager plugin.**

## License

This theme is released under the GPL-2.0-or-later license.

## Contributing

We welcome contributions to improve this theme. Please fork the repository and submit a pull request with your changes.

## Important Notes

- This theme is specifically designed to work with the Content Auto Manager WordPress plugin
- **The theme cannot function independently and requires the plugin to work properly**
- Plugin repository: https://github.com/pptt121212/content-auto-manager
- For best results, use with the Content Auto Manager plugin mentioned above
- Regular updates may be required to maintain compatibility with WordPress core and the plugin
