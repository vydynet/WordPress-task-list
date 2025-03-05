# Task List WordPress Plugin

## Description
A simple WordPress plugin that adds a custom post type for tasks and provides a shortcode `[task_list]` to display tasks.

## Features
- Custom post type "Task"
- Custom taxonomy for task status (pending, done)
- Shortcode `[task_list]` to display tasks
- Optional status filtering
- Responsive CSS styling

## Installation
1. Download the plugin ZIP file
2. Go to WordPress Admin Panel > Plugins > Add New
3. Click "Upload Plugin" and select the downloaded ZIP
4. Activate the plugin

## Usage
### Adding Tasks
1. Go to WordPress Admin > Tasks > Add New
2. Create tasks and assign status (pending/done)

### Displaying Tasks
Use the shortcode `[task_list]` in any post or page to display all tasks.

#### Filtering Tasks by Status
- Display only pending tasks: `[task_list status="pending"]`
- Display only completed tasks: `[task_list status="done"]`

## Requirements
- WordPress 5.0+
- PHP 7.2+

## License
GPL-2.0+

## Author
[Your Name]
