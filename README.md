# PHP Template Project Setup Guide

This guide will help you set up and understand the project's templating system using Twig.

## Setting Up Composer

1. **Install Composer**
   - **Windows**: 
     1. Download the Composer installer from [getcomposer.org](https://getcomposer.org/download/)
     2. Run the installer (composer-setup.exe)
     3. Follow the installation wizard
     4. Verify installation by opening Command Prompt and typing: `composer --version`

   - **Mac/Linux**:
     ```bash
     php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
     php composer-setup.php
     php -r "unlink('composer-setup.php');"
     sudo mv composer.phar /usr/local/bin/composer
     ```

2. **Install Project Dependencies**
   ```bash
   cd frontend
   composer install
   ```

## Project Structure

```
frontend/
├── templates/              # Twig template files
│   ├── base.html.twig     # Base template that others extend
│   ├── components/        # Reusable components
│   │   ├── nav.html.twig
│   │   ├── button.html.twig
│   │   ├── form-input.html.twig
│   │   └── product-card.html.twig
│   └── pages/            # Page-specific templates
│       ├── index.html.twig
│       ├── login.html.twig
│       └── register.html.twig
├── index.php             # Main application file
└── composer.json         # Composer dependencies
```

## Using Twig Templates

1. **Extending Base Template**
   ```twig
   {% extends 'base.html.twig' %}
   
   {% block title %}Your Page Title{% endblock %}
   
   {% block content %}
     Your page content here
   {% endblock %}
   ```

2. **Including Components**
   ```twig
   {# Include a navigation component #}
   {% include 'components/nav.html.twig' %}
   
   {# Include form input with parameters #}
   {% include 'components/form-input.html.twig' with {
       'name': 'username',
       'placeholder': 'Enter username',
       'required': true
   } %}
   ```

3. **Using Variables**
   ```twig
   <h1>{{ page_title }}</h1>
   
   {% if user_logged_in %}
       Welcome, {{ username }}!
   {% endif %}
   ```

## How index.php Works

The `index.php` file serves as the main entry point and handles:

1. **Setup**
   ```php
   // Load Composer dependencies
   require_once __DIR__ . '/vendor/autoload.php';
   
   // Initialize Twig
   $loader = new FilesystemLoader(__DIR__ . '/templates');
   $twig = new Environment($loader);
   ```

2. **Routing**
   - Routes URLs to appropriate templates:
     - `/` or `/index` → Home page
     - `/login` → Login page
     - `/register` → Registration page
     - `/product/{id}` → Individual product pages

3. **Data Handling**
   - Passes data to templates:
     ```php
     echo $twig->render('pages/index.html.twig', [
         'current_page' => 'home',
         'products' => $products
     ]);
     ```

4. **Error Handling**
   - Catches exceptions and displays appropriate error pages
   - Provides different error output based on environment (development/production)

## Running the Project

1. Start the PHP development server:
   ```bash
   cd frontend
   php -S localhost:3000
   ```

2. Visit [http://localhost:3000](http://localhost:3000) in your browser

## Common Template Tasks

1. **Adding a New Page**
   1. Create a new template in `templates/pages/`
   2. Extend the base template
   3. Add a route in `index.php`

2. **Creating a New Component**
   1. Add a new .html.twig file in `templates/components/`
   2. Include it in your pages using:
      ```twig
      {% include 'components/your-component.html.twig' with {
          'param1': 'value1'
      } %}
      ```

3. **Modifying Navigation**
   - Edit `templates/components/nav.html.twig`
   - Update current page highlighting in `index.php`

## Troubleshooting

1. **Cache Issues**
   - Clear the cache directory: `rm -rf frontend/cache/*`
   - Ensure cache directory is writable

2. **Template Not Found**
   - Check file paths are relative to `templates/` directory
   - Verify file extensions are `.html.twig`

3. **Composer Issues**
   - Run `composer dump-autoload`
   - Verify composer.json is valid