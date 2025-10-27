# Content Manager Custom Theme / 内容管家辅助主题

A WordPress theme designed specifically for content management and automation, optimized to work with the Content Auto Manager plugin. / 一款专为内容管理和自动化设计的WordPress主题，与内容自动生成管家插件优化配合使用。

## Overview / 概述

The Content Manager Custom Theme is a specialized WordPress theme that provides advanced functionality for managing and displaying auto-generated content. It's designed with SEO and content clustering in mind, making it ideal for websites that rely on automated content generation.

内容管家辅助主题是一款专业的WordPress主题，为管理和展示自动生成的内容提供高级功能。该主题专为SEO和内容聚类优化设计，非常适合依赖自动内容生成的网站。

This theme is specifically designed to work as a companion theme for the Content Auto Manager plugin. It cannot function independently and must be used together with the plugin. / 本主题专为内容自动生成管家插件的配套主题而设计。无法独立使用，必须与插件配合使用。

## Plugin Dependency / 插件依赖

**Required Plugin / 必需插件**: Content Auto Manager (内容自动生成管家)  
**Plugin Repository / 插件仓库**: [Content Auto Manager Plugin](https://github.com/pptt121212/content-auto-manager)  
**Plugin Compatibility / 插件兼容性**: Version 1.0.2 or higher  

## Features / 功能特性

- **SEO Optimized**: Built with SEO best practices for content-rich sites / **SEO优化**：采用内容丰富型网站的SEO最佳实践
- **Responsive Design**: Fully responsive across all device sizes / **响应式设计**：适配各种设备尺寸
- **Content Clustering**: Built-in support for content clustering and related articles / **内容聚类**：内置内容聚类和相关文章支持
- **Custom Templates**: Specialized templates for different content types / **自定义模板**：针对不同内容类型的专门模板
- **Optimized Performance**: Fast loading times for content-heavy sites / **性能优化**：内容密集型网站的快速加载
- **Plugin Integration**: Seamless integration with Content Auto Manager plugin / **插件集成**：与内容自动生成管家插件无缝集成

## Installation / 安装说明

### Prerequisites / 前提条件
1. Install and activate the Content Auto Manager plugin first / 首先安装并激活内容自动生成管家插件
2. Plugin repository: https://github.com/pptt121212/content-auto-manager / 插件仓库：https://github.com/pptt121212/content-auto-manager

### Theme Installation / 主题安装
1. Download the theme ZIP file from the [Releases](https://github.com/pptt121212/content-manager-custom-theme/releases) page / 从[发布页面](https://github.com/pptt121212/content-manager-custom-theme/releases)下载主题ZIP文件
2. In your WordPress admin, go to Appearance > Themes > Add New / 在WordPress后台，进入 外观 > 主题 > 添加新主题
3. Click "Upload Theme" and select the downloaded ZIP file / 点击"上传主题"并选择下载的ZIP文件
4. Click "Install Now" / 点击"立即安装"
5. Activate the theme / 激活主题

## Configuration / 配置说明

1. After activating the theme, go to Appearance > Customize / 激活主题后，进入 外观 > 自定义
2. Navigate to Theme Options for additional settings / 进入主题选项进行额外设置
3. Configure your content settings to work with the Content Auto Manager plugin / 配置内容设置以配合内容自动生成管家插件使用

## Compatibility / 兼容性

- Compatible with WordPress 5.0+ / 兼容WordPress 5.0+
- Designed to work seamlessly with Content Auto Manager plugin / 专为内容自动生成管家插件优化
- Compatible with popular SEO plugins / 兼容主流SEO插件
- Works with caching plugins / 兼容缓存插件

## File Structure / 文件结构

``
content-manager-custom-theme/
├── 404.php          # Custom 404 page template / 自定义404页面模板
├── category.php     # Category archive template / 分类归档模板
├── footer.php       # Footer section / 页脚部分
├── functions.php    # Theme functions and setup / 主题功能和设置
├── header.php       # Header section with navigation / 带导航的页头部分
├── index.php        # Main blog index template / 主博客索引模板
├── public_key.pem   # Public key for verification / 验证用公钥
├── screenshot.png   # Theme preview image / 主题预览图
├── single.php       # Single post template / 单篇文章模板
├── style.css        # Main stylesheet / 主样式表
└── tag-channel.php  # Tag archive template / 标签归档模板
``

## Requirements / 系统要求

- WordPress 5.0 or higher / WordPress 5.0或更高版本
- PHP 7.0 or higher / PHP 7.0或更高版本
- MySQL 5.6 or higher / MySQL 5.6或更高版本
- Content Auto Manager plugin (v1.0.2 or newer) / 内容自动生成管家插件（v1.0.2或更新版本）
- WebP or JPEG support for images / 图像支持WebP或JPEG格式

## Changelog / 更新日志

### v1.0.4
- Added comprehensive README with bilingual support / 添加支持中英双语的详细README
- Clarified plugin dependency requirements / 明确插件依赖要求
- Updated compatibility information / 更新兼容性信息

## Support / 支持

For support, please open an issue in the GitHub repository: [Issues](https://github.com/pptt121212/content-manager-custom-theme/issues)  
如需支持，请在GitHub仓库提交问题：[Issues](https://github.com/pptt121212/content-manager-custom-theme/issues)

**Note: This theme only works when used together with the Content Auto Manager plugin.**  
**注意：此主题仅在与内容自动生成管家插件配合使用时有效。**

## License / 许可证

This theme is released under the GPL-2.0-or-later license.  
本主题基于GPL-2.0或更高版本许可证发布。

## Contributing / 贡献

We welcome contributions to improve this theme. Please fork the repository and submit a pull request with your changes.  
我们欢迎为改进此主题做出贡献。请fork此仓库并提交您的修改。

## Important Notes / 重要说明

- This theme is specifically designed to work with the Content Auto Manager WordPress plugin / 此主题专为内容自动生成管家WordPress插件设计
- **The theme cannot function independently and requires the plugin to work properly** / **此主题无法独立运行，需要插件才能正常工作**
- Plugin repository: https://github.com/pptt121212/content-auto-manager / 插件仓库：https://github.com/pptt121212/content-auto-manager
- For best results, use with the Content Auto Manager plugin mentioned above / 为获得最佳效果，请配合上述内容自动生成管家插件使用
- Regular updates may be required to maintain compatibility with WordPress core and the plugin / 可能需要定期更新以保持与WordPress核心和插件的兼容性
