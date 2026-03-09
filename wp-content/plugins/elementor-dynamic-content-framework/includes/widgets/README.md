# Widgets Directory

This directory contains Elementor widget implementations for the Dynamic Content Framework.

## Files

### class-dcf-elementor-widget.php

The main Elementor widget class that provides a universal interface for displaying dynamic content groups.

**Features:**
- Extends `\Elementor\Widget_Base`
- Provides dropdown selectors for content groups and layouts
- Renders content using the Layout Engine
- Supports live preview in Elementor editor
- Displays placeholder when no content group is selected

**Widget Details:**
- **Name:** `dcf-dynamic-content`
- **Title:** Dynamic Content
- **Icon:** `eicon-database`
- **Category:** General
- **Keywords:** dynamic, content, data, dcf, framework

**Controls:**
- Content Group selector (dropdown of active content groups)
- Layout selector (dropdown of registered layouts)

**Methods:**
- `get_name()`: Returns widget identifier
- `get_title()`: Returns widget display title
- `get_icon()`: Returns widget icon class
- `get_categories()`: Returns widget categories
- `register_controls()`: Registers widget controls
- `render()`: Renders widget output on frontend
- `content_template()`: Renders widget preview in editor

## Usage

The widget is automatically registered with Elementor through the `DCF_Loader` class. It will appear in the Elementor editor under the "General" category.

### In Elementor Editor:
1. Drag the "Dynamic Content" widget onto the page
2. Select a content group from the dropdown
3. Select a layout to display the content
4. The content will be rendered on the frontend

### Programmatic Registration:

The widget is registered via the `elementor/widgets/register` hook in `DCF_Loader::register_elementor_widgets()`.

## Requirements

- WordPress 6.0+
- Elementor 3.0+
- PHP 7.4+
- DCF Layout Engine must be initialized
- DCF Group model must be available

## Future Enhancements (Task 8.2 & 8.3)

- Dynamic layout-specific settings
- Responsive controls
- Advanced styling options
- Custom CSS classes
- Animation controls
