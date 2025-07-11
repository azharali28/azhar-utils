# AZHAR-UTILS
Reusable PHP utility classes for logging, validation, and path management - built by Azhar

## Installation
Clone or include in your Composer project (PSR-4 ready)

### Composer
```bash
composer require azhar/azhar-utils 
```

### Manually
```php
require_once '/path/to/azhar-utils/vendor/autoload.php'
```

## Usage
```php
use AzharUtils\Logger
```

## Initialise Git and push to GitHub
```bash
cd azhar-utils
git init
git add .
git commit -m  "Initial commit of AzharUtils package"
git remote add origin https://github.com/azharali28/azhar-utils.git
git push -u origin main
```

## Use in other projects
### Install via Composer
```bash
composer config repositories.azhar-utils vcs https://github.com/azharali28/azhar-utils.git
composer require azhar/azhar-utils:dev-main
```

### Then 
```php
require 'vendor/autoload.php';

use AzharUtils\Logger
```

