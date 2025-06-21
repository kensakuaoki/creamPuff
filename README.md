# creamPuff PHP Application

This project now uses a conventional PHP web application layout.

## Structure

```
public/        # Publicly accessible files
  form.php     # Form and barcode scanning page
  login.php    # User authentication
  css/         # Stylesheets
  js/          # JavaScript
config/        # Application configuration
.env.example   # Example environment variables
LICENSE
README.md
```

Authentication is handled by **AWS Cognito**. Uploaded data and scanned
information are stored in **Amazon S3**.

Install dependencies with Composer and configure the environment variables as
shown in `.env.example`.
