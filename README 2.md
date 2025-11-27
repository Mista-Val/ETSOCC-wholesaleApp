# Wholesale Web ( Laravel Project ) 

This is a Laravel project. 

## Requirement

Below requirement must be fullfill in order to run the project successfully : 
 
- PHP version v8.2.0 or above
- Install Composer 
- Database: MySQL  
- Ext-gd must be enabled 

## Installation

1. Clone the repository:

```bash
git clone https://git.devtechnosys.tech/pramod_jangid/wholesale_pos.git
```

2. Run below commands for make directories:

```bash
cd <your-project>
mkdir storage/framework/sessions
mkdir storage/framework/cache
mkdir storage/framework/views
```

3. Install the dependencies using Composer:

```bash
composer install
```

4. Create a new database on mysql.


5. Create a copy of the `.env.example` file and rename it to `.env`. Update the configuration settings such as database credentials, mail driver, etc., in the `.env` file.

```bash
cp .env.example .env
```

6. Migrate your database:

```bash
php artisan migrate
```

7. Seed your database:

```bash
php artisan db:seed
```

8. Run below commands to link storage:

```bash
php artisan storage:link
```

9. Generate the application key:

```bash
php artisan key:generate
```

10. Make sure to provide the root permission to project folder diretories/files In Linux and Mac, you can run below command on root directory:

```bash
sudo chmod -R 777 *
```

## Configuration

You can customize the application's configuration by modifying the `.env` file. Available configuration options include:

- Database connection settings
- Mail driver and SMTP configuration
- Application URL 
- Other require variables values etc. 

## Usage 

To run the admin panel, Please follow below steps.  

- Run the Admin Panel url at browser: http://localhost/wholesale/wholesale_pos/public/admin/products
- Enter below login details to enter into admin 

    - Username : admin@mailinator.com 
    - Password : Admin@123
    
- To run the client panel url at browser: http://localhost/wholesale/wholesale_pos/public/admin/products

    - Here you can create account and login to the client and do further activities. 

## License

Devtechnosys Pvt. Ltd. Inc.
