# Test CodeMonday (PHP Laravel)

## Getting Started with the project Category Management

### Requirements

- PHP 7.4 or higher
- Composer
- MySQL
- Laravel 8.x or higher


## Installation

1. **Clone the repository:**
    - git clone https://github.com/Thitiwut6402341/Test_CodeMonday_PHP_Laravel.git
    - cd category-management-api

2. **Install Dependency**
    - composer install

3. **Set up Environment**
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_database_name
    DB_USERNAME=your_database_username
    DB_PASSWORD=your_database_password

4. **Run migrations**
    - php artisan migrate

5. **Run Serve**
    - php artisan serve


### API Endpoints:
- Create main category
    Endpoint: [POST] /category/create-standalone
    Request Body : {
        "category_name":"Subjects"
    }

API for create main category require 1 input key is category_name type of string. After create this generate
    category_id type of uuid.

- Create Sub-category
        Endpoint: [POST] /sub-category/create-leaf
        Request Body:{
                "parent_id":"uuid()" ,          // your category_id
                "category_name":"Mathematics" // your text
        }

API for create sub category require 2 input parent_id type of uuid() to indicate that data which under the category_id

- Get Standalone Category
    Endpoint: [GET] /category/get-stand-alone?category_id=xxxxxxxxxxxx

API for get data by category_id using by Param 

- Get Category Tree
    Endpoint: [GET] /category/get-tree
    Request Body:{
                "category_id":"uuid()" ,          // your category_id
        }

API for get data in tree format require category_id. can be root node or branch nod. The response data will be show
data in category from require and under this category.

- Get All Categories
    Endpoint: [GET] /category/get-all

API for get all data in the database category

- Get Categories as Array
    Endpoint: GET /category/get-array

API for get data unique category_id and parent_id in array format

- Delete Category
    Endpoint: [DELETE] /delete/category
    Request Body: {
        "category_id": "uuid()"     // your category_id
    }

API for delete category by ID

- Create Deep Tree Categories
    Endpoint: [POST] /categories/create-deep-tree
    Request Body:{
        "max_node": 10000
    }

API for create category node number of max node from require 


### Instructions for Usage:

- **Clone the repository** to your local server.
- **Install dependencies** using Composer.
- **Set up your environment variables** in the `.env` file.
- **Run the migrations** to set up the database tables.
- **Serve the application** to start the Laravel development server.
- Use the provided **API endpoints** to interact with the Category Management API.

