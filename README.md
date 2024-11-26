jempecms
========

Jempe is an easy PHP content management system derived from Codeigniter php framework. 

## Features

- **Simple Routing**: Easily map URI requests to specific controller functions with customizable routes.
- **Admin Interface**: A dedicated admin interface for managing content, files, and configurations.
- **Content Publishing**: Options to convert content to HTML and manage sitemaps.
- **File Management**: Integrated file manager for handling uploads and ensuring unique file names.

## Installation

1. Clone the Repository
```sh
git clone https://github.com/jempe/jempecms.git
cd jempecms
```

2. Set Up Database
- Create a new database for the CMS.
- Update the database configuration in `application/config/database.php`.

3. Run the Installer
   - Navigate to `/admin/install` to run the installation script and create the database tables.
  
## Configuration

**Routes**: The routing configuration can be found in `application/config/routes.php`.

```php
$route['default_controller'] = "jempe";
$route['404_override'] = '';

$route['sitemap.xml'] = "admin/sitemap";
$route['jempe_uploader.xml'] = "admin/jempe_uploader";
$route['jempe_is_unique.xml'] = "admin/jempe_is_unique";

$route['admin/install'] = "install/index";
$route['admin/install/create_db'] = "install/create_db";

$route['admin/tohtml/'] = "publish/tohtml";
$route['admin/tohtml/:any'] = "publish/tohtml";

$route['admin'] = "admin/index";
$route['admin/([a-z_]+)'] = "admin/$1";
$route['admin/file_manager'] = "admin/image_manager";
$route['admin/([a-z_]+).xml'] = "admin/$1";
$route['admin/([a-z_]+).js'] = "admin/$1";
$route['admin/([a-z_]+)/:any'] = "admin/$1";

$route[':any'] = "jempe/index";
```

## Usage
- **Admin Panel**: Access the admin panel at /admin to manage content and settings.
- **Content Publishing**: Use the admin interface to publish content and generate static HTML files.

