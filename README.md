
Built by https://www.blackbox.ai

---

# Checkout Pix - BlackCat Pagamentos

## Project Overview

Checkout Pix is a simple and user-friendly web application that facilitates payments using the Pix payment method via the BlackCat Pagamentos API. The application allows users to input their information and generate a Pix QR code along with a copy-paste code to complete their payments.

## Installation

To set up the project locally, follow these steps:

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd checkout-pix
   ```
   
2. **Set up a local server:**
   Make sure you have PHP installed to run the `pix.php` file. You can use built-in PHP server for this:
   ```bash
   php -S localhost:8000
   ```

3. **Open your web browser and navigate to:**
   ```
   http://localhost:8000/index.html
   ```

## Usage

1. Open the application in your web browser (as instructed above).
2. Fill in your details in the form fields (full name, CPF, email, phone, and quantity).
3. Click on "Gerar Pix" to generate the Pix QR code.
4. The application will display the payment QR code and a copy-paste code for your convenience.
5. Follow the instructions to complete your payment.

## Features

- User-friendly interface for entering payment information.
- Real-time calculation of total payment amount based on the quantity selected.
- Generation of a Pix QR code and a copy-paste code for easy payment.
- Basic form validation to ensure all required fields are filled in before submission.

## Dependencies

This project does not have any external dependencies listed in a `package.json` file, as it is based on straightforward HTML, CSS, and JavaScript without any frameworks or library management.

## Project Structure

Here is the structure of the project:

```
/checkout-pix
│
├── index.html      # Main HTML file for the application
├── style.css       # Styling for the application
├── script.js       # JavaScript file for interactive functionality
└── pix.php         # PHP backend to handle Pix payment requests
```

### File Descriptions

- **index.html**: Contains the HTML structure and form for the checkout process.
- **style.css**: A stylesheet that provides styling for the HTML elements for a better user experience.
- **script.js**: Adds interactive behavior to the checkout form, including total price calculations and form submission handling.
- **pix.php**: PHP script that interacts with the BlackCat Pagamentos API to create Pix payment requests and return relevant payment information back to the frontend.

---

For any issues, please refer to the documentation provided within the files or contact the project maintainer.